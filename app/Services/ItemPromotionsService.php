<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItemPromotionsService {

    // Método para obtener las promociones de múltiples ítems
    public function getMultipleItemPromotions(array $productIdsArray, $accessToken) // Cambié el nombre a productIdsArray
    {
        try {
            $promotions = [];

            foreach ($productIdsArray as $itemId) { // Asegurarse de que sea un array
                $url = "https://api.mercadolibre.com/seller-promotions/items/{$itemId}?app_version=v2";

                // Realizamos la solicitud a la API de MercadoLibre
                $response = Http::withToken($accessToken)->get($url);

                // Verificamos si la respuesta fue exitosa
                if ($response->successful()) {
                    $responseData = $response->json(); // Obtenemos la respuesta

                    // Agregamos el itemId a los datos de la respuesta
                    $responseData['itemId'] = $itemId;

                    $promotions[$itemId] = $responseData; // Guardamos la respuesta con el itemId

                } else {
                    Log::error("Error en la API de MercadoLibre para el item {$itemId}: " . $response->body());
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
