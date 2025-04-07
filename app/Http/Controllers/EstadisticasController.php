<?php
namespace App\Http\Controllers;

use App\Services\EstadisticasService;
use Illuminate\Http\Request;

class EstadisticasController extends Controller
{
    protected $estadisticasService;

    public function __construct(EstadisticasService $estadisticasService)
    {
        $this->estadisticasService = $estadisticasService;
       // $this->middleware('auth'); // Requiere autenticación
    }

    public function index()
    {
        $userId = auth()->id();
        $stockPorTipo = $this->estadisticasService->getStockPorTipo($userId);
        $productosEnPromocion = $this->estadisticasService->getProductosEnPromocion($userId);
        $productosPorEstado = $this->estadisticasService->getProductosPorEstado($userId);
        $stockCritico = $this->estadisticasService->getStockCritico($userId);

        return view('dashboard.estadisticas', compact(
            'stockPorTipo',
            'productosEnPromocion',
            'productosPorEstado',
            'stockCritico'
        ));
    }
}
