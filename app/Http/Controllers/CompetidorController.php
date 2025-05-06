<?php

namespace App\Http\Controllers;

use App\Jobs\ActualizarItemsCompetidor;
use App\Models\Competidor;
use App\Services\CompetidorXCategoriaService;
use Illuminate\Http\Request;

class CompetidorController extends Controller
{
    protected $competidorService;

    public function __construct(CompetidorXCategoriaService $competidorService)
    {
        $this->competidorService = $competidorService;
    }

    public function index()
    {
        $competidores = Competidor::where('user_id', auth()->id())->get();
        $items = \App\Models\ItemCompetidor::whereIn('competidor_id', $competidores->pluck('id'))
            ->with('competidor')
            ->get();

        $userId = auth()->id();
        $mlAccountId = auth()->user()->mercadolibreToken->ml_account_id ?? null;        $stats = [];

        if ($mlAccountId) {
            try {
                $result = $this->competidorService->getItemsByCategory($userId, $mlAccountId, 'MLA1051');
                $stats = $result['stats'];
            } catch (\Exception $e) {
                \Log::error('Error al obtener estadísticas', ['error' => $e->getMessage()]);
            }
        }

        return view('competidores.index', compact('competidores', 'items', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|string',
            'nickname' => 'required|string',
            'nombre' => 'required|string',
        ]);

        Competidor::create([
            'user_id' => auth()->id(),
            'seller_id' => $request->seller_id,
            'nickname' => $request->nickname,
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('competidores.index')->with('success', 'Competidor agregado');
    }

    public function actualizar(Request $request)
    {
        $competidor = Competidor::where('user_id', auth()->id())
            ->findOrFail($request->competidor_id);
        $userId = auth()->id();
        $mlAccountId = auth()->user()->mercadoLibreToken->ml_account_id ?? null;

        if (!$mlAccountId) {
            \Log::error('Cuenta de Mercado Libre no vinculada', ['user_id' => $userId]);
            return redirect()->route('competidores.index')->with('error', 'Vincula tu cuenta de Mercado Libre primero.');
        }

        try {
            // Pasamos la categoría al job (puedes cambiar 'MLA1051' por otra categoría)
            dispatch(new ActualizarItemsCompetidor($competidor, 'MLA1051'));
            return redirect()->route('competidores.index')->with('success', 'Actualización iniciada');
        } catch (\Exception $e) {
            \Log::error('Error al iniciar actualización', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'competidor_id' => $request->competidor_id,
            ]);
            return redirect()->route('competidores.index')->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $competidor = Competidor::where('user_id', auth()->id())
            ->findOrFail($request->competidor_id);
        $competidor->delete();

        return redirect()->route('competidores.index')->with('success', 'Competidor eliminado');
    }

    public function follow(Request $request)
    {
        $items = \App\Models\ItemCompetidor::whereIn('competidor_id', Competidor::where('user_id', auth()->id())->pluck('id'))->get();
        $followData = $request->follow ?? [];

        foreach ($followData as $itemId => $follow) {
            $item = $items->where('item_id', $itemId)->first();
            if ($item) {
                $item->update(['following' => $follow === 'yes']);
            }
        }

        return redirect()->route('competidores.index')->with('success', 'Seguimiento actualizado');
    }
}
