<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class EstadisticasService
{
    public function getStockPorTipo($mlAccountIds = [])
    {
        $query = DB::table('articulos');

        if (!empty($mlAccountIds)) {
            $query->whereIn('user_id', $mlAccountIds);
        }

        $result = $query->select(
            DB::raw('SUM(stock_actual) as stock_actual'),
            DB::raw('SUM(stock_fulfillment) as stock_fulfillment'),
            DB::raw('SUM(stock_deposito) as stock_deposito')
        )->first();

        return [
            'stock_actual' => $result->stock_actual ?? 0,
            'stock_fulfillment' => $result->stock_fulfillment ?? 0,
            'stock_deposito' => $result->stock_deposito ?? 0,
        ];
    }

    public function getProductosEnPromocion($mlAccountIds = [])
    {
        $query = DB::table('articulos')
            ->where('en_promocion', 1)
            ->select('titulo', 'descuento_porcentaje')
            ->orderBy('descuento_porcentaje', 'desc')
            ->limit(5);

        if (!empty($mlAccountIds)) {
            $query->whereIn('user_id', $mlAccountIds);
        }

        $result = $query->get();
        return $result->isEmpty() ? collect([['titulo' => 'Sin promociones', 'descuento_porcentaje' => 0]]) : $result;
    }

    public function getProductosPorEstado($mlAccountIds = [])
    {
        $query = DB::table('articulos')
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado');

        if (!empty($mlAccountIds)) {
            $query->whereIn('user_id', $mlAccountIds);
        }

        $result = $query->get();
        return $result->isEmpty() ? collect([['estado' => 'Sin datos', 'total' => 0]]) : $result;
    }

    public function getStockCritico($mlAccountIds = [])
    {
        $query = DB::table('articulos')
            ->where(function ($q) {
                $q->where('stock_actual', '<', 5)
                  ->orWhere('stock_fulfillment', '<', 5);
            })
            ->select('titulo', 'stock_actual', 'stock_fulfillment');

        if (!empty($mlAccountIds)) {
            $query->whereIn('user_id', $mlAccountIds);
        }

        return $query->get();
    }
}
