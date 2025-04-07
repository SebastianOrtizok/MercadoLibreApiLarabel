<?php
namespace App\Jobs;

use App\Services\ItemPromotionsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncItemPromotionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $products;
    protected $accessToken;
    protected $mlAccountId;

    public function __construct($products, $accessToken, $mlAccountId)
    {
        $this->products = $products;
        $this->accessToken = $accessToken;
        $this->mlAccountId = $mlAccountId;
    }

    public function handle(ItemPromotionsService $service)
    {
        $syncType = $this->queue === 'automatic_promotions' ? 'automática' : 'manual';
        Log::info("Procesando lote de " . $this->products->count() . " productos para cuenta {$this->mlAccountId} (sincronización {$syncType})");

        // Elegir la función del servicio según la cola
        if ($this->queue === 'automatic_promotions') {
            $result = $service->syncItemPromotionsAutomatic($this->products, $this->accessToken);
        } else {
            $result = $service->syncItemPromotions($this->products, $this->accessToken);
        }

        Log::info("Resultado para cuenta {$this->mlAccountId} (sincronización {$syncType}): " . json_encode($result));
    }
}
