<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderDbService;
use App\Services\MercadoLibreService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncOrdersHourly extends Command
{
    protected $signature = 'orders:sync-hourly';
    protected $description = 'Sincroniza las órdenes de MercadoLibre cada hora';

    protected $orderDbService;
    protected $mercadoLibreService;

    public function __construct(OrderDbService $orderDbService, MercadoLibreService $mercadoLibreService)
    {
        parent::__construct();
        $this->orderDbService = $orderDbService;
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function handle()
    {
        try {
            Log::info("Iniciando sincronización horaria de órdenes");

            $mlAccounts = DB::table('mercadolibre_tokens')
                ->select('user_id', 'ml_account_id')
                ->get();

            if ($mlAccounts->isEmpty()) {
                Log::warning("No hay cuentas asociadas en mercadolibre_tokens");
                $this->info("No hay cuentas asociadas.");
                return;
            }

            $dateFrom = Carbon::now()->startOfHour()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
            $dateTo = Carbon::now()->endOfHour()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

            Log::info("Rango calculado para la API: desde {$dateFrom} hasta {$dateTo}");

            $totalOrdersProcessed = 0;
            foreach ($mlAccounts as $account) {
                $this->info("Sincronizando órdenes para la cuenta: {$account->ml_account_id}");
                Log::info("Sincronizando órdenes para la cuenta: {$account->ml_account_id}");

                try {
                    $accessToken = $this->mercadoLibreService->getAccessToken($account->user_id, $account->ml_account_id);
                    Log::info("Llamando a syncOrders con ml_account_id: {$account->ml_account_id}, dateFrom: {$dateFrom}, dateTo: {$dateTo}");
                    $result = $this->orderDbService->syncOrders($account->ml_account_id, $accessToken, $dateFrom, $dateTo);
                    $ordersProcessed = $result['orders_processed'];
                    $totalOrdersProcessed += $ordersProcessed;

                    $this->info("Órdenes procesadas para {$account->ml_account_id}: $ordersProcessed");
                    Log::info("Órdenes procesadas para {$account->ml_account_id}: $ordersProcessed");
                } catch (\Exception $e) {
                    Log::error("Error al sincronizar órdenes para {$account->ml_account_id}: " . $e->getMessage());
                    $this->error("Error en cuenta {$account->ml_account_id}: " . $e->getMessage());
                }
            }

            $this->info("Sincronización horaria completada. Total de órdenes procesadas: $totalOrdersProcessed");
            Log::info("Sincronización horaria completada. Total de órdenes procesadas: $totalOrdersProcessed");

        } catch (\Exception $e) {
            Log::error("Error general en la sincronización horaria: " . $e->getMessage());
            $this->error("Error general: " . $e->getMessage());
        }
    }
}
