<?php

namespace App\Services;

use App\Models\MercadoLibreToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PromotionsService
{
    /**
     * Obtiene todas las promociones activas de una cuenta de MercadoLibre.
     */
    public function getAllPromotions($sellerId, $accessToken)
    {
        try {
            // Construir la URL para obtener las promociones
            $url = "https://api.mercadolibre.com/seller-promotions/search?seller_id={$sellerId}&promotion_type=all";

            // Hacer la solicitud a la API de MercadoLibre con el access token
            $response = Http::withToken($accessToken)->get($url);

            if (!$response->successful()) {
                throw new \Exception("Error en la API de MercadoLibre: " . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Error al obtener promociones: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
