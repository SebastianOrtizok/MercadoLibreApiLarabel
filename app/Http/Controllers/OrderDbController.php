<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\OrderDbService;
use App\Services\MercadoLibreService;
use Carbon\Carbon;

class OrderDbController extends Controller
{
    protected $orderDbService;
    protected $mercadoLibreService;

    public function __construct(OrderDbService $orderDbService, MercadoLibreService $mercadoLibreService)
    {
        $this->orderDbService = $orderDbService;
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function syncOrders(Request $request)
    {
        try {
            $userId = auth()->user()->id;

            $mlAccounts = DB::table('mercadolibre_tokens')
                ->where('user_id', $userId)
                ->select('ml_account_id')
                ->get();

            if ($mlAccounts->isEmpty()) {
                return redirect()->back()->with('error', 'No hay cuentas asociadas.');
            }

            $dateFrom = $request->input('date_from')
                ? Carbon::parse($request->input('date_from'))->startOfDay()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z')
                : Carbon::today()->startOfDay()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
            $dateTo = $request->input('date_to')
                ? Carbon::parse($request->input('date_to'))->endOfDay()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z')
                : Carbon::today()->endOfDay()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
            $orderStatus = $request->input('order_status', 'paid'); // Valor por defecto: 'paid'

            $ordersProcessed = 0;
            foreach ($mlAccounts as $account) {
                $accessToken = $this->mercadoLibreService->getAccessToken($userId, $account->ml_account_id);

                Log::info("Sincronizando órdenes para la cuenta: {$account->ml_account_id}, desde: $dateFrom, hasta: $dateTo, estado: $orderStatus");
                $result = $this->orderDbService->syncOrders($account->ml_account_id, $accessToken, $dateFrom, $dateTo, $orderStatus);
                $ordersProcessed += $result['orders_processed'];
            }

            return redirect()->back()->with('success', "Órdenes sincronizadas correctamente. Total procesadas: $ordersProcessed");

        } catch (\Exception $e) {
            Log::error("Error al sincronizar órdenes: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
