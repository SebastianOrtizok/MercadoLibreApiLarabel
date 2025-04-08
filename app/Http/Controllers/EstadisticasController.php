<?php

namespace App\Http\Controllers;

use App\Services\EstadisticasService;
use App\Services\MercadoLibreService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EstadisticasController extends Controller
{
    protected $estadisticasService;

    public function __construct(EstadisticasService $estadisticasService, MercadoLibreService $mercadoLibreService)
    {
        $this->estadisticasService = new EstadisticasService($mercadoLibreService);
    }

    public function index(Request $request)
    {
        $userId = auth()->id();
        $mlAccountIds = \DB::table('mercadolibre_tokens')
            ->where('user_id', $userId)
            ->pluck('ml_account_id')
            ->toArray();

        $fechaInicio = $request->input('fecha_inicio')
            ? Carbon::parse($request->input('fecha_inicio'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $fechaFin = $request->input('fecha_fin')
            ? Carbon::parse($request->input('fecha_fin'))->endOfDay()->min(Carbon::now())
            : Carbon::now()->endOfDay();

        $stockPorTipo = $this->estadisticasService->getStockPorTipo($mlAccountIds);
        $productosEnPromocion = $this->estadisticasService->getProductosEnPromocion($mlAccountIds);
        $productosPorEstado = $this->estadisticasService->getProductosPorEstado($mlAccountIds);
        $stockCritico = $this->estadisticasService->getStockCritico($mlAccountIds);
        $ventasPorPeriodo = $this->estadisticasService->getVentasPorPeriodo($mlAccountIds, $fechaInicio, $fechaFin);
        $ventasPorDiaSemana = $this->estadisticasService->getVentasPorDiaSemana($mlAccountIds);
        $topProductosVendidos = $this->estadisticasService->getTopProductosVendidos($mlAccountIds, $fechaInicio, $fechaFin);
        $totalFacturado = $this->estadisticasService->getTotalFacturado($mlAccountIds, $fechaInicio, $fechaFin);
        $tasaConversion = $this->estadisticasService->getTasaConversionPorProducto($mlAccountIds, $fechaInicio, $fechaFin);

        $topVentasPorCuenta = $tasaConversion['top_ventas'];
        $bottomVentasPorCuenta = $tasaConversion['bottom_ventas'];

        return view('dashboard.estadisticas', compact(
            'stockPorTipo',
            'productosEnPromocion',
            'productosPorEstado',
            'stockCritico',
            'ventasPorPeriodo',
            'ventasPorDiaSemana',
            'topProductosVendidos',
            'totalFacturado',
            'topVentasPorCuenta',
            'bottomVentasPorCuenta',
            'fechaInicio',
            'fechaFin'
        ));
    }
}
