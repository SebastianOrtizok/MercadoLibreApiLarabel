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

                // Datos locales de articulos
                $currentPrice = $product->precio;
                $originalPrice = $product->precio_original ?? $currentPrice;
                $currentDealIds = json_decode($product->deal_ids ?? '[]', true);

                Log::info("Procesando {$itemId}: precio={$currentPrice}, precio_original={$originalPrice}, deal_ids=" . json_encode($currentDealIds));

                // Consultar promociones
                $promoResponse = Http::withToken($accessToken)->get("https://api.mercadolibre.com/seller-promotions/items/{$itemId}?app_version=v2");
                $promotionData = $promoResponse->json();
                Log::info("Promociones para {$itemId}: " . json_encode($promotionData));

                if ($promoResponse->successful() && is_array($promotionData)) {
                    // Limpiar registros previos para este ítem
                    DB::table('item_promotions')->where('ml_product_id', $itemId)->delete();

                    if (empty($promotionData)) {
                        // Sin promociones: registrar como "Sin Promoción"
                        DB::table('item_promotions')->insert([
                            'ml_product_id' => $itemId,
                            'promotion_id' => 'Sin Promoción',
                            'type' => 'N/A',
                            'status' => 'N/A',
                            'original_price' => $originalPrice,
                            'new_price' => $currentPrice,
                            'start_date' => null,
                            'finish_date' => null,
                            'name' => 'Sin Promoción',
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]);

                        $updateData = [
                            'deal_ids' => '[]',
                            'en_promocion' => 0,
                            'descuento_porcentaje' => null,
                            'updated_at' => now(),
                        ];
                        // Si hay descuento local, mantenerlo
                        if ($originalPrice && $originalPrice > $currentPrice) {
                            $updateData['en_promocion'] = 1;
                            $updateData['descuento_porcentaje'] = round((($originalPrice - $currentPrice) / $originalPrice) * 100, 2);
                        }
                        DB::table('articulos')
                            ->where('ml_product_id', $itemId)
                            ->update($updateData);

                        $promotions[$itemId] = ['status' => 'No promotions'];
                    } else {
                        $dealIds = [];
                        $hasActivePromotion = false;
                        $newPrice = $currentPrice;
                        $newOriginalPrice = $originalPrice;

                        foreach ($promotionData as $index => $promo) {
                            $promoId = $promo['id'] ?? $promo['ref_id'] ?? substr(md5($itemId . $index . json_encode($promo)), 0, 50);
                            $offer = $promo['offers'][0] ?? null;

                            $promoOriginalPrice = $promo['original_price'] ?? $originalPrice;
                            $promoNewPrice = $offer['new_price'] ?? $promo['price'] ?? $currentPrice;

                            $startDate = isset($promo['start_date']) ? Carbon::parse($promo['start_date'])->toDateTimeString() : null;
                            $finishDate = isset($promo['finish_date']) ? Carbon::parse($promo['finish_date'])->toDateTimeString() : null;

                            DB::table('item_promotions')->insert([
                                'ml_product_id' => $itemId,
                                'promotion_id' => $promoId,
                                'type' => $promo['type'] ?? 'N/A',
                                'status' => $promo['status'] ?? 'N/A',
                                'original_price' => $promoOriginalPrice,
                                'new_price' => $promoNewPrice,
                                'start_date' => $startDate,
                                'finish_date' => $finishDate,
                                'name' => $promo['name'] ?? 'N/A',
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]);

                            $promotions[$itemId][] = [
                                'type' => $promo['type'] ?? 'N/A',
                                'status' => $promo['status'] ?? 'N/A',
                                'original_price' => $promoOriginalPrice,
                                'new_price' => $promoNewPrice,
                                'start_date' => $startDate,
                                'finish_date' => $finishDate,
                                'name' => $promo['name'] ?? 'N/A',
                            ];

                            if ($promo['id']) {
                                $dealIds[] = $promo['id'];
                            }
                            if ($promo['status'] === 'started') {
                                $hasActivePromotion = true;
                                $newPrice = $promoNewPrice;
                                $newOriginalPrice = $promoOriginalPrice;
                            }
                        }

                        $discountPercentage = $newOriginalPrice && $newPrice < $newOriginalPrice
                            ? round((($newOriginalPrice - $newPrice) / $newOriginalPrice) * 100, 2)
                            : null;

                        DB::table('articulos')
                            ->where('ml_product_id', $itemId)
                            ->update([
                                'precio' => $newPrice,
                                'precio_original' => $newOriginalPrice,
                                'deal_ids' => json_encode($dealIds),
                                'en_promocion' => $hasActivePromotion ? 1 : ($newPrice < $newOriginalPrice ? 1 : 0),
                                'descuento_porcentaje' => $discountPercentage,
                                'updated_at' => now(),
                            ]);
                    }
                } else {
                    Log::warning("Error en promociones para {$itemId}: " . $promoResponse->body());
                    $promotions[$itemId] = ['error' => $promoResponse->body()];
                }

                usleep(250000); // 0.25s entre requests
            }

            return $promotions;
        } catch (\Exception $e) {
            Log::error("Excepción en syncItemPromotions: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
