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
            $categories = $this->service->getCategories();
            $analysis = null;
            $error = null;

            if ($request->isMethod('post')) {
                $request->validate([
                    'category_id' => 'required|string'
                ]);

                $userId = auth()->id();
                if (!$userId) {
                    throw new \Exception('Usuario no autenticado');
                }

                $mlAccountId = \App\Models\MercadoLibreToken::where('user_id', $userId)->first()->ml_account_id ?? null;
                if (!$mlAccountId) {
                    throw new \Exception('Cuenta de MercadoLibre no vinculada');
                }

                $analysis = $this->service->analyzeCompetitors(
                    $userId,
                    $mlAccountId,
                    $request->category_id
                );
            }

            return view('competidores.CompetidorXCategoriaVista', compact('categories', 'analysis', 'error'));
        } catch (\Exception $e) {
            $error = "No se pudo realizar el análisis. Por favor, intenta de nuevo más tarde.";
            \Log::error("Error en CompetidorXCategoriaController: " . $e->getMessage());
            return view('competidores.CompetidorXCategoriaVista', compact('categories', 'analysis', 'error'));
        }
    }
}
