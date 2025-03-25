<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderDbService;
use App\Services\StockVentaService;
use App\Services\MercadoLibreService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncVentasStockController extends Controller
{
    protected $orderDbService;
    protected $stockVentaService;
    protected $mercadoLibreService;

    public function __construct(OrderDbService $orderDbService, StockVentaService $stockVentaService, MercadoLibreService $mercadoLibreService)
    {
        $this->orderDbService = $orderDbService;
        $this->stockVentaService = $stockVentaService;
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function syncAllNow(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $mlAccounts = DB::table('mercadolibre_tokens')
                ->where('user_id', $userId)
                ->select('user_id', 'ml_account_id')
                ->get();

            if ($mlAccounts->isEmpty()) {
                return redirect()->back()->with('error', 'No hay cuentas asociadas.');
            }

            $dateFrom = Carbon::today()->startOfDay()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
            $dateTo = Carbon::now()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

            $totalOrdersProcessed = 0;
            foreach ($mlAccounts as $account) {
                Log::info("Sincronizando manualmente órdenes para cuenta: {$account->ml_account_id}");
                $accessToken = $this->mercadoLibreService->getAccessToken($account->user_id, $account->ml_account_id);
                $result = $this->orderDbService->syncOrders($account->ml_account_id, $accessToken, $dateFrom, $dateTo);
                $totalOrdersProcessed += $result['orders_processed'];
            }

            Log::info("Iniciando sincronización manual de stock");
            $this->stockVentaService->syncStockFromSales(true); // Todo el día

            return redirect()->back()->with('success', "Sincronización completada. Órdenes procesadas: $totalOrdersProcessed");
        } catch (\Exception $e) {
            Log::error("Error al sincronizar manualmente: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al sincronizar: ' . $e->getMessage());
        }
    }
}
