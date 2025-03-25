<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StockVentaService;
use Illuminate\Support\Facades\Log;

class SyncStockHourly extends Command
{
    protected $signature = 'sync:stock-hourly';
    protected $description = 'Sincroniza el stock cada hora';
    protected $stockVentaService;

    public function __construct(StockVentaService $stockVentaService)
    {
        parent::__construct();
        $this->stockVentaService = $stockVentaService;
    }

    public function handle()
    {
        try {
            Log::info("Iniciando sincronización horaria de stock");
            $this->stockVentaService->syncStockFromSales(true); // subHour()
            $this->info("Sincronización horaria de stock completada");
            Log::info("Sincronización horaria de stock completada");
        } catch (\Exception $e) {
            Log::error("Error en la sincronización horaria de stock: " . $e->getMessage());
            $this->error("Error: " . $e->getMessage());
        }
    }
}
