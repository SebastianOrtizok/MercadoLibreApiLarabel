<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Articulo;
use App\Services\MercadoLibreService;

class StockVentaService
{
    protected $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function syncStockFromSales($fullDay = false)
    {
        $dateFrom = $fullDay ? Carbon::today()->startOfDay() : Carbon::now()->subHour();
        $ventas = DB::table('ordenes')
            ->where('fecha_venta', '>=', $dateFrom)
            ->select('ml_product_id', 'ml_account_id')
            ->distinct()
            ->get();

        Log::info("Artículos vendidos encontrados desde " . $dateFrom->toDateTimeString() . ": " . $ventas->count());
        Log::info("IDs de productos vendidos: " . $ventas->pluck('ml_product_id')->toJson());

        if ($ventas->isEmpty()) {
            Log::info("No hay ventas recientes para sincronizar.");
            return;
        }

        $articulos = Articulo::whereIn('ml_product_id', $ventas->pluck('ml_product_id'))
            ->where('estado', 'active') // Cambié status por estado como en StockSyncJob
            ->get();

        Log::info("Artículos a sincronizar: " . $articulos->count());
        Log::info("Artículos encontrados: " . $articulos->pluck('ml_product_id')->toJson());

        if ($articulos->isEmpty()) {
            Log::warning("No se encontraron artículos activos para los ml_product_id de ventas.");
            return;
        }

        $userId = auth()->id();

        foreach ($articulos as $index => $articulo) {
            $mlAccountId = $ventas->firstWhere('ml_product_id', $articulo->ml_product_id)->ml_account_id;
            Log::info("Procesando artículo #" . ($index + 1) . ": {$articulo->ml_product_id}");

            $accessToken = $this->getMercadoLibreToken($userId, $mlAccountId);
            $stockFulfillment = 0;
            $stockDeposito = 0;

            if ($articulo->user_product_id) {
                try {
                    Log::info("Haciendo request para {$articulo->user_product_id} con token: " . substr($accessToken, 0, 10) . "...");
                    $response = Http::withToken($accessToken)
                        ->timeout(10)
                        ->get("https://api.mercadolibre.com/user-products/{$articulo->user_product_id}/stock");

                    if ($response->status() === 401) {
                        Log::warning("Token vencido o rechazado en artículo #" . ($index + 1) . ", refrescando...");
                        $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);
                        Log::info("Nuevo token obtenido: " . ($accessToken ? substr($accessToken, 0, 10) . "..." : 'VACÍO'));
                        if (!$accessToken) {
                            Log::error("No se pudo refrescar el token para ml_account_id {$mlAccountId}");
                            continue;
                        }
                        Log::info("Reintentando con token: " . substr($accessToken, 0, 10) . "...");
                        $response = Http::withToken($accessToken)
                            ->timeout(10)
                            ->get("https://api.mercadolibre.com/user-products/{$articulo->user_product_id}/stock");
                    }

                    Log::info("Respuesta API para {$articulo->ml_product_id}", [
                        'status' => $response->status(),
                        'data' => $response->json()
                    ]);

                    if ($response->successful()) {
                        $stockData = $response->json();
                        $locations = $stockData['locations'] ?? [];

                        foreach ($locations as $loc) {
                            $type = $loc['type'] ?? 'unknown';
                            $quantity = $loc['quantity'] ?? 0;

                            if ($quantity > 0) {
                                if (in_array($type, ['meli_facility', 'distribution_center'])) {
                                    $stockFulfillment += $quantity;
                                } elseif (in_array($type, ['selling_address', 'warehouse', 'default'])) {
                                    $stockDeposito += $quantity;
                                }
                            }
                        }
                    } else {
                        Log::warning("Fallo API para {$articulo->ml_product_id}", [
                            'status' => $response->status(),
                            'response' => $response->body()
                        ]);
                        continue;
                    }
                } catch (\Exception $e) {
                    Log::error("Error API para {$articulo->ml_product_id}: " . $e->getMessage());
                    continue;
                }
            } else {
                $stockActual = $articulo->stock_actual ?? 0;
                if ($articulo->logistic_type === 'fulfillment') {
                    $stockFulfillment = $stockActual;
                } else {
                    $stockDeposito = $stockActual;
                }
                Log::info("Sin user_product_id, replicando stock_actual para {$articulo->ml_product_id}", [
                    'stock_actual' => $stockActual,
                    'logistic_type' => $articulo->logistic_type
                ]);
            }

            try {
                $updated = $articulo->update([
                    'stock_fulfillment' => $stockFulfillment,
                    'stock_deposito' => $stockDeposito,
                    'updated_at' => now(),
                ]);
                Log::info("Stock actualizado para {$articulo->ml_product_id}", [
                    'success' => $updated,
                    'fulfillment' => $stockFulfillment,
                    'deposito' => $stockDeposito,
                    'stock_actual' => $articulo->stock_actual
                ]);
            } catch (\Exception $e) {
                Log::error("Error al guardar {$articulo->ml_product_id}: " . $e->getMessage());
            }

            usleep(100000);
        }

        Log::info("Sincronización de stock por ventas terminada");
    }

    private function getMercadoLibreToken($userId, $mlAccountId)
    {
        return $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);
    }
}
