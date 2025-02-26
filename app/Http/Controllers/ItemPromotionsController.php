<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ItemPromotionsService;
use App\Models\MercadoLibreToken;
use App\Models\Articulo; // Modelo de la tabla articulos
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemPromotionsController
{
    private $itemPromotionsService;

    // Inyectamos el servicio en el constructor
    public function __construct(ItemPromotionsService $itemPromotionsService)
    {
        $this->itemPromotionsService = $itemPromotionsService;
    }

    // Método para obtener y mostrar las promociones del ítem
    public function promotions(Request $request)
    {
        try {
            $userId = auth()->user()->id;

            // Obtener todas las cuentas de MercadoLibre asociadas al usuario
            $mlAccounts = DB::table('mercadolibre_tokens')
                ->where('user_id', $userId)
                ->select('ml_account_id', 'access_token')
                ->get(); // Obtenemos una colección de cuentas con access_tokens

            // Verificamos si el usuario tiene cuentas asociadas
            if ($mlAccounts->isEmpty()) {
                return response()->json(['error' => 'El usuario no tiene cuentas asociadas.'], 400);
            }

            // Seleccionar la primera cuenta disponible
            $mlAccount = $mlAccounts->first();  // Obtener el primer elemento de la colección
            $mlAccountId = $mlAccount->ml_account_id; // Acceder al ml_account_id
            $accessToken = $mlAccount->access_token; // Acceder al access_token

            $mlAccountIds = $mlAccounts->pluck('ml_account_id');
            $products = DB::table('articulos')
            ->where(function ($query) {
                $query->where('estado', 'active')
                ->whereNotNull('deal_ids')
                ->whereRaw('JSON_LENGTH(deal_ids) > 0');;  // deal_ids no debe ser NULL
      })
            ->orWhereNotNull('precio_original') // Agregamos la condición para 'precio_original' distinto de NULL
            ->select('ml_product_id', 'user_id', 'precio', 'precio_original','deal_ids')
            ->take(10)
            ->get();
            // Si no hay productos, terminamos aquí
            if (empty($products)) {
                return response()->json(['error' => 'No se encontraron productos para el usuario.'], 400);
            }

            // Convertimos los IDs de los productos a string separado por comas
            $productIdsArray = $products; // Mantener el array


            // Obtener promociones para estos productos (asumo que esto llama a la API de MercadoLibre)
            $promotionsData = $this->itemPromotionsService->getMultipleItemPromotions($productIdsArray, $accessToken);

            // Procesar las promociones
            $allItemPromotions = [];
            foreach ($promotionsData as $itemId => $promotions) {
                // Verificamos si hay un error en la respuesta
                if (isset($promotions['error'])) {
                    continue;
                } else {
                    // Si hay promociones, procesamos cada una
                    foreach ($promotions as $promotion) {
                        // Si hay un array de ofertas, procesamos la primera oferta
                        if (isset($promotion['offers']) && count($promotion['offers']) > 0) {
                            $offer = $promotion['offers'][0];  // Tomamos la primera oferta
                            $originalPrice = $offer['original_price'] ?? null;
                            $newPrice = $offer['new_price'] ?? null;
                            $benefits = isset($offer['benefits']) ? $offer['benefits'] : null; // Obtenemos los beneficios si existen
                        } else {
                            // Si no hay ofertas, intentamos obtener el precio directamente
                            $originalPrice = $promotion['original_price'] ?? null;
                            $newPrice = $promotion['new_price'] ?? ($promotion['price'] ?? null);  // Usamos 'price' como fallback
                            $benefits = isset($promotion['benefits']) ? $promotion['benefits'] : null; // Obtenemos los beneficios si existen
                        }

                        $allItemPromotions[] = [
                            'itemId' => $itemId,
                            'type' => $promotion['type'] ?? 'Desconocido',
                            'status' => $promotion['status'] ?? 'Desconocido',
                            'original_price' => $originalPrice,
                            'new_price' => $newPrice,
                            'start_date' => isset($promotion['start_date']) ? \Carbon\Carbon::parse($promotion['start_date'])->format('d M Y') : null,
                            'finish_date' => isset($promotion['finish_date']) ? \Carbon\Carbon::parse($promotion['finish_date'])->format('d M Y') : null,
                            'name' => $promotion['name'] ?? 'Sin nombre',
                            'benefits' => $benefits,  // Incluir beneficios si existen
                        ];
                    }
                }
            }

            // En este punto, $allItemPromotions contiene tanto las promociones como los errores

            // Si no hay promociones, devolvemos error
            if (empty($allItemPromotions)) {
                return response()->json(['error' => 'No se encontraron promociones para los productos del usuario.'], 400);
            }

            // Devolvemos las promociones
            return view('dashboard.item_promotions', [
                'itemPromotions' => $allItemPromotions,
            ]);

        } catch (\Exception $e) {
            Log::error("Error al obtener las promociones del ítem: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
