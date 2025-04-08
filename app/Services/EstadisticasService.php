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
        Log::info('getTasaConversionPorProducto iniciado', [
            'mlAccountIds' => $mlAccountIds,
            'fechaInicio' => $fechaInicio->toDateTimeString(),
            'fechaFin' => $fechaFin->toDateTimeString()
        ]);

        // Obtener productos vendidos ordenados por ventas (descendente)
        $ventasPorProducto = DB::table('ordenes')
            ->select('ml_product_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->whereNotNull('ml_product_id')
            ->when(!empty($mlAccountIds), fn($query) => $query->whereIn('ml_account_id', $mlAccountIds))
            ->groupBy('ml_product_id')
            ->orderBy('total_vendido', 'desc')
            ->get();

        Log::info('Ventas por producto', ['count' => $ventasPorProducto->count(), 'data' => $ventasPorProducto->toArray()]);

        if ($ventasPorProducto->isEmpty()) {
            Log::warning('No se encontraron ventas en el rango de fechas');
            return ['top_ventas' => collect(), 'bottom_ventas' => collect()];
        }

        // Tomar los 20 con más ventas y los 20 con menos ventas
        $top20Ventas = $ventasPorProducto->take(20);
        $bottom20Ventas = $ventasPorProducto->slice(-20);

        Log::info('Top 20 ventas', ['data' => $top20Ventas->toArray()]);
        Log::info('Bottom 20 ventas', ['data' => $bottom20Ventas->toArray()]);

        // Combinar los ml_product_id de ambos grupos (máximo 40 ítems)
        $productIds = $top20Ventas->pluck('ml_product_id')
            ->merge($bottom20Ventas->pluck('ml_product_id'))
            ->unique()
            ->toArray();

        Log::info('Product IDs para consultar visitas', ['productIds' => $productIds]);

        $userId = auth()->id();
        $visitasPorProducto = $this->getVisitasMultiplesProductos($userId, $mlAccountIds, $productIds, $fechaInicio, $fechaFin);

        Log::info('Visitas por producto', ['data' => $visitasPorProducto]);

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

        Log::info('Resultados finales', [
            'top_ventas' => $topResultados->toArray(),
            'bottom_ventas' => $bottomResultados->toArray()
        ]);

        return [
            'top_ventas' => $topResultados,
            'bottom_ventas' => $bottomResultados,
        ];
    }

    protected function getVisitasMultiplesProductos($userId, $mlAccountIds, $productIds, $fechaInicio, $fechaFin)
    {
        $visitasPorProducto = [];
        if (empty($productIds)) {
            Log::warning('No hay product IDs para consultar visitas');
            return $visitasPorProducto;
        }

        // Dividir en bloques de 20
        $chunks = array_chunk($productIds, 20);

        foreach ($mlAccountIds as $mlAccountId) {
            $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);

            foreach ($chunks as $chunk) {
                $idsString = implode(',', $chunk);
                Log::info('Consultando visitas a la API', ['ids' => $idsString]);

                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$accessToken}"
                ])->get("https://api.mercadolibre.com/items/visits", [
                    'ids' => $idsString,
                    'date_from' => $fechaInicio->toDateString(),
                    'date_to' => $fechaFin->toDateString(),
                ]);

                Log::info('Respuesta de la API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    foreach ($data as $item) {
                        // Sumar visitas si el producto ya existe (para múltiples cuentas)
                        $visitasPorProducto[$item['id']] = ($visitasPorProducto[$item['id']] ?? 0) + $item['total_visits'];
                    }
                } else {
                    Log::error('Error al consultar visitas', ['response' => $response->body()]);
                }
            }
        }

        return $visitasPorProducto;
    }
}
