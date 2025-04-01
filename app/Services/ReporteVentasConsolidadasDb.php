<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReporteVentasConsolidadasDb
{
    public function generarReporteVentasConsolidadas(
        $fechaInicio,
        $fechaFin,
        $diasDeRango,
        $filters = [],
        $consolidarPorSku = false,
        $stockType = 'stock_actual',
        $sortColumn = 'cantidad_vendida',
        $sortDirection = 'desc'
    ) {
        Log::info('Iniciando generación de reporte', [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'dias_de_rango' => $diasDeRango,
            'filters' => $filters,
            'consolidar_por_sku' => $consolidarPorSku,
            'stock_type' => $stockType,
            'sort_column' => $sortColumn,
            'sort_direction' => $sortDirection,
        ]);

        if ($diasDeRango == 0) {
            return [
                'total_ventas' => 0,
                'ventas' => [],
            ];
        }

        $userId = auth()->id();
        $tokens = DB::table('mercadolibre_tokens')
            ->where('user_id', $userId)
            ->pluck('ml_account_id')
            ->toArray();

        if (empty($tokens)) {
            Log::warning('No se encontraron tokens para el usuario', ['user_id' => $userId]);
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        // Consulta principal
        $query = DB::table('ordenes as o')
            ->leftJoin('articulos as a', 'o.ml_product_id', '=', 'a.ml_product_id')
            ->leftJoin('mercadolibre_tokens as mt', 'o.ml_account_id', '=', 'mt.ml_account_id')
            ->whereIn('o.ml_account_id', $tokens);

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('o.fecha_venta', [$fechaInicio, $fechaFin]);
        }

        // Aplicar filtros
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
        if ($consolidarPorSku) {
            $query->whereNotNull('a.sku_interno')->where('a.sku_interno', '!=', '');
        }

        // Campos y agrupación
        if ($consolidarPorSku) {
            $selectFields = [
                'a.sku_interno as producto',
                DB::raw('MAX(a.titulo) as titulo'),
                'a.sku_interno as sku',
                DB::raw('SUM(o.cantidad) as cantidad_vendida'),
                DB::raw('MAX(a.tipo_publicacion) as tipo_publicacion'),
                DB::raw('MAX(a.imagen) as imagen'),
                DB::raw("MAX(a.$stockType) as stock"),
                DB::raw('MAX(a.estado) as estado'),
                DB::raw('MAX(a.permalink) as url'),
                DB::raw('MAX(o.fecha_venta) as fecha_ultima_venta'),
                DB::raw('MAX(o.estado_orden) as order_status'),
                DB::raw('GROUP_CONCAT(DISTINCT mt.seller_name SEPARATOR ", ") as seller_name'),
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
                "a.$stockType as stock",
                'a.estado',
                'a.permalink as url',
                DB::raw('MAX(o.fecha_venta) as fecha_ultima_venta'),
                'o.estado_orden as order_status',
                'mt.seller_name',
            ];
            $groupByFields = ['o.ml_product_id', 'a.titulo', 'a.sku_interno', 'a.tipo_publicacion', 'a.imagen', "a.$stockType", 'a.estado', 'a.permalink', 'o.estado_orden', 'mt.seller_name'];
        }

        // Definir columnas ordenables (excluyendo dias_stock por ahora)
        $sortableColumns = [
            'producto' => $consolidarPorSku ? 'a.sku_interno' : 'o.ml_product_id',
            'titulo' => 'titulo',
            'sku' => 'a.sku_interno',
            'cantidad_vendida' => 'SUM(o.cantidad)',
            'stock' => "MAX(a.$stockType)",
            'fecha_ultima_venta' => 'MAX(o.fecha_venta)',
        ];

        // Aplicar ordenamiento SQL solo si no es 'dias_stock'
        if ($sortColumn !== 'dias_stock' && array_key_exists($sortColumn, $sortableColumns)) {
            $orderExpression = $sortableColumns[$sortColumn];
            if (preg_match('/^(SUM|MAX|GROUP_CONCAT)\(.+\)$/', $orderExpression)) {
                $query->orderByRaw("{$orderExpression} {$sortDirection}");
            } else {
                $query->orderBy($orderExpression, $sortDirection);
            }
        } else {
            $query->orderByRaw("SUM(o.cantidad) DESC"); // Orden por defecto
        }

        // Ejecutar la consulta
        $ventasConsolidadas = $query->select($selectFields)
            ->groupBy($groupByFields)
            ->get()
            ->map(function ($venta) use ($diasDeRango) {
                $ventasDiariasPromedio = $venta->cantidad_vendida / $diasDeRango;
                $venta->dias_stock = ($ventasDiariasPromedio > 0 && $venta->stock > 0)
                    ? round($venta->stock / $ventasDiariasPromedio, 2)
                    : null;
                return (array) $venta; // Convertimos cada objeto a array aquí
            });

        // Ordenar por 'dias_stock' en PHP si es necesario
        if ($sortColumn === 'dias_stock') {
            $ventasConsolidadas = $ventasConsolidadas->sortBy(function ($venta) {
                return $venta['dias_stock'] ?? INF; // Usamos array aquí
            }, SORT_REGULAR, $sortDirection === 'desc');
        }

        $ventasArray = $ventasConsolidadas->values()->toArray();
        $totalVentas = array_sum(array_column($ventasArray, 'cantidad_vendida'));

        Log::info('Ventas consolidadas generadas', [
            'total_ventas' => $totalVentas,
            'count' => count($ventasArray),
            'stock_type' => $stockType,
        ]);

        return [
            'total_ventas' => $totalVentas,
            'ventas' => $ventasArray,
        ];
    }
}
