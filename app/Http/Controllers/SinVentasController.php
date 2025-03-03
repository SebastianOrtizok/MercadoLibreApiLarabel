<?php
// app/Http/Controllers/SinVentasController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SinVentasService;
use Carbon\Carbon;

class SinVentasController extends Controller
{
    protected $sinVentasService;

    public function __construct(SinVentasService $sinVentasService)
    {
        $this->sinVentasService = $sinVentasService;
    }

    public function index(Request $request)
    {
        try {
            $fechaInicio = $request->input('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio')) : null;
            $fechaFin = $request->input('fecha_fin') ? Carbon::parse($request->input('fecha_fin')) : null;

            if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
                $request->validate([
                    'fecha_inicio' => 'required|date',
                    'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                ]);

                $filters = [
                    'search' => $request->input('search'),
                    'ml_account_id' => $request->input('ml_account_id'),
                    'tipo_publicacion' => $request->input('tipo_publicacion'),
                    'estado_publicacion' => $request->input('estado_publicacion'),
                ];

                $productosPorVentas = $this->sinVentasService->getProductosOrdenadosPorVentas($fechaInicio, $fechaFin, $filters);
            } else {
                $productosPorVentas = [];
            }

            return view('dashboard.sinventas', [
                'productosPorVentas' => $productosPorVentas,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
