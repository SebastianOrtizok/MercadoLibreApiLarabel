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
    }

    public function handle()
    {
        Log::info("Iniciando StockSyncJob para ml_account_id {$this->mlAccountId}");
        try {
            $articulos = Articulo::where('user_id', $this->mlAccountId)->get();
            Log::info("Artículos encontrados para ml_account_id {$this->mlAccountId}: " . $articulos->count());

            if ($articulos->isEmpty()) {
                Log::warning("No se encontraron artículos para ml_account_id {$this->mlAccountId}");
                return;
            }

            foreach ($articulos as $articulo) {
                if ($articulo->user_product_id) {
                    $response = Http::withToken($this->accessToken)
                        ->get("https://api.mercadolibre.com/user-products/{$articulo->user_product_id}/stock");

                    if ($response->successful()) {
                        $stockData = $response->json();
                        $locations = $stockData['locations'] ?? [];

                        $stockFulfillment = 0;
                        $stockDeposito = 0;

                        foreach ($locations as $loc) {
                            if (in_array($loc['type'], ['meli_facility', 'distribution_center'])) {
                                $stockFulfillment += $loc['quantity'] ?? 0;
                            } elseif (in_array($loc['type'], ['selling_address', 'warehouse', 'default'])) {
                                $stockDeposito += $loc['quantity'] ?? 0;
                            }
                        }

                        $articulo->update([
                            'stock_fulfillment' => $stockFulfillment,
                            'stock_deposito' => $stockDeposito,
                        ]);

                        Log::info("Stock actualizado para {$articulo->ml_product_id}", [
                            'fulfillment' => $stockFulfillment,
                            'deposito' => $stockDeposito
                        ]);
                    } else {
                        Log::warning("Fallo al obtener stock para {$articulo->ml_product_id}", [
                            'status' => $response->status(),
                            'response' => $response->body()
                        ]);

                        if ($response->status() === 401) {
                            $this->accessToken = app(MercadoLibreService::class)->getAccessToken($this->userId, $this->mlAccountId);
                            $response = Http::withToken($this->accessToken)
                                ->get("https://api.mercadolibre.com/user-products/{$articulo->user_product_id}/stock");

                            if ($response->successful()) {
                                $stockData = $response->json();
                                $locations = $stockData['locations'] ?? [];

                                $stockFulfillment = 0;
                                $stockDeposito = 0;

                                foreach ($locations as $loc) {
                                    if (in_array($loc['type'], ['meli_facility', 'distribution_center'])) {
                                        $stockFulfillment += $loc['quantity'] ?? 0;
                                    } elseif (in_array($loc['type'], ['selling_address', 'warehouse', 'default'])) {
                                        $stockDeposito += $loc['quantity'] ?? 0;
                                    }
                                }

                                $articulo->update([
                                    'stock_fulfillment' => $stockFulfillment,
                                    'stock_deposito' => $stockDeposito,
                                ]);

                                Log::info("Stock actualizado tras reintento para {$articulo->ml_product_id}", [
                                    'fulfillment' => $stockFulfillment,
                                    'deposito' => $stockDeposito
                                ]);
                            } else {
                                Log::error("Fallo tras reintento para {$articulo->ml_product_id}", [
                                    'status' => $response->status(),
                                    'response' => $response->body()
                                ]);
                            }
                        }
                    }
                } else {
                    if ($articulo->logistic_type === 'fulfillment') {
                        $articulo->update([
                            'stock_fulfillment' => $articulo->stock_actual,
                            'stock_deposito' => 0,
                        ]);
                        Log::info("Stock fulfillment copiado desde stock_actual para {$articulo->ml_product_id}", [
                            'stock' => $articulo->stock_fulfillment
                        ]);
                    } else {
                        $articulo->update([
                            'stock_deposito' => $articulo->stock_actual,
                            'stock_fulfillment' => 0,
                        ]);
                        Log::info("Stock deposito copiado desde stock_actual para {$articulo->ml_product_id}", [
                            'stock' => $articulo->stock_deposito
                        ]);
                    }
                }
                usleep(500000); // Retraso de 0.5 segundos entre solicitudes
            }
        } catch (\Exception $e) {
            Log::error("Error en StockSyncJob para ml_account_id {$this->mlAccountId}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->fail($e);
        }
    }
}
