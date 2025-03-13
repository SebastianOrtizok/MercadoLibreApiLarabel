<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MissingArticlesService;

class MissingArticlesController extends Controller
{
    protected $missingArticlesService;

    public function __construct(MissingArticlesService $missingArticlesService)
    {
        $this->missingArticlesService = $missingArticlesService;
    }

    public function sync(Request $request, $mlAccountId)
    {
        $usuarioAutenticado = auth()->id();
        $cuenta = \App\Models\MercadoLibreToken::where('ml_account_id', $mlAccountId)
                    ->where('user_id', $usuarioAutenticado)
                    ->first();

        if (!$cuenta) {
            return redirect()->back()->with('error', 'No tienes permiso para sincronizar esta cuenta.');
        }

        $token = $cuenta->access_token;
        $result = $this->missingArticlesService->syncMissingArticles($mlAccountId, $token);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }
}
