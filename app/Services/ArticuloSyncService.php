<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\ArticuloSyncJob;
use App\Models\SyncTimestamp;
use App\Services\MercadoLibreService;

class ArticuloSyncService
{
    protected $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function syncArticulos(string $userId, int $limit = 50)
    {
        try {
            $lastSync = SyncTimestamp::latest()->first()->timestamp ?? now();
            $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();
            $maxLimit = 50; // Límite máximo de la API de ML
            $limit = min($limit, $maxLimit);

            foreach ($tokens as $token) {
                $mlAccountId = $token->ml_account_id;
                $offset = 0;
                $totalItems = 0;

                Log::info("Iniciando sincronización para cuenta ML: {$mlAccountId}", ['limit' => $limit]);

                do {
                    Log::info("Consulta a API para cuenta {$mlAccountId}", [
                        'limit' => $limit,
                        'offset' => $offset,
                        'total_previo' => $totalItems,
                    ]);

                    $response = Http::withToken($this->mercadoLibreService->getAccessToken($userId, $mlAccountId))
                        ->get("https://api.mercadolibre.com/users/{$mlAccountId}/items/search", [
                            'limit' => $limit,
                            'offset' => $offset,
                            'sort' => 'last_updated_desc',
                        ]);

                    if ($response->failed()) {
                        throw new \Exception("Error al buscar ítems para {$mlAccountId}: " . $response->body());
                    }

                    $data = $response->json();
                    $itemIds = $data['results'] ?? [];
                    $totalItems = $data['paging']['total'] ?? 0;

                    if (empty($itemIds)) {
                        Log::info("No hay más ítems para cuenta {$mlAccountId}", ['offset' => $offset]);
                        break;
                    }

                    Log::info("Items obtenidos para cuenta {$mlAccountId}", [
                        'count' => count($itemIds),
                        'total' => $totalItems,
                    ]);

                    $chunks = array_chunk($itemIds, 20);
                    foreach ($chunks as $chunk) {
                        ArticuloSyncJob::dispatch($chunk, $token->access_token, $userId, $mlAccountId);
                    }

                    $offset += $limit;
                } while ($offset < $totalItems);

                SyncTimestamp::updateOrCreate([], ['timestamp' => now()]);
                Log::info("Cuenta procesada: {$mlAccountId}", ['total_items' => $totalItems]);
            }
        } catch (\Exception $e) {
            Log::error("Error en syncArticulos: " . $e->getMessage());
            throw $e;
        }
    }
}
