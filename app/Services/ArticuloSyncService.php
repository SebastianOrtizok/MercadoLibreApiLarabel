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

                    if (empty($itemIds)) {
                        Log::info("No hay más ítems para cuenta {$mlAccountId}", ['offset' => $offset]);
                        break;
                    }

                    Log::info("Items obtenidos para cuenta {$mlAccountId}", [
                        'count' => count($itemIds),
                        'total' => $totalItems,
                    ]);

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

                    foreach ($details as $item) {
                        $body = $item['body'] ?? [];
                        $itemLastUpdated = Carbon::parse($body['last_updated'] ?? now());

                        if ($itemLastUpdated->lessThanOrEqualTo($lastSyncCarbon)) {
                            $continueSync = false;
                            Log::info("Ítem más antiguo encontrado, deteniendo sincronización", [
                                'ml_product_id' => $body['id'],
                                'last_updated' => $itemLastUpdated->toDateTimeString(),
                                'last_sync' => $lastSyncCarbon->toDateTimeString(),
                            ]);
                            break; // Salir del foreach, pero despachar lo que ya tenemos
                        }

                        $itemsToSync[] = $body['id'];
                        $oldestLastUpdated = $itemLastUpdated;
                    }

                    if (!empty($itemsToSync)) {
                        ArticuloSyncJob::dispatch($itemsToSync, $token->access_token, $userId, $mlAccountId);
                        Log::info("Procesando ítems para cuenta {$mlAccountId}", [
                            'offset' => $offset,
                            'oldest_last_updated' => $oldestLastUpdated->toDateTimeString(),
                            'item_count' => count($itemsToSync),
                        ]);
                    }

                    $offset += $limit;
                } while ($continueSync && $offset < $totalItems);

                SyncTimestamp::updateOrCreate([], ['timestamp' => now()]);
                Log::info("Cuenta procesada: {$mlAccountId}", ['total_items' => $totalItems]);
            }
        } catch (\Exception $e) {
            Log::error("Error en syncArticulos: " . $e->getMessage());
            throw $e;
        }
    }
}
