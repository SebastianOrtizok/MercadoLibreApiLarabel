<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EstadisticasService;

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
        $userId = auth()->id(); // ID del usuario autenticado
        $mlAccountIds = \DB::table('mercadolibre_tokens')
            ->where('user_id', $userId)
            ->pluck('ml_account_id')
            ->toArray();

        \Log::info('User ID autenticado:', ['user_id' => $userId]);
        \Log::info('Cuentas de MercadoLibre asociadas:', ['ml_account_ids' => $mlAccountIds]);

        $stockPorTipo = $this->estadisticasService->getStockPorTipo($mlAccountIds);
        $productosEnPromocion = $this->estadisticasService->getProductosEnPromocion($mlAccountIds);
        $productosPorEstado = $this->estadisticasService->getProductosPorEstado($mlAccountIds);
        $stockCritico = $this->estadisticasService->getStockCritico($mlAccountIds);

        \Log::info('Datos para la vista:', [
            'stockPorTipo' => $stockPorTipo,
            'productosEnPromocion' => $productosEnPromocion->toArray(),
            'productosPorEstado' => $productosPorEstado->toArray(),
            'stockCritico' => $stockCritico->toArray()
        ]);

        return view('dashboard.estadisticas', compact(
            'stockPorTipo',
            'productosEnPromocion',
            'productosPorEstado',
            'stockCritico'
        ));
    }
}
