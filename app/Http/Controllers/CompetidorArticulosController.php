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
                       $competidor->official_store_id
                   );

                   if (empty($updatedData)) {
                       \Log::warning("No se pudieron obtener datos actualizados para el artículo", [
                           'item_id' => $item->item_id,
                       ]);
                       continue;
                   }

                   $item->update([
                       'titulo' => $updatedData['titulo'],
                       'precio' => $updatedData['precio'],
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
