<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReporteVentasConsolidadasDb
{
    public function generarReporteVentasConsolidadas($fechaInicio, $fechaFin, $diasDeRango, $filters = [])
    {
        if ($diasDeRango == 0) {
            return [
                'total_ventas' => 0,
                'ventas' => [],
            ];
        }

        $userId = auth()->id();
        $tokens = DB::table('mercadolibre_tokens')->where('user_id', $userId)->pluck('ml_account_id')->toArray();

        if (empty($tokens)) {
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        // Consulta base
        $query = DB::table('ordenes as o')
            ->join('articulos as a', 'o.ml_product_id', '=', 'a.ml_product_id')
            ->whereIn('o.ml_account_id', $tokens)
            ->whereBetween('o.fecha_venta', [$fechaInicio, $fechaFin]);

        // Aplicar filtros
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('a.sku', 'like', "%{$filters['search']}%")
                  ->orWhere('a.titulo', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['tipo_publicacion'])) {
            $query->where('a.tipo_publicacion', $filters['tipo_publicacion']);
        }

        if (!empty($filters['order_status'])) {
            $query->where('o.estado_orden', $filters['order_status']);
        }

        if (!empty($filters['estado_publicacion'])) {
            $query->where('a.estado', $filters['estado_publicacion']);
        }

        if (!empty($filters['ml_account_id'])) {
            $query->where('o.ml_account_id', $filters['ml_account_id']);
        }

        // Seleccionar y agrupar
        $ventasConsolidadas = $query->select(
            'o.ml_product_id as producto',
            'a.titulo',
            DB::raw('SUM(o.cantidad) as cantidad_vendida'),
            'a.tipo_publicacion',
            'o.ml_account_id',
            'a.imagen',
            'a.stock_actual as stock',
            'a.sku',
            'a.estado',
            'a.permalink as url',
            DB::raw('MAX(o.fecha_venta) as fecha_ultima_venta'),
            'o.estado_orden as order_status'
        )
        ->groupBy('o.ml_product_id', 'a.titulo', 'a.tipo_publicacion', 'o.ml_account_id', 'a.imagen', 'a.stock_actual', 'a.sku', 'a.estado', 'a.permalink', 'o.estado_orden')
        ->get()
        ->map(function ($venta) use ($diasDeRango) {
            $ventasDiariasPromedio = $venta->cantidad_vendida / $diasDeRango;
            $venta->dias_stock = ($ventasDiariasPromedio > 0 && $venta->stock > 0)
                ? round($venta->stock / $ventasDiariasPromedio, 2)
                : null;
            return (array) $venta;
        })->toArray();

        $totalVentas = array_sum(array_column($ventasConsolidadas, 'cantidad_vendida'));

        return [
            'total_ventas' => $totalVentas,
            'ventas' => $ventasConsolidadas,
        ];
    }
}
