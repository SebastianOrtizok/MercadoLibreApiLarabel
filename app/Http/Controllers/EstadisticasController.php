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

        // Fechas desde el request o por defecto (últimos 30 días)
        $fechaInicio = $request->input('fecha_inicio')
            ? Carbon::parse($request->input('fecha_inicio'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $fechaFin = $request->input('fecha_fin')
            ? Carbon::parse($request->input('fecha_fin'))->endOfDay()
            : Carbon::now()->endOfDay();

        $stockPorTipo = $this->estadisticasService->getStockPorTipo($mlAccountIds);
        $productosEnPromocion = $this->estadisticasService->getProductosEnPromocion($mlAccountIds);
        $productosPorEstado = $this->estadisticasService->getProductosPorEstado($mlAccountIds);
        $stockCritico = $this->estadisticasService->getStockCritico($mlAccountIds);
        $ventasPorPeriodo = $this->estadisticasService->getVentasPorPeriodo($mlAccountIds, $fechaInicio, $fechaFin);
        $ventasPorDiaSemana = $this->estadisticasService->getVentasPorDiaSemana($mlAccountIds);
        $topProductosVendidos = $this->estadisticasService->getTopProductosVendidos($mlAccountIds, $fechaInicio, $fechaFin); // Nuevo

        return view('dashboard.estadisticas', compact(
            'stockPorTipo',
            'productosEnPromocion',
            'productosPorEstado',
            'stockCritico',
            'ventasPorPeriodo',
            'ventasPorDiaSemana',
            'topProductosVendidos', // Nuevo
            'fechaInicio',
            'fechaFin'
        ));
    }
}
