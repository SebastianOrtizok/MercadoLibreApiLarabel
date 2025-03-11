<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SkuService;
use Illuminate\Support\Facades\DB;

class SkuController extends Controller
{
    protected $skuService;

    public function __construct(SkuService $skuService)
    {
        $this->skuService = $skuService;
    }

    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 50); // Cantidad por página
            $currentPage = (int) $request->input('page', 1); // Página actual

            $filters = [
                'search' => $request->input('search'),
                'ml_account_id' => $request->input('ml_account_id'),
                'tipo_publicacion' => $request->input('tipo_publicacion'),
                'estado_publicacion' => $request->input('estado_publicacion'),
            ];

            // Obtener todos los productos desde el servicio
            $productos = $this->skuService->getProductos($filters);

            // Calcular paginación
            $totalItems = $productos->count();
            $totalPages = ceil($totalItems / $limit);
            $productosPaginados = $productos->forPage($currentPage, $limit)->values();

            \Log::info('Productos paginados desde SkuController', [
                'total_items' => $totalItems,
                'current_page' => $currentPage,
                'limit' => $limit,
                'total_pages' => $totalPages,
                'paginados_count' => $productosPaginados->count(),
                'sample' => $productosPaginados->first()
            ]);

            return view('dashboard.sku', [
                'productos' => $productosPaginados,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'limit' => $limit,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en SkuController: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('dashboard.sku', [
                'productos' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'limit' => 50,
            ])->with('error', $e->getMessage());
        }
    }

    public function updateSku(Request $request)
    {
        try {
            $request->validate([
                'ml_product_id' => 'required|string',
                'sku_interno' => 'nullable|string|max:255',
            ]);

            $updated = DB::table('articulos')
                ->where('ml_product_id', $request->ml_product_id)
                ->update(['sku_interno' => $request->sku_interno]);

            if ($updated) {
                return redirect()->back()->with('success', 'SKU interno actualizado correctamente.');
            } else {
                return redirect()->back()->with('error', 'No se encontró el artículo o no se pudo actualizar.');
            }
        } catch (\Exception $e) {
            \Log::error('Error al actualizar SKU interno: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error al actualizar el SKU interno: ' . $e->getMessage());
        }
    }
}
