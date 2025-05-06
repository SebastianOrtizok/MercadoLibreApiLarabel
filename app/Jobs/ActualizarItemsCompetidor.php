<?php

namespace App\Jobs;

use App\Services\CompetidorXCategoriaService;
use App\Models\Competidor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ActualizarItemsCompetidor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $competidor;
    protected $categoryId;

    public function __construct(Competidor $competidor, $categoryId = 'MLA1051')
    {
        $this->competidor = $competidor;
        $this->categoryId = $categoryId;
    }

    public function handle(CompetidorXCategoriaService $competidorService)
    {
        $userId = $this->competidor->user_id;
        $mlAccountId = \App\Models\MercadoLibreToken::where('user_id', $userId)->first()->ml_account_id ?? null;

        if (!$mlAccountId) {
            \Log::error('Cuenta de Mercado Libre no vinculada', ['user_id' => $userId]);
            return;
        }

        try {
            \Log::info('Iniciando actualización de ítems', [
                'user_id' => $userId,
                'competidor_id' => $this->competidor->id,
                'category_id' => $this->categoryId,
            ]);

            $result = $competidorService->getItemsByCategory($userId, $mlAccountId, $this->categoryId);
            $items = $result['items'];

            \Log::info('Ítems obtenidos para actualizar', [
                'items_count' => count($items),
                'competidor_id' => $this->competidor->id,
            ]);

            foreach ($items as $item) {
                \App\Models\ItemCompetidor::updateOrCreate(
                    [
                        'competidor_id' => $this->competidor->id,
                        'item_id' => $item['item_id'],
                    ],
                    [
                        'titulo' => $item['titulo'],
                        'precio' => $item['precio'],
                        'seller' => $item['seller'],
                        'cantidad_vendida' => $item['cantidad_vendida'],
                        'cantidad_disponible' => $item['cantidad_disponible'],
                        'envio_gratis' => $item['envio_gratis'],
                        'ultima_actualizacion' => now(),
                        'following' => $item['following'],
                    ]
                );
            }

            \Log::info('Ítems de competidores actualizados exitosamente', [
                'competidor_id' => $this->competidor->id,
                'user_id' => $userId,
                'items_count' => count($items),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar ítems de competidores en el job', [
                'error' => $e->getMessage(),
                'competidor_id' => $this->competidor->id,
                'user_id' => $userId,
                'category_id' => $this->categoryId,
            ]);
            throw $e;
        }
    }
}
