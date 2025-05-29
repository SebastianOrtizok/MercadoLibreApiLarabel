<?php

namespace App\Http\Controllers;

use App\Exports\ItemsCompetidoresExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportItemsCompetidores(Request $request)
    {
        try {
            $userId = auth()->id();

            Log::info("Iniciando exportación de ítems competidores", [
                'user_id' => $userId,
                'request' => $request->all(),
            ]);

            // Consulta con JOIN para combinar competidores e items_competidores
            $items = DB::table('items_competidores as ic')
                ->join('competidores as c', 'ic.competidor_id', '=', 'c.id')
                ->where('c.user_id', $userId)
                ->where('ic.following', true) // Filtrar ítems con following: true
                ->select(
                    'c.nombre as competidor_nombre',
                    'c.seller_id as competidor_seller_id',
                    'ic.item_id',
                    'ic.titulo',
                    'ic.precio',
                    'ic.precio_descuento',
                    'ic.precio_sin_impuestos',
                    'ic.info_cuotas',
                    'ic.url',
                    'ic.es_full',
                    'ic.cantidad_disponible',
                    'ic.cantidad_vendida',
                    'ic.envio_gratis',
                    'ic.following',
                    'ic.ultima_actualizacion'
                )
                ->orderBy('ic.ultima_actualizacion', 'desc')
                ->get();

            Log::info("Ítems obtenidos para exportación", [
                'user_id' => $userId,
                'item_count' => $items->count(),
                'item_ids' => $items->pluck('item_id')->toArray(),
            ]);

            if ($items->isEmpty()) {
                Log::warning("No se encontraron ítems para exportar", [
                    'user_id' => $userId,
                ]);
                return redirect()->route('competidores.index')->with('error', 'No hay ítems seleccionados para exportar.');
            }

            // Verificar datos para depuración
            foreach ($items as $item) {
                if (!$item->competidor_nombre) {
                    Log::warning("Ítem sin nombre de competidor", [
                        'item_id' => $item->item_id,
                        'competidor_id' => $item->competidor_id,
                    ]);
                }
                if (!$item->ultima_actualizacion) {
                    Log::warning("Ítem sin última actualización", [
                        'item_id' => $item->item_id,
                    ]);
                }
            }

            return Excel::download(new ItemsCompetidoresExport($items), 'publicaciones_descargadas.xlsx');
        } catch (\Exception $e) {
            Log::error("Error en ExportController@exportItemsCompetidores", [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('competidores.index')->with('error', 'Error al exportar: ' . $e->getMessage());
        }
    }
}
