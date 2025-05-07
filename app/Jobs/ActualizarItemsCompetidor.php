<?php
namespace App\Jobs;

use App\Models\Competidor;
use App\Models\ItemCompetidor;
use App\Services\CompetidorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ActualizarItemsCompetidor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $competidor;

    public function __construct(Competidor $competidor)
    {
        $this->competidor = $competidor;
    }

    public function handle(CompetidorService $competidorService)
    {
        $sellerId = $this->competidor->seller_id;
        $country = 'Argentina'; // Podrías extraer esto de la tabla competidores si agregas un campo

        // Scrapear los ítems del competidor
        $items = $competidorService->scrapeItemsBySeller($sellerId, $country);

        // Guardar los ítems en la base de datos
        foreach ($items as $itemData) {
            ItemCompetidor::updateOrCreate(
                [
                    'competidor_id' => $this->competidor->id,
                    'item_id' => $itemData['item_id'],
                ],
                [
                    'titulo' => $itemData['titulo'],
                    'precio' => $itemData['precio'],
                    'ultima_actualizacion' => now(),
                    'cantidad_disponible' => 0, // Valor predeterminado (no lo obtenemos del scraping)
                    'cantidad_vendida' => 0,    // Valor predeterminado
                    'envio_gratis' => false,    // Valor predeterminado
                ]
            );
        }

        \Log::info("Ítems actualizados para el competidor {$sellerId}", [
            'items_count' => count($items),
            'competidor_id' => $this->competidor->id,
        ]);
    }
}
