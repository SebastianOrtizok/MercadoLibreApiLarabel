<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\MercadoLibreService;
use App\Models\Articulo;

class ArticuloSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $itemIds;
    protected $accessToken;
    protected $userId;
    protected $mlAccountId;
    protected $mercadoLibreService;

    public function __construct(array $itemIds, string $accessToken, string $userId, string $mlAccountId)
    {
        $this->itemIds = $itemIds;
        $this->accessToken = $accessToken;
        $this->userId = $userId;
        $this->mlAccountId = $mlAccountId;
        $this->mercadoLibreService = app(MercadoLibreService::class); // Inyectamos el servicio
    }

    public function handle()
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->get('https://api.mercadolibre.com/items', ['ids' => implode(',', $this->itemIds)]);

            if ($response->failed()) {
                if ($response->status() === 401) {
                    $this->accessToken = $this->mercadoLibreService->getAccessToken($this->userId, $this->mlAccountId);
                    $response = Http::withToken($this->accessToken)
                        ->get('https://api.mercadolibre.com/items', ['ids' => implode(',', $this->itemIds)]);
                }
                if ($response->failed()) {
                    throw new \Exception("Error al obtener detalles: " . $response->body());
                }
            }

            $details = $response->json();
            foreach ($details as $item) {
                $body = $item['body'] ?? [];
                $precio = $body['price'] ?? null;
                $precioOriginal = $body['original_price'] ?? null;
                $enPromocion = $precioOriginal && $precio && $precioOriginal > $precio;
                $descuentoPorcentaje = $enPromocion ? round((($precioOriginal - $precio) / $precioOriginal) * 100, 2) : null;

                Articulo::updateOrInsert(
                    ['ml_product_id' => $body['id']],
                    [
                        'user_id' => $this->mlAccountId, // Cambiado de $this->userId a $this->mlAccountId
                        'titulo' => $body['title'] ?? 'Sin título',
                        'imagen' => $body['thumbnail'] ?? null,
                        'stock_actual' => $body['available_quantity'] ?? 0,
                        'precio' => $precio,
                        'estado' => $body['status'] ?? 'Desconocido',
                        'permalink' => $body['permalink'] ?? '#',
                        'condicion' => $body['condition'] ?? 'Desconocido',
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
                        'updated_at' => now(),
                    ]
                );
            }

            Log::info("Procesado chunk de ítems para cuenta {$this->mlAccountId}", ['itemIds' => $this->itemIds]);
        } catch (\Exception $e) {
            Log::error("Error en ArticuloSyncJob: " . $e->getMessage(), ['itemIds' => $this->itemIds]);
            $this->fail($e);
        }
    }
}
