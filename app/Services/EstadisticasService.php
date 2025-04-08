<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        $now = Carbon::now();
        $maxDateFrom = $now->copy()->subDays(149)->startOfDay();
        $dateFrom = $fechaInicio->greaterThan($maxDateFrom) ? $fechaInicio : $maxDateFrom;
        $dateTo = $fechaFin->lessThanOrEqualTo($now) ? $fechaFin : $now;

        $topVentasPorCuenta = [];

        foreach ($mlAccountIds as $mlAccountId) {
            // Obtener los 20 más vendidos por cuenta
            $ventasPorProducto = DB::table('ordenes')
                ->select('ml_product_id', DB::raw('SUM(cantidad) as total_vendido'))
                ->whereBetween('fecha_venta', [$dateFrom, $dateTo])
                ->where('ml_account_id', $mlAccountId)
                ->whereNotNull('ml_product_id')
                ->groupBy('ml_product_id')
                ->orderBy('total_vendido', 'desc')
                ->limit(20)
                ->get();

            Log::info("Ventas por producto para cuenta $mlAccountId", [
                'count' => $ventasPorProducto->count(),
                'data' => $ventasPorProducto->toArray()
            ]);

            if ($ventasPorProducto->isEmpty()) {
                Log::warning("No se encontraron ventas para cuenta $mlAccountId en el rango de fechas");
                $topVentasPorCuenta[$mlAccountId] = collect();
                continue;
            }

            $productIds = $ventasPorProducto->pluck('ml_product_id')->toArray();
            $userId = auth()->id();
            $visitasPorProducto = $this->getVisitasMultiplesProductos($userId, [$mlAccountId], $productIds, $dateFrom, $dateTo);

            $topResultados = $ventasPorProducto->map(function ($producto) use ($visitasPorProducto) {
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

            $topVentasPorCuenta[$mlAccountId] = $topResultados;
        }

        Log::info('Resultados finales', [
            'top_ventas' => array_map(fn($col) => $col->toArray(), $topVentasPorCuenta)
        ]);

        return $topVentasPorCuenta; // Solo devolvemos top_ventas por cuenta
    }

    protected function getVisitasMultiplesProductos($userId, $mlAccountIds, $productIds, $fechaInicio, $fechaFin)
    {
        $visitasPorProducto = [];
        if (empty($productIds)) {
            Log::warning('No hay product IDs para consultar visitas');
            return $visitasPorProducto;
        }

        $now = Carbon::now()->subHours(48); // Ajustamos 48 horas atrás por el retraso de la API
        $maxDateFrom = $now->copy()->subDays(149)->startOfDay(); // Máximo 150 días atrás
        $dateFrom = $fechaInicio->greaterThan($maxDateFrom) ? $fechaInicio : $maxDateFrom;
        $dateTo = $fechaFin->lessThanOrEqualTo($now) ? $fechaFin : $now;

        Log::info('Fechas ajustadas para API', [
            'adjusted_date_from' => $dateFrom->toIso8601ZuluString(),
            'adjusted_date_to' => $dateTo->toIso8601ZuluString()
        ]);

        $chunks = array_chunk($productIds, 20);
        $mlAccountId = $mlAccountIds[0];
        $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);

        foreach ($chunks as $chunk) {
            $idsString = implode(',', $chunk);

            Log::info('Consultando visitas a la API', [
                'ids' => $idsString,
                'date_from' => $dateFrom->toIso8601ZuluString(),
                'date_to' => $dateTo->toIso8601ZuluString(),
                'ml_account_id' => $mlAccountId
            ]);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}"
            ])->get("https://api.mercadolibre.com/items/visits", [
                'ids' => $idsString,
                'date_from' => $dateFrom->toIso8601ZuluString(),
                'date_to' => $dateTo->toIso8601ZuluString(),
            ]);

            Log::info('Respuesta de la API', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                foreach ($data as $item) {
                    $visitasPorProducto[$item['item_id']] = $item['total_visits'];
                }
            } else {
                Log::error('Error al consultar visitas', ['response' => $response->body()]);
            }
        }

        return $visitasPorProducto;
    }
}
