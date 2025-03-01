<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Services\ReporteVentasConsolidadasDb;

class VentasConsolidadasControllerDB extends Controller
{
    protected $reporteVentasConsolidadasDb;

    public function __construct(ReporteVentasConsolidadasDb $reporteVentasConsolidadasDb)
    {
        $this->reporteVentasConsolidadasDb = $reporteVentasConsolidadasDb;
    }

    public function ventasConsolidadas(Request $request, $fecha_inicio = null, $fecha_fin = null)
    {
        try {
            $limit = $request->input('limit', 50);
            $page = (int) $request->input('page', 1);

            // Priorizar fecha_inicio_calculada (del deslizador) si está presente
        if ($request->has('fecha_inicio_calculada') && !empty($request->input('fecha_inicio_calculada'))) {
            $fechaInicio = Carbon::parse($request->input('fecha_inicio_calculada'));
        } else {
            // Si no hay fecha_inicio_calculada, usar fecha_inicio manual o el default
            $fechaInicio = Carbon::parse($fecha_inicio ?? $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d')));
        }

            $fechaFin = Carbon::parse($fecha_fin ?? $request->input('fecha_fin', Carbon::now()->format('Y-m-d')));
            $diasDeRango = $fechaInicio->diffInDays($fechaFin) ?: 1;

            // Recoger filtros del request
            $filters = [
                'search' => $request->input('search'),
                'tipo_publicacion' => $request->input('tipo_publicacion'),
                'order_status' => $request->input('order_status'),
                'estado_publicacion' => $request->input('estado_publicacion'),
                'ml_account_id' => $request->input('ml_account_id'),
            ];

            $cacheKey = "ventas_consolidadas_db_{$fechaInicio->format('Ymd')}_{$fechaFin->format('Ymd')}_" . md5(serialize($filters));

            $ventas = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($fechaInicio, $fechaFin, $diasDeRango, $filters) {
                return $this->reporteVentasConsolidadasDb->generarReporteVentasConsolidadas($fechaInicio, $fechaFin, $diasDeRango, $filters);
            });

            $ventasCollection = collect($ventas['ventas'] ?? []);
            $ventasOrdenadas = $ventasCollection->sortBy('titulo')->values();

            $ventasConsolidadas = [];
            $ventaAnterior = null;
            $totalPorTitulo = [];
            $tituloContador = [];
            $imagenPorTitulo = [];
            $resumenPorCuenta = []; // Nuevo array para el resumen por cuenta

            foreach ($ventasOrdenadas as $venta) {
                // Calcular resumen por cuenta
                $cuenta = $venta['ml_account_id'] ?? $venta['seller_nickname'] ?? 'Desconocida';
                $resumenPorCuenta[$cuenta] = ($resumenPorCuenta[$cuenta] ?? 0) + (int)$venta['cantidad_vendida'];

                if (!isset($imagenPorTitulo[$venta['titulo']])) {
                    $imagenPorTitulo[$venta['titulo']] = $venta['imagen'];
                }
                $tituloContador[$venta['titulo']] = ($tituloContador[$venta['titulo']] ?? 0) + 1;

                if ($ventaAnterior && $venta['titulo'] != $ventaAnterior['titulo']) {
                    if ($tituloContador[$ventaAnterior['titulo']] > 1) {
                        $ventasConsolidadas[] = [
                            'producto' => 'MLA' . rand(1000000000, 9999999999),
                            'titulo' => "{$ventaAnterior['titulo']} Total",
                            'cantidad_vendida' => $totalPorTitulo[$ventaAnterior['titulo']]['cantidad'],
                            'tipo_publicacion' => 'gold_special',
                            'fecha_venta' => now()->format('Y-m-d\TH:i:s.000-04:00'),
                            'order_status' => 'paid',
                            'seller_nickname' => 'TRTEK/TROTA',
                            'fecha_ultima_venta' => $totalPorTitulo[$ventaAnterior['titulo']]['fecha_ultima_venta'],
                            'imagen' => $imagenPorTitulo[$ventaAnterior['titulo']],
                            'stock' => $totalPorTitulo[$ventaAnterior['titulo']]['stock'],
                            'sku' => 'No disp.',
                            'estado' => 'No disp.',
                            'url' => 'No disp',
                            'dias_stock' => round($totalPorTitulo[$ventaAnterior['titulo']]['stock'] / ($totalPorTitulo[$ventaAnterior['titulo']]['cantidad'] / $diasDeRango), 2),
                        ];
                    }
                    $totalPorTitulo[$ventaAnterior['titulo']] = ['cantidad' => 0, 'stock' => 0, 'fecha_ultima_venta' => null];
                }

                if (!isset($totalPorTitulo[$venta['titulo']])) {
                    $totalPorTitulo[$venta['titulo']] = ['cantidad' => 0, 'stock' => 0, 'fecha_ultima_venta' => null];
                }

                $totalPorTitulo[$venta['titulo']]['cantidad'] += $venta['cantidad_vendida'];
                $totalPorTitulo[$venta['titulo']]['stock'] = $venta['stock'];
                if (!$totalPorTitulo[$venta['titulo']]['fecha_ultima_venta'] || strtotime($venta['fecha_ultima_venta']) > strtotime($totalPorTitulo[$venta['titulo']]['fecha_ultima_venta'])) {
                    $totalPorTitulo[$venta['titulo']]['fecha_ultima_venta'] = $venta['fecha_ultima_venta'];
                }

                $ventasConsolidadas[] = $venta;
                $ventaAnterior = $venta;
            }

            if ($ventaAnterior && $tituloContador[$ventaAnterior['titulo']] > 1) {
                $ventasConsolidadas[] = [
                    'producto' => 'MLA' . rand(1000000000, 9999999999),
                    'titulo' => "{$ventaAnterior['titulo']} Total",
                    'cantidad_vendida' => $totalPorTitulo[$ventaAnterior['titulo']]['cantidad'],
                    'tipo_publicacion' => 'gold_special',
                    'fecha_venta' => now()->format('Y-m-d\TH:i:s.000-04:00'),
                    'order_status' => 'paid',
                    'seller_nickname' => 'TRTEK/TROTA',
                    'fecha_ultima_venta' => $totalPorTitulo[$ventaAnterior['titulo']]['fecha_ultima_venta'],
                    'imagen' => $imagenPorTitulo[$ventaAnterior['titulo']],
                    'stock' => $totalPorTitulo[$ventaAnterior['titulo']]['stock'],
                    'sku' => 'No disp.',
                    'estado' => 'No disp.',
                    'url' => 'No disp',
                    'dias_stock' => round($totalPorTitulo[$ventaAnterior['titulo']]['stock'] / ($totalPorTitulo[$ventaAnterior['titulo']]['cantidad'] / $diasDeRango), 2),
                ];
            }

            // Calcular el máximo valor de ventas para las barras de progreso
            $maxVentasTotal = !empty($resumenPorCuenta) ? max($resumenPorCuenta) : 1;

            $totalVentas = count($ventasConsolidadas);
            $totalPages = ceil($totalVentas / $limit);

            session(['ventas_consolidadas' => $ventasConsolidadas]);
            $ventasPaginadas = collect($ventasConsolidadas)->forPage($page, $limit)->values();

            return view('dashboard.ventasconsolidadasdb', [
                'ventas' => $ventasPaginadas,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'diasDeRango' => $diasDeRango,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'limit' => $limit,
                'totalVentas' => $totalVentas,
                'diasSeleccionados' => $request->input('dias', 30), // Valor del deslizador
                'resumenPorCuenta' => $resumenPorCuenta, // Nuevo dato para la vista
                'maxVentasTotal' => $maxVentasTotal,     // Nuevo dato para la vista
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
