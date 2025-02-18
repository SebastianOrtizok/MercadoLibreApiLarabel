<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PromotionsService;
use App\Models\MercadoLibreToken; // Modelo de la base de datos de los tokens
use Illuminate\Support\Facades\Log;

class PromotionsController extends Controller
{
    private $promotionsService;

    public function __construct(PromotionsService $promotionsService)
    {
        $this->promotionsService = $promotionsService;
    }

    /**
     * Obtener todas las promociones de las cuentas asociadas al usuario.
     */
    public function promotions(Request $request)
    {
        try {
            // Obtener el user_id del usuario logueado
            $userId = auth()->user()->id;

            // Obtener todas las cuentas asociadas al usuario
            $accounts = MercadoLibreToken::where('user_id', $userId)->get();

            // Verificar que el usuario tiene cuentas asociadas
            if ($accounts->isEmpty()) {
                return response()->json(['error' => 'El usuario no tiene cuentas asociadas.'], 400);
            }

            // Recoger todas las promociones de todas las cuentas asociadas
            $promotions = [];
            foreach ($accounts as $account) {
                // Obtener el token de acceso de la cuenta
                $accessToken = $account->access_token;
                // Obtener las promociones para cada cuenta
                $promotions[] = $this->promotionsService->getAllPromotions($account->ml_account_id, $accessToken);
            }

            // Devolver las promociones de todas las cuentas asociadas
            return response()->json($promotions);
        } catch (\Exception $e) {
            Log::error("Error al obtener promociones: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Otros métodos como show(), store(), update(), destroy() siguen igual
}
