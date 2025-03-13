<?php

namespace App\Services;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MissingArticlesService
{
    private $client;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->client = $mercadoLibreService->getHttpClient();
    }

    public function syncMissingArticles($mlAccountId, $token)
    {
        try {
            // Obtener ml_product_id que están en ordenes pero no en articulos
            $missingIds = DB::table('ordenes')
                ->where('ml_account_id', $mlAccountId)
                ->whereNotIn('ml_product_id', DB::table('articulos')->select('ml_product_id'))
                ->pluck('ml_product_id')
                ->unique()
                ->toArray();

            if (empty($missingIds)) {
                return ['success' => true, 'message' => 'No hay artículos faltantes para sincronizar.', 'count' => 0];
            }

            \Log::info("Sincronizando artículos faltantes para {$mlAccountId}", [
                'count' => count($missingIds),
                'sample' => array_slice($missingIds, 0, 5)
            ]);

            // Descargar detalles de los ítems faltantes
            $chunks = array_chunk($missingIds, 20);
            $allItems = [];

            foreach ($chunks as $chunk) {
                $response = $this->client->get("items", [
                    'headers' => ['Authorization' => "Bearer {$token}"],
                    'query' => ['ids' => implode(',', $chunk)]
                ]);

                $details = json_decode($response->getBody(), true);
                $allItems = array_merge($allItems, $details);
                sleep(1); // Evitar rate limiting
            }

            // Insertar o actualizar en articulos
            foreach ($allItems as $item) {
                $body = $item['body'] ?? [];
                $precio = $body['price'] ?? null;
                $precioOriginal = $body['original_price'] ?? null;
                $enPromocion = $precioOriginal && $precio && $precioOriginal > $precio;
                $descuentoPorcentaje = $enPromocion ? round((($precioOriginal - $precio) / $precioOriginal) * 100, 2) : null;

                \App\Models\Articulo::updateOrInsert(
                    ['ml_product_id' => $body['id']],
                    [
                        'user_id' => $mlAccountId,
                        'titulo' => $body['title'] ?? 'Sin título',
                        'imagen' => $body['thumbnail'] ?? null,
                        'stock_actual' => $body['available_quantity'] ?? 0,
                        'precio' => $precio,
                        'estado' => $body['status'] ?? 'Desconocido',
                        'permalink' => $body['permalink'] ?? '#',
                        'condicion' => $body['condition'] ?? 'Desconocido',
                        'sku' => $body['user_product_id'] ?? null,
                        'tipo_publicacion' => $body['listing_type_id'] ?? 'Desconocido',
                        'en_catalogo' => $body['catalog_listing'] ?? false,
                        'logistic_type' => $body['shipping']['logistic_type'] ?? null,
                        'inventory_id' => $body['inventory_id'] ?? null,
                        'user_product_id' => $body['user_product_id'] ?? null,
                        'precio_original' => $precioOriginal,
                        'category_id' => $body['category_id'] ?? null,
                        'en_promocion' => $enPromocion,
                        'descuento_porcentaje' => $descuentoPorcentaje,
                        'deal_ids' => json_encode($body['deal_ids'] ?? []),
                    ]
                );
            }

            \Log::info("Sincronización de artículos faltantes completada", ['count' => count($allItems)]);
            return [
                'success' => true,
                'message' => "Sincronización completada. Se agregaron " . count($allItems) . " artículos faltantes.",
                'count' => count($allItems)
            ];
        } catch (\Exception $e) {
            \Log::error("Error al sincronizar artículos faltantes para {$mlAccountId}: " . $e->getMessage());
            return ['success' => false, 'message' => "Error al sincronizar: " . $e->getMessage(), 'count' => 0];
        }
    }
}
