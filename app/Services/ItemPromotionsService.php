<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItemPromotionsService {

    // Método para obtener las promociones de múltiples ítems
    public function getMultipleItemPromotions($productIdsArray, $accessToken)
    {
        try {
            $promotions = [];

            // Limitar la cantidad de registros a 10 (por seguridad, no hacer demasiadas solicitudes)
            $productIdsArray = $productIdsArray->take(3); // Si es una colección, usamos ->take()

            foreach ($productIdsArray as $product) {
                // $product es un objeto, por lo que accedemos con -> y no con []

                $itemId = $product->ml_product_id; // Asegúrate de usar -> para acceder a las propiedades
                $url = "https://api.mercadolibre.com/seller-promotions/items/{$itemId}?app_version=v2";

                // Realizamos la solicitud a la API de MercadoLibre
                $response = Http::withToken($accessToken)->get($url);

                // Verificamos si la respuesta fue exitosa
                if ($response->successful()) {
                    $responseData = $response->json(); // Obtenemos la respuesta en formato JSON

                    // Si quieres agregar el precio y precio_original de la base de datos a la respuesta:
                    $responseData['precio'] = $product->precio;
                    $responseData['precio_original'] = $product->precio_original;

                    // Agregamos el itemId a los datos de la respuesta
                    $responseData['itemId'] = $itemId;

                    // Guardamos la respuesta con el itemId
                    $promotions[$itemId] = $responseData;
                } else {
                    $promotions[$itemId] = ['error' => $response->body(), 'itemId' => $itemId];
                }
            }
            
            return $promotions; // Devolvemos el array con todas las promociones obtenidas
        } catch (\Exception $e) {
            Log::error("Excepción en getMultipleItemPromotions: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

}
