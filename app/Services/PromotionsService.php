<?php

namespace App\Services;

use App\Models\MercadoLibreToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PromotionsService {
    public function getAllPromotions($sellerId, $accessToken)
    {
        try {
            $url = "https://api.mercadolibre.com/seller-promotions/users/{$sellerId}?app_version=v2";
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
