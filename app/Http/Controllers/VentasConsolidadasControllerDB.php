<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            \Log::info('Filtros recibidos:', $request->all());
            $userId = auth()->id();
            \Log::info('User ID autenticado:', ['user_id' => $userId]);

            $limit = $request->input('limit', 50);
            $page = (int) $request->input('page', 1);
            $consolidarPorSku = $request->input('consolidar_por_sku', false) === 'true';
            $stockType = $request->input('stock_type', 'stock_actual');

            $sortColumn = $request->input('sort_column', 'cantidad_vendida');
            $sortDirection = $request->input('sort_direction', 'desc');

            $fechaInicio = Carbon::parse($fecha_inicio ?? $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d')));
            $fechaFin = Carbon::parse($fecha_fin ?? $request->input('fecha_fin', Carbon::now()->format('Y-m-d')));
            $diasDeRango = $fechaInicio->diffInDays($fechaFin) ?: 1;
            \Log::info('Rango de fechas:', ['inicio' => $fechaInicio->toDateString(), 'fin' => $fechaFin->toDateString(), 'dias' => $diasDeRango]);

            $filters = [
                'search' => $request->input('search'),
                'tipo_publicacion' => $request->input('tipo_publicacion'),
                'order_status' => $request->input('order_status'),
                'estado_publicacion' => $request->input('estado_publicacion'),
                'ml_account_id' => $request->input('ml_account_id'),
            ];
            \Log::info('Filtros aplicados:', $filters);

            $ventasData = $this->reporteVentasConsolidadasDb->generarReporteVentasConsolidadas(
                $fechaInicio,
                $fechaFin,
                $diasDeRango,
                $filters,
                $consolidarPorSku,
                $stockType,
                $sortColumn,
                $sortDirection
            );
            \Log::info('Datos de ventas:', ['count' => count($ventasData['ventas'] ?? []), 'sample' => !empty($ventasData['ventas']) ? $ventasData['ventas'][0] : null]);

            $ventasCollection = collect($ventasData['ventas'] ?? []);
            $dataToShow = $ventasCollection->forPage($page, $limit)->values();
            $totalItems = $ventasCollection->count();
            $resumenPorCuenta = $ventasData['resumen_por_cuenta_monto'] ?? [];
            $maxVentasTotal = !empty($resumenPorCuenta) ? max(array_column($resumenPorCuenta, 'cantidad_vendida')) : 1;
            $totalPages = ceil($totalItems / $limit);

            session(['ventas_consolidadas' => $ventasCollection->toArray() ?? []]);
            \Log::info('Datos pasados a la vista:', [
                'data_count' => $dataToShow->count(),
                'totalItems' => $totalItems
            ]);

            return view('dashboard.ventasconsolidadasdb', [
                'data' => $dataToShow ?? collect(),
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'diasDeRango' => $diasDeRango,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'limit' => $limit,
                'totalVentas' => $totalItems,
                'resumenPorCuenta' => $resumenPorCuenta,
                'maxVentasTotal' => $maxVentasTotal,
                'consolidarPorSku' => $consolidarPorSku,
                'stockType' => $stockType,
                'sortColumn' => $sortColumn,
                'sortDirection' => $sortDirection,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en VentasConsolidadasControllerDB: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('dashboard.ventasconsolidadasdb', [
                'data' => collect(),
                'fechaInicio' => Carbon::now()->subDays(30),
                'fechaFin' => Carbon::now(),
                'diasDeRango' => 30,
                'totalPages' => 1,
                'currentPage' => 1,
                'limit' => 50,
                'totalVentas' => 0,
                'resumenPorCuenta' => [],
                'maxVentasTotal' => 1,
                'consolidarPorSku' => false,
                'stockType' => 'stock_actual',
                'sortColumn' => 'cantidad_vendida',
                'sortDirection' => 'desc',
            ]);
        }
    }
}
