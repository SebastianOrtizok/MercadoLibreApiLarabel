<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ListadoArticulosService;
use Illuminate\Support\Facades\Auth;

class ListadoArticulosController extends Controller
{
    protected $listadoArticulosService;

    public function __construct(ListadoArticulosService $listadoArticulosService)
    {
        $this->middleware('auth'); // Asegurar que el usuario esté autenticado
        $this->listadoArticulosService = $listadoArticulosService;
    }

    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 50); // Cantidad por página
            $currentPage = (int) $request->input('page', 1); // Página actual

            $filters = [
                'search' => $request->input('search'),
                'estado' => $request->input('estado'),
                'user_id' => Auth::id(), // ID del usuario logueado
            ];

            // Obtener artículos filtrados por usuario desde el servicio
            $articulos = $this->listadoArticulosService->getArticulos($filters);

            // Calcular paginación
            $totalItems = $articulos->count();
            $totalPages = ceil($totalItems / $limit);
            $articulosPaginados = $articulos->forPage($currentPage, $limit)->values();

            \Log::info('Artículos paginados desde ListadoArticulosController', [
                'total_items' => $totalItems,
                'current_page' => $currentPage,
                'limit' => $limit,
                'total_pages' => $totalPages,
                'user_id' => $filters['user_id'],
            ]);

            return view('dashboard.listado_articulos', [
                'articulos' => $articulosPaginados,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'limit' => $limit,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en ListadoArticulosController: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('dashboard.listado_articulos', [
                'articulos' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'limit' => 50,
            ])->with('error', $e->getMessage());
        }
    }
}
