<?php
namespace App\Services;

use App\Models\Articulo;
use Illuminate\Support\Facades\DB;

class EstadisticasService
{
    public function getStockPorTipo($userId = null)
    {
        $query = Articulo::select(
            DB::raw('SUM(stock_actual) as stock_actual'),
            DB::raw('SUM(stock_fulfillment) as stock_fulfillment'),
            DB::raw('SUM(stock_deposito) as stock_deposito')
        );

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $result = $query->first();

        return [
            'stock_actual' => $result->stock_actual ?? 0,
            'stock_fulfillment' => $result->stock_fulfillment ?? 0,
            'stock_deposito' => $result->stock_deposito ?? 0,
        ];
    }

    public function getProductosEnPromocion($userId = null)
    {
        $query = Articulo::where('en_promocion', 1)
            ->select(
                'titulo',
                'descuento_porcentaje'
            )
            ->orderBy('descuento_porcentaje', 'desc')
            ->limit(5);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    public function getProductosPorEstado($userId = null)
    {
        $query = Articulo::select(
            'estado',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('estado');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    public function getStockCritico($userId = null)
    {
        $query = Articulo::where(function ($q) {
            $q->where('stock_actual', '<', 5)
              ->orWhere('stock_fulfillment', '<', 5);
        })
        ->select('titulo', 'stock_actual', 'stock_fulfillment');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }
}
