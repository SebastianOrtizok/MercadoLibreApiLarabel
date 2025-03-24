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

            $dateFrom = Carbon::now()->subHour()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
            $dateTo = Carbon::now()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

            // Encolar ventas
            foreach ($mlAccounts as $account) {
                Log::info("Encolando sincronización manual de órdenes para cuenta: {$account->ml_account_id}");
                \App\Jobs\SyncOrdersJob::dispatch($account->user_id, $account->ml_account_id, $dateFrom, $dateTo)
                    ->onQueue('orders');
            }

            // Stock sincrónico
            Log::info("Iniciando sincronización manual de stock");
            $this->stockVentaService->syncStockFromSales();

            return redirect()->back()->with('success', 'Sincronización de ventas y stock iniciada correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al sincronizar manualmente: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al sincronizar: ' . $e->getMessage());
        }
    }
}
