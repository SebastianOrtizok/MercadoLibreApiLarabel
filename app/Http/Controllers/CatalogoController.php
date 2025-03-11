<?php

namespace App\Http\Controllers;

use App\Services\CatalogoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CatalogoController extends Controller
{
    protected $catalogoService;

    public function __construct(CatalogoService $catalogoService)
    {
        $this->catalogoService = $catalogoService;
    }

    public function index(Request $request)
    {
        try {
            $userId = auth()->id();
            Log::info('User ID autenticado para catálogo:', ['user_id' => $userId]);

            $filters = [
                'search' => $request->input('search'),
                'cuenta_ml' => $request->input('cuenta_ml'),
            ];
            $limit = $request->input('limit', 10);
            $currentPage = $request->input('page', 1);

            $result = $this->catalogoService->getArticulosEnCatalogo($userId, $filters, $limit, $currentPage);

            Log::info('Datos de catálogo generados:', [
                'total_articulos' => $result['total'],
                'current_page' => $currentPage,
                'total_pages' => $result['totalPages'],
                'sample' => $result['articulos']->isNotEmpty() ? $result['articulos']->first() : null
            ]);

            $cuentas_ml = DB::table('mercadolibre_tokens')
                ->where('user_id', $userId)
                ->pluck('seller_name')
                ->filter()
                ->unique()
                ->values()
                ->all();

            return view('dashboard.catalogo', [
                'articulos' => $result['articulos'],
                'totalArticulos' => $result['total'],
                'currentPage' => $currentPage,
                'totalPages' => $result['totalPages'],
                'limit' => $limit,
                'cuentas_ml' => $cuentas_ml,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en CatalogoController: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('dashboard.catalogo', [
                'articulos' => collect(),
                'totalArticulos' => 0,
                'currentPage' => 1,
                'totalPages' => 1,
                'limit' => 10,
                'cuentas_ml' => [],
            ]);
        }
    }

    public function competencia($mlProductId)
    {
        try {
            $userId = auth()->id();
            Log::info('Consultando competencia para artículo:', ['user_id' => $userId, 'ml_product_id' => $mlProductId]);

            $competencia = $this->catalogoService->getCompetenciaArticulo($userId, $mlProductId);

            return view('dashboard.catalogo-competencia', [
                'articulo' => $competencia['articulo'],
                'competencia' => $competencia['competencia']
            ]);
        } catch (\Exception $e) {
            Log::error('Error al consultar competencia: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('dashboard.catalogo-competencia', [
                'articulo' => null,
                'competencia' => null,
                'error' => 'No se pudo obtener los datos de competencia.'
            ]);
        }
    }
}
