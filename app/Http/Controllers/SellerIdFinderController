<?php

namespace App\Http\Controllers;

use App\Services\SellerIdFinderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SellerIdFinderController extends Controller
{
    protected $sellerIdFinderService;

    public function __construct(SellerIdFinderService $sellerIdFinderService)
    {
        $this->sellerIdFinderService = $sellerIdFinderService;
    }

    public function findSellerId(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string',
        ]);

        $nickname = $request->input('nickname');
        $sellerId = $this->sellerIdFinderService->findSellerIdByNickname($nickname);

        if ($sellerId) {
            return response()->json(['success' => true, 'seller_id' => $sellerId]);
        }

        return response()->json(['success' => false, 'message' => 'No se encontró el Seller ID para el nickname proporcionado.'], 404);
    }
}
