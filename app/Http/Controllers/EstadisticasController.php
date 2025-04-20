<?php
namespace App\Http\Controllers;

use App\Services\EstadisticasService;
use App\Services\MercadoLibreService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

        // Log para depurar ml_account_ids
        Log::info('ml_account_ids obtenidos para el usuario', [
            'user_id' => $userId,
            'ml_account_ids' => $mlAccountIds,
        ]);

        // Si no hay ml_account_ids, retornar datos vacíos
        if (empty($mlAccountIds)) {
            Log::warning('No se encontraron ml_account_ids para el usuario', ['user_id' => $userId]);
            return view('dashboard.estadisticas', [
                'stockPorTipo' => ['stock_actual' => 0, 'stock_fulfillment' => 0, 'stock_deposito' => 0],
                'productosEnPromocion' => collect([['titulo' => 'Sin promociones', 'descuento_porcentaje' => 0]]),
                'productosPorEstado' => collect([['estado' => 'Sin datos', 'total' => 0]]),
                'stockCritico' => collect([]),
                'ventasPorPeriodo' => collect([]),
                'ventasPorDiaSemana' => array_fill_keys(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'], 0),
                'topProductosVendidos' => collect([]),
                'totalFacturado' => 0,
                'topVentasPorCuenta' => [],
                'fechaInicio' => Carbon::now()->subDays(30)->startOfDay(),
                'fechaFin' => Carbon::now()->endOfDay(),
            ]);
        }

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
        $topVentasPorCuenta = $this->estadisticasService->getTasaConversionPorProducto($mlAccountIds, $fechaInicio, $fechaFin);

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
            'fechaInicio',
            'fechaFin'
        ));
    }
}
