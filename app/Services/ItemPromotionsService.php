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

            foreach ($products as $product) {
                $itemId = $product->ml_product_id;
                $url = "https://api.mercadolibre.com/seller-promotions/items/{$itemId}?app_version=v2";

                // Log de datos existentes en item_promotions
                $existingPromos = DB::table('item_promotions')->where('ml_product_id', $itemId)->get();
                Log::info("Datos existentes en item_promotions para {$itemId}: " . $existingPromos->toJson());

                $response = Http::withToken($accessToken)
                    ->timeout(10)
                    ->get($url);

                $responseBody = $response->body();
                Log::info("Respuesta cruda API para {$itemId}: " . $responseBody);
                $promotionData = $response->json();
                Log::info("Respuesta parseada para {$itemId}: " . json_encode($promotionData));
                Log::info("Datos de articulos para {$itemId}: precio_original={$product->precio_original}, precio={$product->precio}, imagen={$product->imagen}, permalink={$product->permalink}");

                if ($response->successful()) {
                    if (!is_array($promotionData)) {
                        Log::warning("promotionData no es array para {$itemId}: " . json_encode($promotionData));
                        $promotions[$itemId] = ['error' => 'Respuesta inválida: ' . json_encode($promotionData)];
                        continue;
                    }

                    foreach ($promotionData as $index => $promo) {
                        $promoId = $promo['id'] ?? $promo['ref_id'] ?? substr(md5($itemId . $index . json_encode($promo)), 0, 50);
                        $offer = $promo['offers'][0] ?? null;

                        $originalPrice = $product->precio_original ?? $offer['original_price'] ?? $promo['price'] ?? null;
                        $newPrice = $offer['new_price'] ?? $promo['price'] ?? $product->precio ?? null;

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
            }

            return $promotions;
        } catch (\Exception $e) {
            Log::error("Excepción en syncItemPromotions: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
