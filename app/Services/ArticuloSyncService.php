<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\ArticuloSyncJob;
use App\Models\SyncTimestamp;
use App\Services\MercadoLibreService;
use Carbon\Carbon;

class ArticuloSyncService
{
    protected $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function syncArticulos(string $userId, int $limit = 20)
    {
        try {
            $lastSync = SyncTimestamp::latest()->first()->timestamp ?? now();
            $lastSyncCarbon = Carbon::parse($lastSync);
            $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();
            $maxLimit = 20;
            $limit = min($limit, $maxLimit);

            Log::info("Inicio de syncArticulos", ['user_id' => $userId, 'limit' => $limit]);

            foreach ($tokens as $token) {
                $mlAccountId = $token->ml_account_id;
                $offset = 0;
                $totalItems = 0;
                $continueSync = true;

                Log::info("Iniciando sincronización para cuenta ML: {$mlAccountId}", [
                    'limit' => $limit,
                    'last_sync' => $lastSyncCarbon->toDateTimeString(),
                ]);

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

                    Log::info("Respuesta de /items/search", [
                        'item_count' => count($itemIds),
                        'total' => $totalItems,
                        'item_ids' => $itemIds,
                    ]);

                    if (empty($itemIds)) {
                        Log::info("No hay más ítems para cuenta {$mlAccountId}", ['offset' => $offset]);
                        break;
                    }

                    $detailsResponse = Http::withToken($this->mercadoLibreService->getAccessToken($userId, $mlAccountId))
                        ->get("https://api.mercadolibre.com/items", [
                            'ids' => implode(',', $itemIds),
                        ]);

                    if ($detailsResponse->failed()) {
                        throw new \Exception("Error al obtener detalles para {$mlAccountId}: " . $detailsResponse->body());
                    }

                    $details = $detailsResponse->json();
                    $oldestLastUpdated = null;
                    $itemsToSync = [];

                    Log::info("Procesando detalles de ítems", ['details_count' => count($details)]);

                    foreach ($details as $item) {
                        $body = $item['body'] ?? [];
                        $itemLastUpdated = Carbon::parse($body['last_updated'] ?? now());

                        Log::info("Revisando ítem", [
                            'ml_product_id' => $body['id'],
                            'last_updated' => $itemLastUpdated->toDateTimeString(),
                            'last_sync' => $lastSyncCarbon->toDateTimeString(),
                        ]);

                        if ($itemLastUpdated->lessThanOrEqualTo($lastSyncCarbon)) {
                            Log::info("Ítem anterior a last_sync, no se sincroniza", [
                                'ml_product_id' => $body['id'],
                                'last_updated' => $itemLastUpdated->toDateTimeString(),
                            ]);
                            $continueSync = false; // Parar el do-while después de este chunk
                            continue; // Saltar este ítem, pero seguir revisando el chunk
                        }

                        $itemsToSync[] = $body['id'];
                        $oldestLastUpdated = $itemLastUpdated;
                    }

                    if (!empty($itemsToSync)) {
                        Log::info("Despachando job para ítems", [
                            'items_to_sync' => $itemsToSync,
                            'count' => count($itemsToSync),
                        ]);
                        ArticuloSyncJob::dispatch($itemsToSync, $token->access_token, $userId, $mlAccountId);
                        Log::info("Job despachado para cuenta {$mlAccountId}", [
                            'offset' => $offset,
                            'oldest_last_updated' => $oldestLastUpdated->toDateTimeString(),
                            'item_count' => count($itemsToSync),
                        ]);
                    } else {
                        Log::info("No hay ítems para despachar en este chunk", ['offset' => $offset]);
                        $continueSync = false; // Parar si no hay nada para sincronizar
                    }

                    $offset += $limit;
                } while ($continueSync && $offset < $totalItems);

                SyncTimestamp::updateOrCreate([], ['timestamp' => now()]);
                Log::info("Cuenta procesada: {$mlAccountId}", ['total_items' => $totalItems]);
            }

            Log::info("Fin de syncArticulos", ['user_id' => $userId]);
        } catch (\Exception $e) {
            Log::error("Error en syncArticulos: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
}
