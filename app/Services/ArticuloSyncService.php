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
            $offset = 0;

            foreach ($tokens as $token) {
                $mlAccountId = $token->ml_account_id;
                Log::info("Procesando cuenta ML: {$mlAccountId}");

                do {
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
                    if (empty($itemIds)) break;

                    $chunks = array_chunk($itemIds, 20);
                    foreach ($chunks as $chunk) {
                        ArticuloSyncJob::dispatch($chunk, $token->access_token, $userId, $mlAccountId);
                    }

                    $offset += $limit;
                } while ($offset < ($data['paging']['total'] ?? 0));

                SyncTimestamp::updateOrCreate([], ['timestamp' => now()]);
                Log::info("Cuenta procesada: {$mlAccountId}");
            }
        } catch (\Exception $e) {
            Log::error("Error en syncArticulos: " . $e->getMessage());
            throw $e;
        }
    }
}
