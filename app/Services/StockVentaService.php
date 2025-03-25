<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Articulo;
use App\Services\MercadoLibreService;
use Carbon\Carbon;

class StockVentaService
{
    protected $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function syncStockFromSales($hourly = false)
    {
        $dateFrom = $hourly ? now()->subHour()->startOfHour() : now()->subDay()->startOfDay();
        $dateTo = $hourly ? now()->subHour()->endOfHour() : now()->subDay()->endOfDay();

        Log::info("Buscando ventas desde: {$dateFrom} hasta: {$dateTo}");

        $ventas = DB::table('ordenes')
            ->where('fecha_venta', '>=', $dateFrom)
            ->where('fecha_venta', '<=', $dateTo)
            ->where('estado_orden', 'paid')
            ->select('ml_product_id', 'ml_account_id')
            ->distinct()
            ->get();

        Log::info("Artículos vendidos encontrados (" . ($hourly ? 'hora' : 'día') . "): " . $ventas->count());
        Log::info("IDs de productos vendidos: " . $ventas->pluck('ml_product_id')->toJson());

        if ($ventas->isEmpty()) {
            Log::info("No hay ventas recientes para sincronizar.");
            return;
        }

        $articulos = Articulo::whereIn('ml_product_id', $ventas->pluck('ml_product_id'))
            ->where('estado', 'active')
            ->get();

        Log::info("Artículos a sincronizar: " . $articulos->count());
        Log::info("Artículos encontrados: " . $articulos->pluck('ml_product_id')->toJson());

        if ($articulos->isEmpty()) {
            Log::warning("No se encontraron artículos activos para los ml_product_id de ventas.");
            return;
        }

        foreach ($articulos as $index => $articulo) {
            $venta = $ventas->firstWhere('ml_product_id', $articulo->ml_product_id);
            $mlAccountId = $venta->ml_account_id;
            Log::info("Procesando artículo #" . ($index + 1) . ": {$articulo->ml_product_id} con ml_account_id: {$mlAccountId}");

            // Buscar el user_id asociado al ml_account_id en mercadolibre_tokens
            $tokenRecord = DB::table('mercadolibre_tokens')
                ->where('ml_account_id', $mlAccountId)
                ->first();

            if (!$tokenRecord) {
                Log::error("No hay token registrado para ml_account_id: {$mlAccountId}");
                continue;
            }

            $userId = $tokenRecord->user_id;
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
                Log::info("Sin user_product_id, dejando stock en 0 para {$articulo->ml_product_id}");
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
                ]);
            } catch (\Exception $e) {
                Log::error("Error al guardar {$articulo->ml_product_id}: " . $e->getMessage());
            }

            usleep(100000);
        }

        Log::info("Sincronización de stock por ventas terminada");
    }
}
