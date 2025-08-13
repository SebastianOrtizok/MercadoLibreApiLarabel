<?php

namespace App\Http\Controllers;

use App\Models\Competidor;
use App\Models\ItemCompetidor;
use App\Services\CompetidorService; // Cambiado de CompetidorArticulosService
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompetidorArticulosController extends Controller
{
    protected $competidorService;

    public function __construct(CompetidorService $competidorService)
    {
        try {
            $this->competidorService = $competidorService;
            if (!auth()->check()) {
                \Log::warning('Usuario no autenticado en el constructor');
                throw new \Exception('Usuario no autenticado');
            }
            \Log::info('Constructor ejecutado con éxito', ['service' => get_class($competidorService)]);
        } catch (\Exception $e) {
            \Log::error('Error en el constructor', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function filtrarCompetidores(Request $request)
    {
        try {
            Log::info('Entrando al método filtrarCompetidores', ['url' => $request->url(), 'params' => $request->all()]);

            $userId = auth()->id();
            if (!$userId) {
                Log::warning('No se obtuvo user_id');
                throw new \Exception('Usuario no autenticado o ID no disponible');
            }

            $competidores = Competidor::where('user_id', $userId)->get();
            Log::info('Competidores encontrados', ['user_id' => $userId, 'count' => $competidores->count(), 'ids' => $competidores->pluck('id')->toArray()]);

            if ($competidores->isEmpty()) {
                Log::warning('No se encontraron competidores para el usuario', ['user_id' => $userId]);
                $items = collect();
                $currentPage = 1;
                $totalPages = 1;
                $limit = 10;
            } else {
                $query = ItemCompetidor::with('competidor')->whereIn('competidor_id', $competidores->pluck('id'));

                if ($request->has('nickname') && !empty($request->input('nickname'))) {
                    $query->whereHas('competidor', function ($q) use ($request) {
                        $q->where('nickname', 'like', '%' . $request->input('nickname') . '%');
                        Log::info('Filtro nickname aplicado', ['nickname' => $request->input('nickname')]);
                    });
                }

                if ($request->has('titulo') && !empty($request->input('titulo'))) {
                    $query->where('titulo', 'like', '%' . $request->input('titulo') . '%');
                    Log::info('Filtro título aplicado', ['titulo' => $request->input('titulo')]);
                }

                if ($request->has('categorias') && !empty($request->input('categorias'))) {
                    $query->where('categorias', 'like', '%' . $request->input('categorias') . '%');
                    Log::info('Filtro categorías aplicado', ['categorias' => $request->input('categorias')]);
                }

                if ($request->has('es_full') && $request->input('es_full') !== null) {
                    $query->where('es_full', filter_var($request->input('es_full'), FILTER_VALIDATE_BOOLEAN));
                    Log::info('Filtro es_full aplicado', ['es_full' => $request->input('es_full')]);
                }

                if ($request->has('following') && $request->input('following') !== null) {
                    $query->where('following', filter_var($request->input('following'), FILTER_VALIDATE_BOOLEAN));
                    Log::info('Filtro following aplicado', ['following' => $request->input('following')]);
                }

                if ($request->has('order_by') && in_array($request->input('order_by'), ['precio', 'precio_descuento', 'ultima_actualizacion'])) {
                    $direction = $request->input('direction', 'asc');
                    $query->orderBy($request->input('order_by'), $direction);
                    Log::info('Ordenamiento aplicado', ['order_by' => $request->input('order_by'), 'direction' => $direction]);
                }

                Log::info('Consulta final antes de paginar', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
                $items = $query->paginate(10);
                $currentPage = $items->currentPage();
                $totalPages = $items->lastPage();
                $limit = $items->perPage();
            }

            return view('competidores.index', compact('items', 'competidores', 'currentPage', 'totalPages', 'limit'));
        } catch (\Exception $e) {
            Log::error('Error en filtrarCompetidores', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'user_id' => $userId ?? 'No disponible'
            ]);
            return redirect()->back()->with('error', 'Error al procesar los filtros: ' . $e->getMessage());
        }
    }

    public function actualizar(Request $request)
    {
        \Log::info("Solicitud recibida en CompetidorArticulosController@actualizar", [
            'request' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        $competidor = Competidor::where('user_id', auth()->id())
            ->findOrFail($request->competidor_id);

        $categoria = $request->input('categoria'); // Obtener la categoría seleccionada

        \Log::info("Iniciando actualización de competidor", [
            'competidor_id' => $competidor->id,
            'seller_id' => $competidor->seller_id,
            'nickname' => $competidor->nickname,
            'official_store_id' => $competidor->official_store_id,
            'categoria_seleccionada' => $categoria,
        ]);

        try {
            $items = $this->competidorService->scrapeItemsBySeller(
                $competidor->seller_id,
                strtolower($competidor->nickname),
                $competidor->official_store_id,
                $categoria
            );

            \Log::info("Ítems scrapeados para el competidor", [
                'competidor_id' => $competidor->id,
                'total_items' => count($items),
                'sample_items' => array_slice($items, 0, 5),
            ]);

            if (empty($items)) {
                \Log::warning("No se encontraron ítems para actualizar", [
                    'competidor_id' => $competidor->id,
                ]);
                return redirect()->route('competidores.index')->with('error', 'No se encontraron ítems para actualizar.');
            }

            $totalUpdated = 0;
            foreach ($items as $itemData) {
                $updatedItem = ItemCompetidor::updateOrCreate(
                    [
                        'competidor_id' => $competidor->id,
                        'item_id' => $itemData['item_id'],
                    ],
                    [
                        'titulo' => $itemData['titulo'],
                        'precio' => $itemData['precio'],
                        'precio_descuento' => $itemData['precio_descuento'],
                        'info_cuotas' => $itemData['info_cuotas'],
                        'url' => $itemData['url'],
                        'es_full' => $itemData['es_full'],
                        'envio_gratis' => $itemData['envio_gratis'],
                        'ultima_actualizacion' => now(),
                        'categorias' => $itemData['categorias'], // Usar la categoría scrapeada o la seleccionada
                    ]
                );

                \Log::info("Ítem actualizado o creado", [
                    'competidor_id' => $competidor->id,
                    'item_id' => $itemData['item_id'],
                    'updated_item' => $updatedItem->toArray(),
                ]);
                $totalUpdated++;
            }

            return redirect()->route('competidores.index')->with('success', "Competidor actualizado con {$totalUpdated} ítems");
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
}
