<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\UserHelper; // Agregamos el helper

class SinVentasService
{
    /**
     * Obtiene productos ordenados por ventas, permitiendo al administrador usar el usuario seleccionado
     */
    public function getProductosOrdenadosPorVentas(Carbon $fechaInicio, Carbon $fechaFin, array $filters = [], $consolidarPorSku = false)
    {
        // Usar el usuario efectivo en lugar de auth()->id()
        $user = UserHelper::getEffectiveUser();
        $userId = $user->id;
        $tokens = DB::table('mercadolibre_tokens')->where('user_id', $userId)->pluck('ml_account_id')->toArray();

        if (empty($tokens)) {
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        $query = DB::table('articulos as a')
            ->leftJoin('ordenes as o', function ($join) use ($fechaInicio, $fechaFin) {
                $join->on('a.ml_product_id', '=', 'o.ml_product_id')
                     ->whereBetween('o.fecha_venta', [$fechaInicio, $fechaFin]);
            })
            ->join('mercadolibre_tokens as mt', 'a.user_id', '=', 'mt.ml_account_id')
            ->whereIn('a.user_id', $tokens);

        // Aplicar filtros
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('a.sku_interno', 'like', "%{$filters['search']}%")
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

        if ($consolidarPorSku) {
            // Agrupar por sku_interno
            $query->select([
                'a.sku_interno as ml_product_id', // Usamos SKU como identificador principal
                DB::raw('GROUP_CONCAT(DISTINCT mt.seller_name SEPARATOR ", ") as seller_name'),
                DB::raw('MAX(a.titulo) as titulo'),
                DB::raw('GROUP_CONCAT(DISTINCT a.permalink SEPARATOR ", ") as permalink'),
                DB::raw('MAX(a.imagen) as imagen'),
                DB::raw('MAX(a.stock_actual) as stock_actual'), // Usamos MAX como en ventas consolidadas
                'a.sku_interno as sku',
                DB::raw('MAX(a.tipo_publicacion) as tipo_publicacion'),
                DB::raw('MAX(a.estado) as estado'),
                DB::raw('COALESCE(SUM(o.cantidad), 0) as cantidad_vendida'),
            ])
            ->groupBy('a.sku_interno')
            ->whereNotNull('a.sku_interno')
            ->where('a.sku_interno', '!=', '');
        } else {
            // Sin agrupar, mantener el original
            $query->select([
                'a.ml_product_id',
                'mt.seller_name',
                'a.titulo',
                'a.permalink',
                'a.imagen',
                'a.stock_actual',
                'a.sku_interno as sku',
                'a.tipo_publicacion',
                'a.estado',
                DB::raw('COALESCE(SUM(o.cantidad), 0) as cantidad_vendida'),
            ])
            ->groupBy('a.ml_product_id', 'mt.seller_name', 'a.titulo', 'a.permalink', 'a.imagen', 'a.stock_actual', 'a.sku_interno', 'a.tipo_publicacion', 'a.estado');
        }

        return $query->orderBy('cantidad_vendida', 'asc')
            ->get()
            ->map(function ($producto) use ($fechaInicio, $fechaFin) {
                $diasDeRango = $fechaInicio->diffInDays($fechaFin) ?: 1;
                $ventasDiariasPromedio = $producto->cantidad_vendida / $diasDeRango;
                $producto->dias_stock = ($ventasDiariasPromedio > 0 && $producto->stock_actual > 0)
                    ? round($producto->stock_actual / $ventasDiariasPromedio, 2)
                    : null;
                return $producto;
            });
    }
}
