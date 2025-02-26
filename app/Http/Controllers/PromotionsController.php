<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\PromotionsService;
use App\Models\MercadoLibreToken;
use Illuminate\Support\Facades\Log;

class PromotionsController {
    private $promotionsService;

    public function __construct(PromotionsService $promotionsService)
    {
        $this->promotionsService = $promotionsService;
    }

    public function promotions(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $accounts = MercadoLibreToken::where('user_id', $userId)->get();

            if ($accounts->isEmpty()) {
                return response()->json(['error' => 'El usuario no tiene cuentas asociadas.'], 400);
            }

            $allPromotions = [];
            $totalDiscount = 0;
            $discountCount = 0;

            foreach ($accounts as $account) {
                $accessToken = $account->access_token;
                $promotionsData = $this->promotionsService->getAllPromotions($account->ml_account_id, $accessToken);
                if (!isset($promotionsData['results'])) {
                    continue;
                }

                foreach ($promotionsData['results'] as $promotion) {
                    $discount = $promotion['discount_percentage'] ?? null;
                    if ($discount) {
                        $totalDiscount += $discount;
                        $discountCount++;
                    }

                    $allPromotions[] = [
                        'id' => $promotion['id'],
                        'title' => $promotion['name'] ?? 'Sin título',
                        'type' => $promotion['type'],
                        'start_date' => isset($promotion['start_date']) ? Carbon::parse($promotion['start_date'])->format('d M Y') : null,
                        'end_date' => isset($promotion['finish_date']) ? Carbon::parse($promotion['finish_date'])->format('d M Y') : null,
                        'status' => $promotion['status'],
                        'discount' => $discount,
                    ];
                }
            }

            $averageDiscount = $discountCount > 0 ? $totalDiscount / $discountCount : 0;

            return view('dashboard.promotions', [
                'promotions' => $allPromotions,
                'averageDiscount' => round($averageDiscount, 2)
            ]);
        } catch (\Exception $e) {
            Log::error("Error al obtener promociones: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
