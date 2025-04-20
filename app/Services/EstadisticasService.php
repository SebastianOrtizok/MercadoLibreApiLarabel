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

        $stock = [
            'stock_actual' => $result->stock_actual ?? 0,
            'stock_fulfillment' => $result->stock_fulfillment ?? 0,
            'stock_deposito' => $result->stock_deposito ?? 0,
        ];

        Log::info('getStockPorTipo', [
            'ml_account_ids' => $mlAccountIds,
            'result' => $stock,
        ]);

        return $stock;
    }

    public function getTopProductosVendidos($mlAccountIds = [], $fechaInicio, $fechaFin)
    {
        $query = DB::table('ordenes')
            ->join('articulos', 'ordenes.ml_product_id', '=', 'articulos.ml_product_id')
            ->select(
                'articulos.titulo',
                DB::raw('SUM(ordenes.cantidad) as total_vendido'),
                DB::raw('SUM(ordenes.cantidad * ordenes.precio_unitario) as total_facturado')
            )
            ->whereBetween('ordenes.fecha_venta', [$fechaInicio, $fechaFin])
            ->whereIn('articulos.user_id', $mlAccountIds) // Filtro adicional para articulos.user_id
            ->groupBy('articulos.titulo')
            ->orderBy('total_vendido', 'desc')
            ->limit(10);

        if (!empty($mlAccountIds)) {
            $query->whereIn('ordenes.ml_account_id', $mlAccountIds);
        }

        $result = $query->get();

        Log::info('getTopProductosVendidos', [
            'ml_account_ids' => $mlAccountIds,
            'fecha_inicio' => $fechaInicio->toDateTimeString(),
            'fecha_fin' => $fechaFin->toDateTimeString(),
            'result_count' => $result->count(),
            'sample' => $result->first() ? $result->first() : 'No data',
        ]);

        return $result;
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

        Log::info('getProductosEnPromocion', [
            'ml_account_ids' => $mlAccountIds,
            'result_count' => $result->count(),
            'sample' => $result->first() ? $result->first() : 'No data',
        ]);

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

        Log::info('getProductosPorEstado', [
            'ml_account_ids' => $mlAccountIds,
            'result_count' => $result->count(),
            'sample' => $result->first() ? $result->first() : 'No data',
        ]);

        return $result->isEmpty() ? collect([['estado' => 'Sin datos', 'total' => 0]]) : $result;
    }

    public function getStockCritico($mlAccountIds = [])
    {
        $query = DB::table('articulos')
            ->where('estado', 'active')
            ->where(function ($q) {
                $q->where('stock_fulfillment', '<', 5)
                  ->orWhere('stock_deposito', '<', 5);
            })
            ->select('titulo', 'stock_fulfillment', 'stock_deposito')
            ->orderByRaw('LEAST(stock_fulfillment, stock_deposito) ASC');

        if (!empty($mlAccountIds)) {
            $query->whereIn('user_id', $mlAccountIds);
        }

        $result = $query->get();

        Log::info('getStockCritico', [
            'ml_account_ids' => $mlAccountIds,
            'result_count' => $result->count(),
            'sample' => $result->first() ? $result->first() : 'No data',
        ]);

        return $result;
    }

    public function getTotalFacturado($mlAccountIds = [], $fechaInicio, $fechaFin)
    {
        $query = DB::table('ordenes')
            ->select(DB::raw('SUM(cantidad * precio_unitario) as total_facturado'))
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);

        if (!empty($mlAccountIds)) {
            $query->whereIn('ml_account_id', $mlAccountIds);
        }

        $result = $query->value('total_facturado') ?? 0;

        Log::info('getTotalFacturado', [
            'ml_account_ids' => $mlAccountIds,
            'fecha_inicio' => $fechaInicio->toDateTimeString(),
            'fecha_fin' => $fechaFin->toDateTimeString(),
            'result' => $result,
        ]);

        return $result;
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

        $result = $query->get();

        Log::info('getVentasPorPeriodo', [
            'ml_account_ids' => $mlAccountIds,
            'fecha_inicio' => $fechaInicio->toDateTimeString(),
            'fecha_fin' => $fechaFin->toDateTimeString(),
            'result_count' => $result->count(),
            'sample' => $result->first() ? $result->first() : 'No data',
        ]);

        return $result;
    }

    public function getVentasPorDiaSemana($mlAccountIds = [])
    {
        $isPostgres = config('database.default') === 'pgsql';

        $query = DB::table('ordenes')
            ->select(
                DB::raw($isPostgres ? "TO_CHAR(fecha_venta, 'Day') as dia_semana" : "DAYNAME(fecha_venta) as dia_semana"),
                DB::raw('SUM(cantidad) as total_vendido')
            )
            ->groupBy(DB::raw($isPostgres ? "TO_CHAR(fecha_venta, 'Day')" : "DAYNAME(fecha_venta)"));

        if (!empty($mlAccountIds)) {
            $query->whereIn('ml_account_id', $mlAccountIds);
        }

        $result = $query->get();

        Log::info('getVentasPorDiaSemana', [
            'ml_account_ids' => $mlAccountIds,
            'result_count' => $result->count(),
            'sample' => $result->first() ? $result->first() : 'No data',
        ]);

        // Definimos los días en español
        $diasEnEspañol = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];

        $diasOrdenados = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $ventasPorDia = collect($diasOrdenados)->mapWithKeys(function ($dia) use ($result, $diasEnEspañol) {
            $venta = $result->firstWhere('dia_semana', fn($value) => trim($value) === $dia);
            $diaEspañol = $diasEnEspañol[$dia] ?? $dia;
            return [$diaEspañol => $venta ? $venta->total_vendido : 0];
        })->all();

        return $ventasPorDia;
    }

    public function getTasaConversionPorProducto($mlAccountIds = [], $fechaInicio, $fechaFin)
    {
        Log::info('getTasaConversionPorProducto iniciado', [
            'mlAccountIds' => $mlAccountIds,
            'fechaInicio' => $fechaInicio->toDateTimeString(),
            'fechaFin' => $fechaFin->toDateTimeString(),
        ]);

        $now = Carbon::now();
        $maxDateFrom = $now->copy()->subDays(149)->startOfDay();
        $dateFrom = $fechaInicio->greaterThan($maxDateFrom) ? $fechaInicio : $maxDateFrom;
        $dateTo = $fechaFin->lessThanOrEqualTo($now) ? $fechaFin : $now;

        $topVentasPorCuenta = [];

        foreach ($mlAccountIds as $mlAccountId) {
            // Obtener los más vendidos por cuenta
            $ventasPorProducto = DB::table('ordenes')
                ->select('ml_product_id', DB::raw('SUM(cantidad) as total_vendido'))
                ->whereBetween('fecha_venta', [$dateFrom, $dateTo])
                ->where('ml_account_id', $mlAccountId)
                ->whereNotNull('ml_product_id')
                ->groupBy('ml_product_id')
                ->orderBy('total_vendido', 'desc')
                ->limit(10) // Aumentamos a 10 para más datos
                ->get();

            Log::info("Ventas por producto para cuenta $mlAccountId", [
                'count' => $ventasPorProducto->count(),
                'data' => $ventasPorProducto->toArray(),
            ]);

            if ($ventasPorProducto->isEmpty()) {
                Log::warning("No se encontraron ventas para cuenta $mlAccountId en el rango de fechas");
                $topVentasPorCuenta[$mlAccountId] = collect([]);
                continue;
            }

            $productIds = $ventasPorProducto->pluck('ml_product_id')->toArray();
            $userId = auth()->id();
            $visitasPorProducto = $this->getVisitasMultiplesProductos($userId, [$mlAccountId], $productIds, $dateFrom, $dateTo);

            $topResultados = $ventasPorProducto->map(function ($producto) use ($visitasPorProducto, $mlAccountId) {
                $visitas = $visitasPorProducto[$producto->ml_product_id] ?? 0;
                $titulo = DB::table('articulos')
                    ->where('ml_product_id', $producto->ml_product_id)
                    ->where('user_id', $mlAccountId) // Filtro adicional
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

        Log::info('Resultados finales getTasaConversionPorProducto', [
            'top_ventas' => array_map(fn($col) => $col->toArray(), $topVentasPorCuenta),
        ]);

        return $topVentasPorCuenta;
    }

    protected function getVisitasMultiplesProductos($userId, $mlAccountIds, $productIds, $fechaInicio, $fechaFin)
    {
        $visitasPorProducto = [];

        if (empty($productIds)) {
            Log::warning('No hay product IDs para consultar visitas');
            return $visitasPorProducto;
        }

        $now = Carbon::now()->subHours(48);
        $maxDateFrom = $now->copy()->subDays(149)->startOfDay();

        $dateFrom = $fechaInicio->greaterThan($maxDateFrom) ? $fechaInicio->copy()->startOfDay() : $maxDateFrom;
        $dateTo = $fechaFin->lessThanOrEqualTo($now) ? $fechaFin->copy()->startOfDay() : $now->copy()->startOfDay();

        if ($dateFrom->greaterThan($dateTo)) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $mlAccountId = $mlAccountIds[0];
        $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);

        $chunks = array_chunk($productIds, 5);

        foreach ($chunks as $chunk) {
            $ids = implode(',', $chunk);

            Log::info('Consultando visitas con endpoint múltiple', [
                'ids' => $ids,
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
            ]);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}"
            ])->get('https://api.mercadolibre.com/items/visits', [
                'ids' => $ids,
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
            ]);

            Log::info('Respuesta del endpoint múltiple', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                foreach ($data as $item) {
                    if (isset($item['item_id'])) {
                        $visitasPorProducto[$item['item_id']] = $item['total_visits'] ?? 0;
                    }
                }
            } else {
                Log::error('Error en endpoint múltiple', ['response' => $response->body()]);
            }
        }

        return $visitasPorProducto;
    }
}
