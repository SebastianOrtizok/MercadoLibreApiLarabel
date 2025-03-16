<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemPromotionsService
{
    public function syncItemPromotions($products, $accessToken)
    {
        try {
            $promotions = [];
            $requestCount = 0;
            $maxRequests = 10; // Límite de 10 ítems para debug

            foreach ($products as $product) {
                if ($requestCount >= $maxRequests) {
                    Log::info("Límite de {$maxRequests} solicitudes alcanzado. Deteniendo...");
                    break;
                }

                $itemId = $product->ml_product_id;
                $url = "https://api.mercadolibre.com/seller-promotions/items/{$itemId}?app_version=v2";

                $response = Http::withToken($accessToken)
                    ->timeout(10)
                    ->get($url);

                $responseBody = $response->body();
                Log::info("Respuesta cruda API para {$itemId}: " . $responseBody);
                $promotionData = $response->json();
                Log::info("Respuesta parseada para {$itemId}: " . json_encode($promotionData));

                if ($response->successful()) {
                    if (!is_array($promotionData)) {
                        Log::warning("promotionData no es array para {$itemId}: " . json_encode($promotionData));
                        $promotions[$itemId] = ['error' => 'Respuesta inválida: ' . json_encode($promotionData)];
                        continue;
                    }

                    foreach ($promotionData as $index => $promo) {
                        $promoId = $promo['id'] ?? $promo['ref_id'] ?? substr(md5($itemId . $index . json_encode($promo)), 0, 50);
                        $offer = $promo['offers'][0] ?? null;

                        $originalPrice = $offer['original_price'] ?? $product->precio_original ?? $promo['price'] ?? null;
                        $newPrice = $offer['new_price'] ?? $promo['price'] ?? null;

                        $startDate = isset($promo['start_date']) ? Carbon::parse($promo['start_date'])->toDateTimeString() : null;
                        $finishDate = isset($promo['finish_date']) ? Carbon::parse($promo['finish_date'])->toDateTimeString() : null;

                        DB::table('item_promotions')->updateOrInsert(
                            [
                                'ml_product_id' => $itemId,
                                'promotion_id' => $promoId,
                            ],
                            [
                                'type' => $promo['type'] ?? null,
                                'status' => $promo['status'] ?? null,
                                'original_price' => $originalPrice,
                                'new_price' => $newPrice,
                                'start_date' => $startDate,
                                'finish_date' => $finishDate,
                                'name' => $promo['name'] ?? null,
                                'updated_at' => now(),
                            ]
                        );
                        $promotions[$itemId][] = [
                            'type' => $promo['type'] ?? null,
                            'status' => $promo['status'] ?? null,
                            'original_price' => $originalPrice,
                            'new_price' => $newPrice,
                            'start_date' => $startDate,
                            'finish_date' => $finishDate,
                            'name' => $promo['name'] ?? null,
                        ];
                    }
                } else {
                    Log::warning("Error API para {$itemId}: " . $responseBody);
                    $promotions[$itemId] = ['error' => $responseBody];
                }

                $requestCount++;
            }

            return $promotions;
        } catch (\Exception $e) {
            Log::error("Excepción en syncItemPromotions: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
