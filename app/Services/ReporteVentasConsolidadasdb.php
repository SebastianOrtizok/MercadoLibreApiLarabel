<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReporteVentasConsolidadasDb
{
    public function generarReporteVentasConsolidadas($fechaInicio, $fechaFin, $diasDeRango, $filters = [], $consolidarPorSku = false)
    {
        if ($diasDeRango == 0) {
            return [
                'total_ventas' => 0,
                'ventas' => [],
            ];
        }

        $userId = auth()->id();
        Log::info('User ID del usuario logueado', ['user_id' => $userId]);

        $tokens = DB::table('mercadolibre_tokens')
            ->where('user_id', $userId)
            ->pluck('ml_account_id')
            ->toArray();
        Log::info('Tokens (seller_ids) obtenidos para el usuario', ['tokens' => $tokens]);

        if (empty($tokens)) {
            Log::warning('No se encontraron tokens para el usuario', ['user_id' => $userId]);
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        // Construcción de la consulta
        $query = DB::table('ordenes as o')
            ->leftJoin('articulos as a', 'o.ml_product_id', '=', 'a.ml_product_id')
            ->leftJoin('mercadolibre_tokens as mt', 'o.ml_account_id', '=', 'mt.ml_account_id')
            ->whereIn('o.ml_account_id', $tokens);

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('o.fecha_venta', [$fechaInicio, $fechaFin]);
        }

        // Aplicar filtros si existen
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('a.sku_interno', 'like', "%{$filters['search']}%")
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

        // Definir los campos de selección y agrupación según si se agrupa por SKU
        if ($consolidarPorSku) {
            $selectFields = [
                'a.sku_interno as producto',
                DB::raw('MAX(a.titulo) as titulo'), // Obtener el título correspondiente
                'a.sku_interno as sku',
                DB::raw('SUM(o.cantidad) as cantidad_vendida'),
                DB::raw('MAX(a.tipo_publicacion) as tipo_publicacion'), // Asegurar que haya tipo de publicación
                DB::raw('MAX(a.imagen) as imagen'),
                DB::raw('MAX(a.stock_actual) as stock'),
                DB::raw('MAX(a.estado) as estado'),
                DB::raw('MAX(a.permalink) as url'),
                DB::raw('MAX(o.fecha_venta) as fecha_ultima_venta'),
                DB::raw('MAX(o.estado_orden) as order_status'),
                DB::raw('MAX(mt.seller_name) as seller_name')
            ];

            $groupByFields = ['a.sku_interno'];
        } else {
            $selectFields = [
                'o.ml_product_id as producto',
                'a.titulo',
                'a.sku_interno as sku',
                DB::raw('SUM(o.cantidad) as cantidad_vendida'),
                'a.tipo_publicacion',
                'a.imagen',
                'a.stock_actual as stock',
                'a.estado',
                'a.permalink as url',
                DB::raw('MAX(o.fecha_venta) as fecha_ultima_venta'),
                'o.estado_orden as order_status',
                'mt.seller_name'
            ];

            $groupByFields = ['o.ml_product_id', 'a.titulo', 'a.sku_interno', 'a.tipo_publicacion', 'a.imagen', 'a.stock_actual', 'a.estado', 'a.permalink', 'o.estado_orden', 'mt.seller_name'];
        }

        // Obtener la cantidad de registros antes de agrupar
        $totalRegistros = $query->count();
        Log::info('Total de registros antes de agrupar', [
            'count' => $totalRegistros,
            'fecha_inicio' => $fechaInicio ? $fechaInicio->toDateTimeString() : 'No aplicado',
            'fecha_fin' => $fechaFin ? $fechaFin->toDateTimeString() : 'No aplicado',
            'tokens' => $tokens
        ]);

        if ($totalRegistros == 0) {
            Log::warning('No se encontraron registros antes de agrupar. Revisar datos en ordenes y articulos.');
        }

        // Aplicar agrupamiento y procesar resultados
        $ventasConsolidadas = $query->select($selectFields)
            ->groupBy($groupByFields)
            ->get()
            ->map(function ($venta) use ($diasDeRango) {
                $ventasDiariasPromedio = $venta->cantidad_vendida / $diasDeRango;
                $venta->dias_stock = ($ventasDiariasPromedio > 0 && $venta->stock > 0)
                    ? round($venta->stock / $ventasDiariasPromedio, 2)
                    : null;
                return (array) $venta;
            })->toArray();

        $totalVentas = array_sum(array_column($ventasConsolidadas, 'cantidad_vendida'));
        Log::info('Ventas consolidadas generadas', [
            'total_ventas' => $totalVentas,
            'count' => count($ventasConsolidadas),
            'sample' => !empty($ventasConsolidadas) ? $ventasConsolidadas[0] : null
        ]);

        return [
            'total_ventas' => $totalVentas,
            'ventas' => $ventasConsolidadas,
        ];
    }
}
