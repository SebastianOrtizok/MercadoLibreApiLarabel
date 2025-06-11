<?php

namespace App\Http\Controllers;

use App\Models\Competidor;
use App\Models\ItemCompetidor;
use App\Services\CompetidorArticulosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompetidorArticulosController extends Controller
{
    protected $competidorArticulosService;

    public function __construct(CompetidorArticulosService $competidorArticulosService)
    {
        try {
            $this->competidorArticulosService = $competidorArticulosService;
            if (!auth()->check()) {
                Log::warning('Usuario no autenticado en el constructor');
                throw new \Exception('Usuario no autenticado');
            }
            Log::info('Constructor ejecutado con éxito', ['service' => get_class($competidorArticulosService)]);
        } catch (\Exception $e) {
            Log::error('Error en el constructor', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

public function index(Request $request)
{
    try {
        \Log::info('Entrando al método index', ['url' => $request->url(), 'params' => $request->all()]);

        $userId = auth()->id();
        if (!$userId) {
            \Log::warning('No se obtuvo user_id');
            throw new \Exception('Usuario no autenticado o ID no disponible');
        }

        $competidores = Competidor::where('user_id', $userId)->get();
        \Log::info('Competidores encontrados', ['user_id' => $userId, 'count' => $competidores->count(), 'ids' => $competidores->pluck('id')->toArray()]);

        $items = $competidores->isEmpty() ? collect() : ItemCompetidor::whereIn('competidor_id', $competidores->pluck('id'))->paginate(10);

        \Log::info('Items cargados', ['count' => $items->count()]);

        return view('competidores.index', compact('items', 'competidores'));
    } catch (\Exception $e) {
        \Log::error('Error en index', [
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

        $competidores = Competidor::where('user_id', auth()->id())->get();

        \Log::info("Iniciando actualización de artículos seleccionados para todos los competidores", [
            'user_id' => auth()->id(),
            'competidores' => $competidores->pluck('id')->toArray(),
        ]);

        try {
            $items = ItemCompetidor::whereIn('competidor_id', $competidores->pluck('id'))
                ->where('following', true)
                ->get();

            \Log::info("Ítems seleccionados para actualizar", [
                'count' => $items->count(),
                'item_ids' => $items->pluck('item_id')->toArray(),
            ]);

            if ($items->isEmpty()) {
                \Log::warning("No se encontraron artículos seleccionados para actualizar", [
                    'user_id' => auth()->id(),
                ]);
                return redirect()->route('competidores.index')->with('error', 'No hay artículos seleccionados para actualizar.');
            }

            foreach ($items as $item) {
                $competidor = $item->competidor;

                $updatedData = $this->competidorArticulosService->scrapeItemDetails(
                    $item->item_id,
                    $competidor->seller_id,
                    strtolower($competidor->nickname),
                    $item->url,
                    $competidor->official_store_id
                );

                if (empty($updatedData)) {
                    \Log::warning("No se pudieron obtener datos actualizados para el artículo", [
                        'item_id' => $item->item_id,
                        'url' => $item->url,
                    ]);
                    continue;
                }

                $item->update([
                    'titulo' => $updatedData['titulo'],
                    // Excluimos 'precio' para no actualizarlo
                    'precio_descuento' => $updatedData['precio_descuento'],
                    'info_cuotas' => $updatedData['info_cuotas'],
                    'url' => $updatedData['url'],
                    'es_full' => $updatedData['es_full'],
                    'envio_gratis' => $updatedData['envio_gratis'],
                    'precio_sin_impuestos' => $updatedData['precio_sin_impuestos'] ?? null,
                    'ultima_actualizacion' => now(),
                ]);

                \Log::info("Artículo actualizado", [
                    'item_id' => $item->item_id,
                    'url' => $item->url,
                    'updated_data' => $updatedData,
                ]);
            }

            return redirect()->route('competidores.index')->with('success', 'Datos de ' . $items->count() . ' artículos seleccionados actualizados.');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar artículos seleccionados', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('competidores.index')->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
}
