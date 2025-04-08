<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class EstadisticasService
{
    protected $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->mercadoLibreService = $mercadoLibreService;
    }

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

    public function getTopProductosVendidos($mlAccountIds = [], $fechaInicio, $fechaFin)
{
    $query = DB::table('ordenes')
        ->join('articulos', 'ordenes.ml_product_id', '=', 'articulos.ml_product_id') // Unimos por ml_product_id
        ->select(
            'articulos.titulo',
            DB::raw('SUM(ordenes.cantidad) as total_vendido'),
            DB::raw('SUM(ordenes.cantidad * ordenes.precio_unitario) as total_facturado')
        )
        ->whereBetween('ordenes.fecha_venta', [$fechaInicio, $fechaFin])
        ->groupBy('articulos.titulo') // Agrupamos por título
        ->orderBy('total_vendido', 'desc')
        ->limit(10);

    if (!empty($mlAccountIds)) {
        $query->whereIn('ordenes.ml_account_id', $mlAccountIds);
    }

    return $query->get();
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
            ->where('estado', 'active') // Solo publicaciones activas
            ->where(function ($q) {
                $q->where('stock_fulfillment', '<', 5)
                  ->orWhere('stock_deposito', '<', 5);
            })
            ->select('titulo', 'stock_fulfillment', 'stock_deposito') // Cambiamos stock_actual por stock_deposito
            ->orderByRaw('LEAST(stock_fulfillment, stock_deposito) ASC'); // Ordenar por el menor stock primero

        if (!empty($mlAccountIds)) {
            $query->whereIn('user_id', $mlAccountIds);
        }

        return $query->get();
    }
    public function getTotalFacturado($mlAccountIds = [], $fechaInicio, $fechaFin)
    {
        $query = DB::table('ordenes')
            ->select(DB::raw('SUM(cantidad * precio_unitario) as total_facturado'))
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);

        if (!empty($mlAccountIds)) {
            $query->whereIn('ml_account_id', $mlAccountIds);
        }

        return $query->value('total_facturado') ?? 0; // Devuelve 0 si no hay resultados
    }

    public function getVentasPorPeriodo($mlAccountIds = [], $fechaInicio, $fechaFin)
    {
        $query = DB::table('ordenes')
            ->select(
                DB::raw('DATE(fecha_venta) as fecha'),
                DB::raw('SUM(cantidad) as total_vendido'),
                DB::raw('SUM(cantidad * precio_unitario) as total_facturado')
            )
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->groupBy(DB::raw('DATE(fecha_venta)'));

        if (!empty($mlAccountIds)) {
            $query->whereIn('ml_account_id', $mlAccountIds);
        }

        return $query->get();
    }

    public function getVentasPorDiaSemana($mlAccountIds = [])
    {
        $query = DB::table('ordenes')
            ->select(
                DB::raw('DAYNAME(fecha_venta) as dia_semana'),
                DB::raw('SUM(cantidad) as total_vendido')
            )
            ->groupBy(DB::raw('DAYNAME(fecha_venta)'));

        if (!empty($mlAccountIds)) {
            $query->whereIn('ml_account_id', $mlAccountIds);
        }

        $result = $query->get();

        // Ordenar los días de la semana (Lunes a Domingo)
        $diasOrdenados = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $ventasPorDia = collect($diasOrdenados)->mapWithKeys(function ($dia) use ($result) {
            $venta = $result->firstWhere('dia_semana', $dia);
            return [$dia => $venta ? $venta->total_vendido : 0];
        })->all();

        return $ventasPorDia;
    }

    public function getTasaConversionPorProducto($mlAccountIds = [], $fechaInicio, $fechaFin)
    {
        // Obtener productos vendidos ordenados por ventas (descendente)
        $ventasPorProducto = DB::table('ordenes')
            ->select('ml_product_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->whereNotNull('ml_product_id')
            ->when(!empty($mlAccountIds), fn($query) => $query->whereIn('ml_account_id', $mlAccountIds))
            ->groupBy('ml_product_id')
            ->orderBy('total_vendido', 'desc')
            ->get();

        // Tomar los 20 con más ventas y los 20 con menos ventas
        $top20Ventas = $ventasPorProducto->take(20);
        $bottom20Ventas = $ventasPorProducto->slice(-20);

        // Combinar los ml_product_id de ambos grupos (máximo 40 ítems)
        $productIds = $top20Ventas->pluck('ml_product_id')
            ->merge($bottom20Ventas->pluck('ml_product_id'))
            ->unique()
            ->take(50) // Límite seguro según la API
            ->toArray();

        $userId = auth()->id();
        $visitasPorProducto = $this->getVisitasMultiplesProductos($userId, $mlAccountIds, $productIds, $fechaInicio, $fechaFin);

        // Procesar top 20
        $topResultados = $top20Ventas->map(function ($producto) use ($visitasPorProducto) {
            $visitas = $visitasPorProducto[$producto->ml_product_id] ?? 0;
            $titulo = DB::table('articulos')
                ->where('ml_product_id', $producto->ml_product_id)
                ->value('titulo') ?? 'Producto Desconocido';
            $tasaConversion = $visitas > 0 ? ($producto->total_vendido / $visitas) * 100 : 0;

            return (object) [
                'ml_product_id' => $producto->ml_product_id,
                'titulo' => $titulo,
                'total_vendido' => $producto->total_vendido,
                'visitas' => $visitas,
                'tasa_conversion' => round($tasaConversion, 2),
            ];
        });

        // Procesar bottom 20
        $bottomResultados = $bottom20Ventas->map(function ($producto) use ($visitasPorProducto) {
            $visitas = $visitasPorProducto[$producto->ml_product_id] ?? 0;
            $titulo = DB::table('articulos')
                ->where('ml_product_id', $producto->ml_product_id)
                ->value('titulo') ?? 'Producto Desconocido';
            $tasaConversion = $visitas > 0 ? ($producto->total_vendido / $visitas) * 100 : 0;

            return (object) [
                'ml_product_id' => $producto->ml_product_id,
                'titulo' => $titulo,
                'total_vendido' => $producto->total_vendido,
                'visitas' => $visitas,
                'tasa_conversion' => round($tasaConversion, 2),
            ];
        });

        return [
            'top_ventas' => $topResultados,
            'bottom_ventas' => $bottomResultados,
        ];
    }

    protected function getVisitasMultiplesProductos($userId, $mlAccountIds, $productIds, $fechaInicio, $fechaFin)
    {
        $visitasPorProducto = [];
        if (empty($productIds)) {
            return $visitasPorProducto;
        }

        // Hacer una sola consulta con hasta 50 ítems
        $idsString = implode(',', array_slice($productIds, 0, 50)); // Asegurar máximo 50
        foreach ($mlAccountIds as $mlAccountId) {
            $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}"
            ])->get("https://api.mercadolibre.com/items/visits", [
                'ids' => $idsString,
                'date_from' => $fechaInicio->toDateString(),
                'date_to' => $fechaFin->toDateString(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                foreach ($data as $item) {
                    $visitasPorProducto[$item['id']] = $item['total_visits'];
                }
            }
        }

        return $visitasPorProducto;
    }
}
