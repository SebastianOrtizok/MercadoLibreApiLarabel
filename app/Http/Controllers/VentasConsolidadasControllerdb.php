<?php
// app/Http/Controllers/VentasConsolidadasControllerDB.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Services\ReporteVentasConsolidadasDb;
use App\Services\SinVentasService;

class VentasConsolidadasControllerDB extends Controller
{
    protected $reporteVentasConsolidadasDb;
    protected $sinVentasService;

    public function __construct(ReporteVentasConsolidadasDb $reporteVentasConsolidadasDb, SinVentasService $sinVentasService)
    {
        $this->reporteVentasConsolidadasDb = $reporteVentasConsolidadasDb;
        $this->sinVentasService = $sinVentasService;
    }

    public function ventasConsolidadas(Request $request, $fecha_inicio = null, $fecha_fin = null)
    {

        $showSinVentas = $request->input('sin_ventas', false);
        try {
            \Log::info('Filtros recibidos en el controlador:', $request->all());
            $limit = $request->input('limit', 50);
            $page = (int) $request->input('page', 1);

            $fechaInicio = Carbon::parse($fecha_inicio ?? $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d')));
            $fechaFin = Carbon::parse($fecha_fin ?? $request->input('fecha_fin', Carbon::now()->format('Y-m-d')));
            $diasDeRango = $fechaInicio->diffInDays($fechaFin) ?: 1;

            $filters = [
                'search' => $request->input('search'),
                'tipo_publicacion' => $request->input('tipo_publicacion'),
                'order_status' => $request->input('order_status'),
                'estado_publicacion' => $request->input('estado_publicacion'),
                'ml_account_id' => $request->input('ml_account_id'),
            ];

            if ($showSinVentas) {
                $sinVentasData = $this->sinVentasService->getProductosOrdenadosPorVentas($fechaInicio, $fechaFin, $filters);
                $dataToShow = collect($sinVentasData)->filter(function ($item) {
                    return $item->cantidad_vendida == 0;
                })->map(function ($item) {
                    $arrayItem = (array) $item;
                    // Normalizar claves para consistencia con la vista
                    $arrayItem['producto'] = $arrayItem['ml_product_id'];
                    $arrayItem['url'] = $arrayItem['permalink'];
                    $arrayItem['fecha_ultima_venta'] = null; // Agregar clave faltante como null
                    unset($arrayItem['ml_product_id']);
                    unset($arrayItem['permalink']);
                    return $arrayItem;
                })->forPage($page, $limit)->values();
                $totalItems = count($sinVentasData);
                $resumenPorCuenta = [];
            } else {
                $ventasData = $this->reporteVentasConsolidadasDb->generarReporteVentasConsolidadas($fechaInicio, $fechaFin, $diasDeRango, $filters);
                $ventasCollection = collect($ventasData['ventas'] ?? []);
                $ventasOrdenadas = $ventasCollection->sortBy('titulo')->values();
                $ventasConsolidadas = [];
                $ventaAnterior = null;
                $totalPorTitulo = [];
                $tituloContador = [];
                $imagenPorTitulo = [];
                $resumenPorCuenta = [];

                foreach ($ventasOrdenadas as $venta) {
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

                $dataToShow = collect($ventasConsolidadas)->forPage($page, $limit)->values();
                $totalItems = count($ventasConsolidadas);
            }

            $maxVentasTotal = !empty($resumenPorCuenta) ? max($resumenPorCuenta) : 1;
            $totalPages = ceil($totalItems / $limit);

            session(['ventas_consolidadas' => $ventasConsolidadas ?? []]);
            return view('dashboard.ventasconsolidadasdb', [
                'data' => $dataToShow,
                'showSinVentas' => $showSinVentas,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'diasDeRango' => $diasDeRango,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'limit' => $limit,
                'totalVentas' => $totalItems,
                'resumenPorCuenta' => $resumenPorCuenta,
                'maxVentasTotal' => $maxVentasTotal,
            ]);

        } catch (\Exception $e) {
            $showSinVentas = $request->input('sin_ventas', false);
            return view('dashboard.ventasconsolidadasdb', [
                'data' => collect(),
                'showSinVentas' => $showSinVentas, // Asegurar que siempre esté definida
                'fechaInicio' => Carbon::now()->subDays(30),
                'fechaFin' => Carbon::now(),
                'diasDeRango' => 30,
                'totalPages' => 1,
                'currentPage' => 1,
                'limit' => 50,
                'totalVentas' => 0,
                'resumenPorCuenta' => [],
                'maxVentasTotal' => 1,
            ]);
        }
    }
}
