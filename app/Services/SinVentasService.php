<?php

// app/Services/SinVentasService.php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SinVentasService
{
    public function getProductosOrdenadosPorVentas(Carbon $fechaInicio, Carbon $fechaFin, array $filters = [])
    {
        $userId = auth()->id();
        $tokens = DB::table('mercadolibre_tokens')->where('user_id', $userId)->pluck('ml_account_id')->toArray();

        if (empty($tokens)) {
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        $query = DB::table('articulos as a')
            ->leftJoin('ordenes as o', function ($join) use ($fechaInicio, $fechaFin) {
                $join->on('a.ml_product_id', '=', 'o.ml_product_id')
                    ->whereBetween('o.fecha_venta', [$fechaInicio, $fechaFin]);
            })
            ->whereIn('a.user_id', $tokens);

        // Aplicar filtros
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('a.sku', 'like', "%{$filters['search']}%")
                  ->orWhere('a.titulo', 'like', "%{$filters['search']}%");
            });
        }
        if (!empty($filters['ml_account_id'])) {
            $query->where('a.user_id', $filters['ml_account_id']);
        }
        if (!empty($filters['tipo_publicacion'])) {
            $query->where('a.tipo_publicacion', $filters['tipo_publicacion']);
        }
        if (!empty($filters['estado_publicacion'])) {
            $query->where('a.estado', $filters['estado_publicacion']);
        }

        return $query->select(
                'a.ml_product_id',
                'a.user_id',
                'a.titulo',
                'a.permalink',
                'a.imagen',
                'a.stock_actual',
                'a.sku',
                'a.tipo_publicacion',
                'a.estado',
                DB::raw('SUM(o.cantidad) as cantidad_vendida')
            )
            ->groupBy('a.ml_product_id', 'a.user_id', 'a.titulo', 'a.permalink', 'a.imagen', 'a.stock_actual', 'a.sku', 'a.tipo_publicacion', 'a.estado')
            ->orderBy('cantidad_vendida', 'asc')
            ->get()
            ->map(function ($producto) use ($fechaInicio, $fechaFin) {
                $producto->cantidad_vendida = $producto->cantidad_vendida ?? 0;
                $diasDeRango = $fechaInicio->diffInDays($fechaFin) ?: 1;
                $ventasDiariasPromedio = $producto->cantidad_vendida / $diasDeRango;
                $producto->dias_stock = ($ventasDiariasPromedio > 0 && $producto->stock_actual > 0)
                    ? round($producto->stock_actual / $ventasDiariasPromedio, 2)
                    : null;
                return $producto;
            });
    }
}
