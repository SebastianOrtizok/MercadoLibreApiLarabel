<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CompetidorXCategoriaService;
use App\Services\MercadoLibreService;

class CompetidorXCategoriaController extends Controller
{
    protected $service;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->service = new CompetidorXCategoriaService($mercadoLibreService);
    }

    public function index(Request $request)
    {
        try {
            \Log::info("Iniciando CompetidorXCategoriaController::index");
            $categories = $this->service->getCategories();
            \Log::info("Categorías obtenidas: " . count($categories));
            $items = [];
            $total = 0;
            $categoryId = $request->input('category_id');
            $error = null;

            if ($request->isMethod('post')) {
                \Log::info("Solicitud POST recibida", ['category_id' => $request->category_id]);
                $request->validate([
                    'category_id' => 'required|string'
                ]);

                $userId = auth()->id();
                if (!$userId) {
                    throw new \Exception('Usuario no autenticado');
                }
                \Log::info("Usuario autenticado", ['user_id' => $userId]);

                $mlAccountId = \App\Models\MercadoLibreToken::where('user_id', $userId)->first()->ml_account_id ?? null;
                if (!$mlAccountId) {
                    throw new \Exception('Cuenta de MercadoLibre no vinculada');
                }
                \Log::info("Cuenta de MercadoLibre vinculada", ['ml_account_id' => $mlAccountId]);

                $limit = $request->input('limit', 50);
                $offset = $request->input('offset', 0);
                \Log::info("Parámetros de la solicitud", ['limit' => $limit, 'offset' => $offset]);

                $data = $this->service->getItemsByCategory(
                    $userId,
                    $mlAccountId,
                    $request->category_id,
                    $limit,
                    $offset
                );
                \Log::info("Resultado del servicio", ['items_count' => count($data['items'] ?? []), 'total' => $data['total'] ?? 0]);

                $items = $data['items'] ?? [];
                $total = $data['total'] ?? 0;
            } else {
                \Log::info("Solicitud no es POST, mostrando formulario");
            }

            return view('competidores.CompetidorXCategoriaVista', compact('categories', 'items', 'total', 'categoryId', 'error'));
        } catch (\Exception $e) {
            $error = "No se pudo obtener el listado de ítems: " . $e->getMessage();
            \Log::error("Error en CompetidorXCategoriaController: " . $e->getMessage());
            return view('competidores.CompetidorXCategoriaVista', compact('categories', 'items', 'total', 'categoryId', 'error'));
        }
    }
}
