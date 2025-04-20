<?php
namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use App\Models\Competidor;
use App\Models\ItemCompetidor;

class ActualizarItemsCompetidor
{
    use Dispatchable;

    protected $competidor;

    public function __construct(Competidor $competidor)
    {
        $this->competidor = $competidor;
    }

    public function handle()
    {
        dump("Consultando ítems para el competidor ID: " . $this->competidor->id);
        dump("Seller ID del competidor: " . $this->competidor->seller_id);
        dump("User ID asociado: " . $this->competidor->user_id);

        $user = $this->competidor->user;
        if (!$user) {
            dump("No se encontró el usuario asociado al competidor.");
            return "Fin del Job - Sin usuario";
        }

        $tokenModel = $user->mercadolibreTokens()->first();
        if (!$tokenModel) {
            dump("No se encontró un token de acceso para el usuario ID: " . $user->id);
            return "Fin del Job - Sin token";
        }
        $token = $tokenModel->access_token;
        dump("Usando token de ml_account_id: " . $tokenModel->ml_account_id);
        dump("Token: " . substr($token, 0, 20) . "...");

        $offset = 0;
        $limit = 50;
        $totalProcessedItems = 0;

        do {
            $response = Http::withToken($token)->get("https://api.mercadolibre.com/users/{$this->competidor->seller_id}/items/search", [
                'status' => 'active',
                'limit' => $limit,
                'offset' => $offset,
            ]);

            dump("Estado de la respuesta (offset $offset): " . $response->status());

            if (!$response->successful()) {
                dump("Error en la API: " . $response->body());
                break;
            }

            $data = $response->json();
            $itemIds = $data['results'] ?? [];
            dump("Cantidad de ítems recibidos en esta página: " . count($itemIds));

            if (empty($itemIds)) {
                dump("No hay más ítems para procesar.");
                break;
            }

            foreach ($itemIds as $itemId) {
                $itemResponse = Http::withToken($token)->get("https://api.mercadolibre.com/items/{$itemId}", [
                    'include_attributes' => 'all',
                ]);

                if ($itemResponse->successful()) {
                    $itemData = $itemResponse->json();
                    if ($itemData['seller_id'] == $this->competidor->seller_id) {
                        ItemCompetidor::updateOrCreate(
                            [
                                'competidor_id' => $this->competidor->id,
                                'item_id' => $itemData['id'],
                            ],
                            [
                                'titulo' => $itemData['title'] ?? 'Sin título',
                                'precio' => $itemData['price'] ?? null,
                                'cantidad_disponible' => $itemData['available_quantity'] ?? 0,
                                'cantidad_vendida' => $itemData['sold_quantity'] ?? 0,
                                'envio_gratis' => $itemData['shipping']['free_shipping'] ?? false,
                                'ultima_actualizacion' => now(),
                            ]
                        );
                        $totalProcessedItems++;
                        dump("Ítem procesado: " . $itemData['id']);
                    }
                }
            }

            $offset += $limit;
            dump("Ítems procesados hasta ahora: " . $totalProcessedItems);

        } while (count($itemIds) == $limit);

        dump("Total de ítems procesados para seller_id " . $this->competidor->seller_id . ": " . $totalProcessedItems);
        return "Fin del Job";
    }
}
