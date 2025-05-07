<?php
namespace App\Http\Controllers;

use App\Models\Competidor;
use App\Services\CompetidorService;
use Illuminate\Http\Request;

class CompetidorController extends Controller
{
    protected $competidorService;

    public function __construct(CompetidorService $competidorService)
    {
        $this->competidorService = $competidorService;
    }

    public function index()
    {
        $competidores = Competidor::where('user_id', auth()->id())->get();
        $items = \App\Models\ItemCompetidor::whereIn('competidor_id', $competidores->pluck('id'))
            ->with('competidor')
            ->get();

        return view('competidores.index', compact('competidores', 'items'));
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

        try {
            // Realizar scraping directamente
            $items = $this->competidorService->scrapeItemsBySeller($competidor->seller_id);

            // Guardar los ítems en la base de datos
            foreach ($items as $itemData) {
                \App\Models\ItemCompetidor::updateOrCreate(
                    [
                        'competidor_id' => $competidor->id,
                        'item_id' => $itemData['item_id'],
                    ],
                    [
                        'titulo' => $itemData['titulo'],
                        'precio' => $itemData['precio'],
                        'ultima_actualizacion' => now(),
                        'cantidad_disponible' => 0,
                        'cantidad_vendida' => 0,
                        'envio_gratis' => false,
                    ]
                );
            }

            return redirect()->route('competidores.index')->with('success', 'Competidor actualizado con ' . count($items) . ' ítems');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar competidor', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
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
