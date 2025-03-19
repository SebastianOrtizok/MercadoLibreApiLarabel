<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockSyncService;

class StockSyncController extends Controller
{
    protected $stockSyncService;

    public function __construct(StockSyncService $stockSyncService)
    {
        $this->stockSyncService = $stockSyncService;
    }

    public function sync(Request $request)
    {
        $userId = auth()->id();
        $this->stockSyncService->syncStocks($userId);

        return response()->json(['message' => 'Sincronización de stock iniciada'], 200);
    }
}
