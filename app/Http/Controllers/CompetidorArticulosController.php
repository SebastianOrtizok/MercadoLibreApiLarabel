<?php

namespace App\Http\Controllers;

use App\Models\Competidor;
use App\Models\ItemCompetidor;
use App\Services\CompetidorArticulosService;
use Illuminate\Http\Request;

class CompetidorArticulosController extends Controller
{
    protected $competidorArticulosService;

    public function __construct(CompetidorArticulosService $competidorArticulosService)
    {
        $this->competidorArticulosService = $competidorArticulosService;
    }

public function index(Request $request)
    {
        $userId = auth()->id();
        $competidores = Competidor::where('user_id', $userId)->get();

        $query = ItemCompetidor::with('competidor') // Carga la relación con competidores para acceder a nickname
            ->whereIn('competidor_id', $competidores->pluck('id'));

        // Aplicar filtros
        if ($request->has('nickname') && !empty($request->input('nickname'))) {
            $query->whereHas('competidor', function ($q) use ($request) {
                $q->where('nickname', 'like', '%' . $request->input('nickname') . '%');
            });
        }

        if ($request->has('titulo') && !empty($request->input('titulo'))) {
            $query->where('titulo', 'like', '%' . $request->input('titulo') . '%');
        }

        if ($request->has('es_full') && $request->input('es_full') !== null) {
            $query->where('es_full', $request->input('es_full') === '1');
        }

        if ($request->has('following') && $request->input('following') !== null) {
            $query->where('following', $request->input('following') === '1');
        }

        if ($request->has('order_by') && in_array($request->input('order_by'), ['precio', 'precio_descuento', 'ultima_actualizacion'])) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('order_by'), $direction);
        }

        $items = $query->paginate(10); // Paginar los resultados (ajusta el número según necesites)

        return view('competidores.index', compact('items', 'competidores'));
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
