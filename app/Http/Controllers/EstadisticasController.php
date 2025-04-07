<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EstadisticasService;
use Carbon\Carbon;

class EstadisticasController extends Controller
{
    protected $estadisticasService;

    public function __construct(EstadisticasService $estadisticasService)
    {
        $this->estadisticasService = $estadisticasService;
        // $this->middleware('auth'); // Descomenta cuando configures autenticación
    }

    public function index(Request $request)
    {
        $userId = auth()->id();
        $mlAccountIds = \DB::table('mercadolibre_tokens')
            ->where('user_id', $userId)
            ->pluck('ml_account_id')
            ->toArray();

        \Log::info('User ID autenticado:', ['user_id' => $userId]);
        \Log::info('Cuentas de MercadoLibre asociadas:', ['ml_account_ids' => $mlAccountIds]);

        // Fechas desde el request o por defecto (últimos 30 días)
        $fechaInicio = $request->input('fecha_inicio')
            ? Carbon::parse($request->input('fecha_inicio'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $fechaFin = $request->input('fecha_fin')
            ? Carbon::parse($request->input('fecha_fin'))->endOfDay()
            : Carbon::now()->endOfDay();

        \Log::info('Fechas recibidas:', [
            'fecha_inicio_raw' => $request->input('fecha_inicio'),
            'fecha_fin_raw' => $request->input('fecha_fin'),
            'fecha_inicio_parsed' => $fechaInicio->toDateTimeString(),
            'fecha_fin_parsed' => $fechaFin->toDateTimeString()
        ]);

        $stockPorTipo = $this->estadisticasService->getStockPorTipo($mlAccountIds);
        $productosEnPromocion = $this->estadisticasService->getProductosEnPromocion($mlAccountIds);
        $productosPorEstado = $this->estadisticasService->getProductosPorEstado($mlAccountIds);
        $stockCritico = $this->estadisticasService->getStockCritico($mlAccountIds);
        $ventasPorPeriodo = $this->estadisticasService->getVentasPorPeriodo($mlAccountIds, $fechaInicio, $fechaFin);
        $ventasPorDiaSemana = $this->estadisticasService->getVentasPorDiaSemana($mlAccountIds);

        \Log::info('Datos para la vista:', [
            'stockPorTipo' => $stockPorTipo,
            'productosEnPromocion' => $productosEnPromocion->toArray(),
            'productosPorEstado' => $productosPorEstado->toArray(),
            'stockCritico' => $stockCritico->toArray(),
            'ventasPorPeriodo' => $ventasPorPeriodo->toArray(),
            'ventasPorDiaSemana' => $ventasPorDiaSemana
        ]);

        return view('dashboard.estadisticas', compact(
            'stockPorTipo',
            'productosEnPromocion',
            'productosPorEstado',
            'stockCritico',
            'ventasPorPeriodo',
            'ventasPorDiaSemana',
            'fechaInicio',
            'fechaFin'
        ));
    }
}
