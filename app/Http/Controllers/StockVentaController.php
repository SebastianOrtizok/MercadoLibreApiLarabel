<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockVentaService;

class StockVentaController extends Controller
{
    public function sync(Request $request, StockVentaService $service)
    {
        try {
            $service->syncStockFromSales();
            return redirect()->route('dashboard')->with('success', 'Stock de ventas sincronizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Error al sincronizar stock: ' . $e->getMessage());
        }
    }
}
