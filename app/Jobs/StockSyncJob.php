<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Articulo;
use App\Services\MercadoLibreService;

class StockSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $mlAccountId;
    protected $accessToken;

    public function __construct($userId, $mlAccountId, $accessToken)
    {
        $this->userId = $userId;
        $this->mlAccountId = $mlAccountId;
        $this->accessToken = $accessToken;
        $this->onConnection('database')->onQueue('default');
    }

    public function handle()
    {
        Log::info("Iniciando StockSyncJob para ml_account_id {$this->mlAccountId}");
        $articulos = Articulo::where('user_id', $this->mlAccountId)->get();
        Log::info("Artículos encontrados: " . $articulos->count());

        if ($articulos->isEmpty()) {
            Log::warning("No se encontraron artículos para ml_account_id {$this->mlAccountId}");
            return;
        }

        $count = 0;
        foreach ($articulos as $articulo) {
            $count++;
            Log::info("Procesando artículo #$count: {$articulo->ml_product_id}");

            $stockFulfillment = 0;
            $stockDeposito = 0;

            if ($articulo->user_product_id) {
                try {
                    $response = Http::withToken($this->accessToken)
                        ->timeout(10) // Timeout de 10 segundos
                        ->get("https://api.mercadolibre.com/user-products/{$articulo->user_product_id}/stock");

                    Log::info("Respuesta API para {$articulo->ml_product_id}", [
                        'status' => $response->status(),
                        'data' => $response->json()
                    ]);

                    if ($response->successful()) {
                        $stockData = $response->json();
                        $locations = $stockData['locations'] ?? [];

                        foreach ($locations as $loc) {
                            $type = $loc['type'] ?? 'unknown';
                            $quantity = $loc['quantity'] ?? 0;

                            if ($quantity > 0) {
                                if (in_array($type, ['meli_facility', 'distribution_center'])) {
                                    $stockFulfillment += $quantity;
                                } elseif (in_array($type, ['selling_address', 'warehouse', 'default'])) {
                                    $stockDeposito += $quantity;
                                }
                            }
                        }
                    } else {
                        Log::warning("Fallo API para {$articulo->ml_product_id}", [
                            'status' => $response->status(),
                            'response' => $response->body()
                        ]);
                        continue; // Saltar este artículo
                    }
                } catch (\Exception $e) {
                    Log::error("Error API para {$articulo->ml_product_id}: " . $e->getMessage());
                    continue; // Saltar este artículo
                }
            } else {
                $stockActual = $articulo->stock_actual ?? 0;
                if ($articulo->logistic_type === 'fulfillment') {
                    $stockFulfillment = $stockActual;
                } else {
                    $stockDeposito = $stockActual;
                }
                Log::info("Sin user_product_id, replicando stock_actual para {$articulo->ml_product_id}", [
                    'stock_actual' => $stockActual,
                    'logistic_type' => $articulo->logistic_type
                ]);
            }

            try {
                $updated = $articulo->update([
                    'stock_fulfillment' => $stockFulfillment,
                    'stock_deposito' => $stockDeposito,
                ]);
                Log::info("Stock actualizado para {$articulo->ml_product_id}", [
                    'success' => $updated,
                    'fulfillment' => $stockFulfillment,
                    'deposito' => $stockDeposito,
                    'stock_actual' => $articulo->stock_actual
                ]);
            } catch (\Exception $e) {
                Log::error("Error al guardar {$articulo->ml_product_id}: " . $e->getMessage());
            }

            usleep(200000); // Reducimos a 0.2 segundos
        }
        Log::info("StockSyncJob terminado");
    }
}
