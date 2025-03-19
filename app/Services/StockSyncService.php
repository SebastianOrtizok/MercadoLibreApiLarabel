<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\StockSyncJob;

class StockSyncService
{
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
                StockSyncJob::dispatch($userId, $token->access_token, $token->ml_account_id);
                Log::info("Despachado StockSyncJob para cuenta {$token->ml_account_id}");
            }
        } catch (\Exception $e) {
            Log::error("Error en StockSyncService: " . $e->getMessage());
        }
    }
}
