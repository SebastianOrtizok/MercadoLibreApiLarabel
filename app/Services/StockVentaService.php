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
        $dateFrom = $hourly ? now()->subHours(2)->startOfHour() : now()->subDay()->startOfDay();
        $dateTo = $hourly ? now()->subHours(2)->endOfHour() : now()->subDay()->endOfDay();

        Log::info("Buscando ventas desde: {$dateFrom} hasta: {$dateTo}");

        $ventas = DB::table('ordenes')
            ->where('fecha_venta', '>=', $dateFrom)
            ->where('fecha_venta', '<=', $dateTo)
            ->where('estado_orden', 'paid')
            ->select('ml_product_id', 'ml_account_id')
            ->distinct()
            ->get();

        Log::info("Artículos vendidos encontrados (" . ($hourly ? 'hora' : 'día') . "): " . $ventas->count());

        if ($ventas->isEmpty()) {
            Log::info("No hay ventas recientes para sincronizar.");
            return;
        }

        $articulos = Articulo::whereIn('ml_product_id', $ventas->pluck('ml_product_id'))
            ->where('estado', 'active')
            ->get();

        Log::info("Artículos a sincronizar: " . $articulos->count());

        if ($articulos->isEmpty()) {
            Log::warning("No se encontraron artículos activos para los ml_product_id de ventas.");
            return;
        }

        // Paso 1: Actualizar stock_actual en lotes de 20 desde /items
        $mlAccountIds = $ventas->pluck('ml_account_id')->unique()->all();
        Log::info("Cuentas a procesar: " . json_encode($mlAccountIds));

        foreach ($mlAccountIds as $mlAccountId) {
            $tokenRecord = DB::table('mercadolibre_tokens')
                ->where('ml_account_id', $mlAccountId)
                ->first();

            if (!$tokenRecord) {
                Log::error("No hay token registrado para ml_account_id: {$mlAccountId}");
                continue;
            }

            $userId = $tokenRecord->user_id;
            $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);

            $productIds = $ventas->where('ml_account_id', $mlAccountId)->pluck('ml_product_id')->all();
            if (empty($productIds)) {
                Log::info("No hay productos para sincronizar en ml_account_id: {$mlAccountId}");
                continue;
            }

            $chunks = array_chunk($productIds, 20);
            foreach ($chunks as $chunk) {
                $idsString = implode(',', $chunk);
                Log::info("Consultando /items para ml_account_id {$mlAccountId} con IDs: {$idsString}");

                try {
                    $response = Http::withToken($accessToken)
                        ->timeout(10)
                        ->get("https://api.mercadolibre.com/items", [
                            'ids' => $idsString
                        ]);

                    if ($response->successful()) {
                        $itemsData = $response->json();
                        if (!is_array($itemsData)) {
                            Log::warning("Respuesta de /items no es un array para {$mlAccountId}");
                            continue;
                        }

                        foreach ($itemsData as $item) {
                            if (isset($item['code']) && $item['code'] == 200) {
                                $mlProductId = $item['body']['id'];
                                $stockActual = $item['body']['available_quantity'] ?? 0;
                                $articulo = $articulos->firstWhere('ml_product_id', $mlProductId);
                                if ($articulo) {
                                    $articulo->update(['stock_actual' => $stockActual]);
                                    Log::info("Stock actual consultado y actualizado para {$mlProductId}: stock_actual = {$stockActual}");
                                } else {
                                    Log::warning("Artículo no encontrado en la base para {$mlProductId}");
                                }
                            }
                        }
                    } else {
                        Log::warning("Fallo al consultar /items para {$mlAccountId}", [
                            'status' => $response->status()
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Error al consultar /items para {$mlAccountId}: " . $e->getMessage());
                }
            }
        }

        // Paso 2: Actualizar stock_fulfillment y stock_deposito por artículo
        foreach ($articulos as $index => $articulo) {
            $venta = $ventas->firstWhere('ml_product_id', $articulo->ml_product_id);
            $mlAccountId = $venta->ml_account_id;
            Log::info("Procesando artículo #" . ($index + 1) . ": {$articulo->ml_product_id} con ml_account_id: {$mlAccountId}");

            $tokenRecord = DB::table('mercadolibre_tokens')
                ->where('ml_account_id', $mlAccountId)
                ->first();

            if (!$tokenRecord) {
                Log::error("No hay token registrado para ml_account_id: {$mlAccountId}");
                continue;
            }

            $userId = $tokenRecord->user_id;
            $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);
            $stockFulfillment = 0;
            $stockDeposito = 0;

            if ($articulo->user_product_id) {
                try {
                    $response = Http::withToken($accessToken)
                        ->timeout(10)
                        ->get("https://api.mercadolibre.com/user-products/{$articulo->user_product_id}/stock");

                    if ($response->status() === 401) {
                        Log::warning("Token vencido o rechazado en artículo #" . ($index + 1) . ", refrescando...");
                        $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);
                        if (!$accessToken) {
                            Log::error("No se pudo refrescar el token para ml_account_id {$mlAccountId}");
                            continue;
                        }
                        $response = Http::withToken($accessToken)
                            ->timeout(10)
                            ->get("https://api.mercadolibre.com/user-products/{$articulo->user_product_id}/stock");
                    }

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

                        $updated = $articulo->update([
                            'stock_fulfillment' => $stockFulfillment,
                            'stock_deposito' => $stockDeposito,
                            'updated_at' => now(),
                        ]);
                        Log::info("Stock actualizado para {$articulo->ml_product_id}", [
                            'success' => $updated,
                            'stock_actual' => $articulo->stock_actual,
                            'fulfillment' => $stockFulfillment,
                            'deposito' => $stockDeposito
                        ]);
                    } else {
                        Log::warning("Fallo API para {$articulo->ml_product_id}", [
                            'status' => $response->status()
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Error API para {$articulo->ml_product_id}: " . $e->getMessage());
                }
            } else {
                Log::info("Sin user_product_id, dejando stock_fulfillment y stock_deposito en 0 para {$articulo->ml_product_id}");
            }

            usleep(100000); // Pausa para evitar saturar la API
        }

        Log::info("Sincronización de stock por ventas terminada");
    }
}
