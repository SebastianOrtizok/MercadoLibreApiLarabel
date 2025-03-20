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

                // Consultar datos del ítem desde /items/{id}
                $itemResponse = Http::withToken($accessToken)->get("https://api.mercadolibre.com/items/{$itemId}");
                $itemData = $itemResponse->json();
                Log::info("Datos de /items para {$itemId}: " . json_encode($itemData));

                if ($itemResponse->successful()) {
                    DB::table('articulos')
                        ->where('ml_product_id', $itemId)
                        ->update([
                            'precio' => $itemData['price'] ?? $product->precio,
                            'precio_original' => $itemData['original_price'] ?? $itemData['price'] ?? $product->precio_original,
                            'deal_ids' => json_encode($itemData['deal_ids'] ?? []), // Siempre array, nunca null
                            'updated_at' => now(),
                        ]);
                }

                // Consultar promociones
                $promoResponse = Http::withToken($accessToken)->get("https://api.mercadolibre.com/seller-promotions/items/{$itemId}?app_version=v2");
                $promotionData = $promoResponse->json();
                Log::info("Promociones para {$itemId}: " . json_encode($promotionData));

                if ($promoResponse->successful() && is_array($promotionData)) {
                    if (empty($promotionData)) {
                        DB::table('item_promotions')->where('ml_product_id', $itemId)->delete();
                        $promotions[$itemId] = ['status' => 'No promotions'];
                    } else {
                        foreach ($promotionData as $index => $promo) {
                            $promoId = $promo['id'] ?? $promo['ref_id'] ?? substr(md5($itemId . $index . json_encode($promo)), 0, 50);
                            $offer = $promo['offers'][0] ?? null;

                            $originalPrice = $promo['original_price'] ?? $itemData['original_price'] ?? $itemData['price'] ?? $product->precio_original;
                            $newPrice = $offer['new_price'] ?? $promo['price'] ?? $itemData['price'] ?? $product->precio;

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
                    }
                } else {
                    Log::warning("Error en promociones para {$itemId}: " . $promoResponse->body());
                    $promotions[$itemId] = ['error' => $promoResponse->body()];
                }

                usleep(500000);
            }

            return $promotions;
        } catch (\Exception $e) {
            Log::error("Excepción en syncItemPromotions: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
