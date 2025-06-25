<?php
namespace App\Http\Controllers;

use App\Models\Competidor;
use App\Services\CompetidorService;
use Illuminate\Http\Request;
use App\Exports\ItemsCompetidoresExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Suscripcion; // Agregamos el modelo Suscripcion

class CompetidorController extends Controller
{
    protected $competidorService;

    public function __construct(CompetidorService $competidorService)
    {
        $this->competidorService = $competidorService;
    }

    public function index(Request $request)
    {
        $competidores = Competidor::where('user_id', auth()->id())->get();

        $items = \App\Models\ItemCompetidor::whereIn('competidor_id', $competidores->pluck('id'))
            ->with('competidor')
            ->orderBy('following', 'desc')
            ->paginate(10);

        $currentPage = $items->currentPage();
        $totalPages = $items->lastPage();
        $limit = $items->perPage();

        if ($request->has('export')) {
            $allItems = \App\Models\ItemCompetidor::whereIn('competidor_id', $competidores->pluck('id'))
                ->with('competidor')
                ->orderBy('following', 'desc')
                ->get();
            return Excel::download(new ItemsCompetidoresExport($allItems), 'publicaciones_descargadas.xlsx');
        }

        return view('competidores.index', compact('competidores', 'items', 'currentPage', 'totalPages', 'limit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|string',
            'nickname' => 'required|string',
            'nombre' => 'required|string',
            'official_store_id' => 'nullable|integer',
        ]);

        // Verificar el plan del usuario
        $suscripcion = Suscripcion::where('usuario_id', auth()->id())->latest()->first();
        $competidorCount = Competidor::where('user_id', auth()->id())->count();

        // Definir límites por plan
        $limits = [
            'mensual' => 5,
            'trimestral' => 15,
            'anual' => 60,
        ];

        if ($suscripcion && array_key_exists($suscripcion->plan, $limits)) {
            $planLimit = $limits[$suscripcion->plan];

            if ($competidorCount >= $planLimit) {
                return redirect()->route('competidores.index')->with('error', 'Has alcanzado el límite del plan. El tope de tu plan ' . $suscripcion->plan . ' es ' . $planLimit . ' competidores.');
            }
        }

        Competidor::create([
            'user_id' => auth()->id(),
            'seller_id' => $request->seller_id,
            'nickname' => $request->nickname,
            'nombre' => $request->nombre,
            'official_store_id' => $request->official_store_id,
        ]);

        return redirect()->route('competidores.index')->with('success', 'Competidor agregado');
    }

    public function actualizar(Request $request)
    {
        $competidor = Competidor::where('user_id', auth()->id())
            ->findOrFail($request->competidor_id);

        \Log::info("Iniciando actualización de competidor", [
            'competidor_id' => $competidor->id,
            'seller_id' => $competidor->seller_id,
            'nickname' => $competidor->nickname,
            'official_store_id' => $competidor->official_store_id,
        ]);

        try {
            $items = $this->competidorService->scrapeItemsBySeller(
                $competidor->seller_id,
                strtolower($competidor->nickname),
                $competidor->official_store_id
            );

            \Log::info("Ítems scrapeados para el competidor", [
                'competidor_id' => $competidor->id,
                'total_items' => count($items),
                'sample_items' => array_slice($items, 0, 500),
            ]);

            if (empty($items)) {
                \Log::warning("No se encontraron ítems para actualizar", [
                    'competidor_id' => $competidor->id,
                ]);
                return redirect()->route('competidores.index')->with('error', 'No se encontraron ítems para actualizar.');
            }

            foreach ($items as $itemData) {
                $updatedItem = \App\Models\ItemCompetidor::updateOrCreate(
                    [
                        'competidor_id' => $competidor->id,
                        'item_id' => $itemData['item_id'],
                    ],
                    [
                        'titulo' => $itemData['titulo'],
                        'precio' => number_format($itemData['precio'], 2, '.', ''),
                        'precio_descuento' => number_format($itemData['precio_descuento'], 2, '.', ''),
                        'info_cuotas' => $itemData['info_cuotas'],
                        'url' => $itemData['url'],
                        'es_full' => $itemData['es_full'],
                        'envio_gratis' => $itemData['envio_gratis'],
                        'ultima_actualizacion' => now(),
                        'cantidad_disponible' => 0,
                        'cantidad_vendida' => 0,
                    ]
                );

                \Log::info("Ítem actualizado o creado", [
                    'competidor_id' => $competidor->id,
                    'item_id' => $itemData['item_id'],
                    'updated_item' => $updatedItem->toArray(),
                ]);
            }

            return redirect()->route('competidores.index')->with('success', 'Competidor actualizado con ' . count($items) . ' ítems');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar competidor', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'competidor_id' => $request->competidor_id,
                'trace' => $e->getTraceAsString(),
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
    \Log::info("Solicitud recibida en CompetidorController@follow", [
        'request' => $request->all(),
        'user_id' => auth()->id(),
    ]);

    try {
        // Obtener los competidores del usuario autenticado
        $competidores = Competidor::where('user_id', auth()->id())->pluck('id');

        // Obtener el array de ítems seleccionados desde el formulario
        $followData = $request->input('follow', []);
        $selectedItemIds = array_keys(array_filter($followData, fn($value) => $value === 'yes'));

        \Log::info("Ítems seleccionados desde el formulario", [
            'follow_data' => $followData,
            'selected_item_ids' => $selectedItemIds,
        ]);

        // Actualizar solo los ítems seleccionados
        $updatedCount = 0;
        if (!empty($selectedItemIds)) {
            $updatedCount = \App\Models\ItemCompetidor::whereIn('competidor_id', $competidores)
                ->whereIn('item_id', $selectedItemIds)
                ->update(['following' => true]);

            \Log::info("Ítems marcados como seguidos", [
                'updated_count' => $updatedCount,
                'selected_item_ids' => $selectedItemIds,
            ]);
        }

        // Los ítems no incluidos en el formulario no se modifican
        \Log::info("Método follow completado", ['updated_count' => $updatedCount]);

        return redirect()->route('competidores.index')->with('success', "Se actualizaron $updatedCount publicaciones como seguidas.");
    } catch (\Exception $e) {
        \Log::error('Error al actualizar el seguimiento de ítems', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id(),
            'trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route('competidores.index')->with('error', 'Error al actualizar el seguimiento: ' . $e->getMessage());
    }
}
}
