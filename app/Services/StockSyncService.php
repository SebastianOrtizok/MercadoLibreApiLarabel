<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\StockSyncJob;
use App\Services\MercadoLibreService;

class StockSyncService
{
    protected $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function syncStocks($userId)
    {
        try {
            $tokens = DB::table('mercadolibre_tokens')
                ->where('user_id', $userId)
                ->get();

            if ($tokens->isEmpty()) {
                Log::warning('No se encontraron tokens para el usuario', ['user_id' => $userId]);
                return;
            }

            foreach ($tokens as $token) {
                // Obtener token fresco
                $accessToken = $this->mercadoLibreService->getAccessToken($userId, $token->ml_account_id);
                StockSyncJob::dispatch($userId, $token->ml_account_id)
                    ->onConnection('database')
                    ->onQueue('default');
                Log::info("Despachado StockSyncJob para cuenta {$token->ml_account_id} con token fresco");
            }
        } catch (\Exception $e) {
            Log::error("Error en StockSyncService: " . $e->getMessage());
        }
    }
}
