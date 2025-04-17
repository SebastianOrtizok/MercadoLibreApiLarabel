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

        $offset = 0;
        $limit = 50;
        $totalProcessedItems = 0;

        do {
            // Usa el endpoint público para buscar ítems del competidor
            $response = Http::get("https://api.mercadolibre.com/sites/MLA/search", [
                'seller_id' => $this->competidor->seller_id,
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
            $items = $data['results'] ?? [];
            dump("Cantidad de ítems recibidos en esta página: " . count($items));

            if (empty($items)) {
                dump("No hay más ítems para procesar.");
                break;
            }

            foreach ($items as $itemData) {
                // No necesitas una segunda llamada a la API porque el endpoint /search ya devuelve los datos básicos
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

            $offset += $limit;
            dump("Ítems procesados hasta ahora: " . $totalProcessedItems);

        } while (count($items) == $limit);

        dump("Total de ítems procesados para seller_id " . $this->competidor->seller_id . ": " . $totalProcessedItems);
        return "Fin del Job";
    }
}
