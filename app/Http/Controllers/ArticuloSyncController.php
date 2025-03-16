<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ArticuloSyncService;
use App\Jobs\ArticuloSyncJob;

class ArticuloSyncController extends Controller
{
    protected $syncService;

    public function __construct(ArticuloSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function sync(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $limit = (int) $request->input('limit', 50);

            Log::info("Iniciando sincronización de artículos para usuario: {$userId}");

            $this->syncService->syncArticulos($userId, $limit);

            return redirect()->back()->with('success', 'Sincronización de artículos iniciada en segundo plano.');
        } catch (\Exception $e) {
            Log::error("Error al iniciar sincronización de artículos: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al sincronizar artículos: ' . $e->getMessage());
        }
    }
}
