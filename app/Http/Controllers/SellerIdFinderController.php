<?php

namespace App\Http\Controllers;

use App\Services\SellerIdFinderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MercadoLibreToken;

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

        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuario no autenticado.'], 401);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener el token y el ml_account_id desde la tabla mercadolibre_tokens
        $mercadoLibreToken = MercadoLibreToken::where('user_id', $user->id)->first();

        if (!$mercadoLibreToken || !$mercadoLibreToken->access_token) {
            return response()->json(['success' => false, 'message' => 'No se encontró un token válido para el usuario logueado.'], 401);
        }

        $token = $mercadoLibreToken->access_token;
        $mlAccountId = $mercadoLibreToken->ml_account_id;

        // Buscar el seller_id usando el servicio
        $sellerId = $this->sellerIdFinderService->findSellerIdByNickname($nickname, $token, $mlAccountId);

        if ($sellerId) {
            return response()->json(['success' => true, 'seller_id' => $sellerId]);
        }

        return response()->json(['success' => false, 'message' => 'No se encontró el Seller ID para el nickname proporcionado.'], 404);
    }
}
