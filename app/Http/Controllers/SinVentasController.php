<?php
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
            $fechaInicio = Carbon::parse($request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d')));
            $fechaFin = Carbon::parse($request->input('fecha_fin', now()->format('Y-m-d')));

            $request->validate([
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            ]);

            $limit = $request->input('limit', 50); // Cantidad por página
            $currentPage = (int) $request->input('page', 1); // Página actual
            $consolidarPorSku = $request->input('consolidar_por_sku', false) === 'true'; // Nuevo parámetro

            $filters = [
                'search' => $request->input('search'),
                'ml_account_id' => $request->input('ml_account_id'),
                'tipo_publicacion' => $request->input('tipo_publicacion'),
                'estado_publicacion' => $request->input('estado_publicacion'),
            ];

            // Obtener productos sin ventas, con opción de consolidar por SKU
            $productosSinVentas = $this->sinVentasService->getProductosOrdenadosPorVentas($fechaInicio, $fechaFin, $filters, $consolidarPorSku)
                ->filter(function ($producto) {
                    return $producto->cantidad_vendida == 0;
                });

            // Calcular paginación
            $totalItems = $productosSinVentas->count();
            $totalPages = ceil($totalItems / $limit);
            $productosPaginados = $productosSinVentas->forPage($currentPage, $limit)->values();

            \Log::info('Productos sin ventas paginados', [
                'total_items' => $totalItems,
                'current_page' => $currentPage,
                'limit' => $limit,
                'total_pages' => $totalPages,
                'paginados_count' => $productosPaginados->count(),
                'consolidar_por_sku' => $consolidarPorSku
            ]);

            return view('dashboard.sinventas', [
                'productosPorVentas' => $productosPaginados,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'limit' => $limit,
                'consolidarPorSku' => $consolidarPorSku, // Pasamos esto a la vista
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en SinVentasController: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('dashboard.sinventas', [
                'productosPorVentas' => [],
                'fechaInicio' => Carbon::now()->subDays(30),
                'fechaFin' => Carbon::now(),
                'currentPage' => 1,
                'totalPages' => 1,
                'limit' => 50,
                'consolidarPorSku' => false,
            ])->with('error', $e->getMessage());
        }
    }
}
