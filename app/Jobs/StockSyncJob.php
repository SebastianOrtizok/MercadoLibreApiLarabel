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
        try {
            $articulos = Articulo::where('user_id', $this->mlAccountId)->get();
            Log::info("Artículos encontrados: " . $articulos->count());

            if ($articulos->isEmpty()) {
                Log::warning("No se encontraron artículos para ml_account_id {$this->mlAccountId}");
                return;
            }

            foreach ($articulos as $articulo) {
                $stockFulfillment = 0;
                $stockDeposito = 0;

                if ($articulo->user_product_id) {
                    $response = Http::withToken($this->accessToken)
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

                            // Solo sumar cantidades disponibles
                            if ($loc['availability_type'] === 'available') {
                                if (in_array($type, ['meli_facility', 'distribution_center'])) {
                                    $stockFulfillment += $quantity;
                                } elseif (in_array($type, ['selling_address', 'warehouse', 'default'])) {
                                    $stockDeposito += $quantity;
                                } else {
                                    Log::info("Ubicación desconocida ignorada para {$articulo->ml_product_id}", [
                                        'type' => $type,
                                        'quantity' => $quantity
                                    ]);
                                }
                            }
                        }
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

                                foreach ($locations as $loc) {
                                    $type = $loc['type'] ?? 'unknown';
                                    $quantity = $loc['quantity'] ?? 0;

                                    if ($loc['availability_type'] === 'available') {
                                        if (in_array($type, ['meli_facility', 'distribution_center'])) {
                                            $stockFulfillment += $quantity;
                                        } elseif (in_array($type, ['selling_address', 'warehouse', 'default'])) {
                                            $stockDeposito += $quantity;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    // Sin user_product_id, replicar stock_actual según logistic_type
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

                // Actualizar solo stock_fulfillment y stock_deposito, stock_actual queda intacto
                $articulo->update([
                    'stock_fulfillment' => $stockFulfillment,
                    'stock_deposito' => $stockDeposito,
                ]);

                Log::info("Stock actualizado para {$articulo->ml_product_id}", [
                    'fulfillment' => $stockFulfillment,
                    'deposito' => $stockDeposito,
                    'stock_actual' => $articulo->stock_actual // Solo para referencia, no se modifica
                ]);

                usleep(500000); // Retraso de 0.5 segundos
            }
        } catch (\Exception $e) {
            Log::error("Error en StockSyncJob: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->fail($e);
        }
    }
}
