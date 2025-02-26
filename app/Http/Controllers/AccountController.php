<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ConsultaMercadoLibreService;
use App\Services\ReporteVentasService;
use App\Services\ReporteVentasConsolidadas;
use App\Services\ReporteVentaConsolidada;
use App\Services\MercadoLibreService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


class AccountController extends Controller
{
    private $consultaService;
    private $mercadoLibreService;
    private $reporteVentasService;
    private $ReporteVentasConsolidadas;
    private $ReporteVentaConsolidada;
    protected $client;

    public function __construct(ConsultaMercadoLibreService $consultaService, MercadoLibreService $mercadoLibreService, ReporteVentasService $reporteVentasService,ReporteVentasConsolidadas $ReporteVentasConsolidadas,ReporteVentaConsolidada $ReporteVentaConsolidada)
    {
        $this->consultaService = $consultaService;
        $this->mercadoLibreService = $mercadoLibreService;
        $this->reporteVentasService = $reporteVentasService;
        $this->ReporteVentasConsolidadas = $ReporteVentasConsolidadas;
        $this->ReporteVentaConsolidada = $ReporteVentaConsolidada;
    }


    public function showAccountInfo()
    {
        try {
            // Obtener la información de todas las cuentas
            $accountsInfo = $this->consultaService->getAccountInfo();
            return view('dashboard.account', ['accounts' => $accountsInfo]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function showInventory(Request $request)
{
    try {
        $userProductId = $request->query('user_product_id');
        $sellerId = $request->query('ml_account_id');

        // Verifica si los valores llegan correctamente
        if (!$userProductId || !$sellerId) {
            return response()->json(['error' => 'Faltan parámetros en la URL'], 400);
        }

        $inventory = $this->consultaService->getInventory($sellerId, $userProductId);
        return response()->json($inventory);
    } catch (\Exception $e) {
        \Log::error("Error en showInventory: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function showOwnPublications(Request $request)
{
    try {
        $userId = "";
        $limit = (int) $request->input('limit', 50);
        $page = (int) $request->input('page', 1);
        $offset = ($page - 1) * $limit;
        $search = $request->input('search');
        $status = $request->input('status', 'active');

        if ($status === 'all') {
            $status = null;
        }

        // Obteniendo publicaciones del servicio
        $publications = $this->consultaService->getOwnPublications($userId, $limit, $offset, $search, $status);
        $totalPublications = $publications['total'] ?? 0;
        $totalPages = ceil($totalPublications / $limit);
        return view('dashboard.publications', [
            'publications' => $publications['items'],
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'totalPublications' => $totalPublications,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}


public function showItemsByCategory(Request $request, $categoryId)
{
    try {
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);

        // Llama al servicio para obtener los items por categoría
        $data = $this->consultaService->getItemsByCategory($categoryId, $limit, $offset);

        // Verifica si existe la clave "items" y extráela, sino usa un array vacío
        $items = $data['items'] ?? [];

        // Retorna la vista con los datos correctamente formateados
        return view('dashboard.category_items', compact('items', 'categoryId'));
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}



public function ShowSales(Request $request, $item_id = null, $fecha_inicio = null, $fecha_fin = null)
{
    try {
        $limit = $request->input('limit', 50);
        $page = (int) $request->input('page', 1); // Página actual
        $offset = ($page - 1) * $limit; // Cálculo del desplazamiento
        $fechaActual = Carbon::now();

        $fechaInicio = $fecha_inicio ?? $request->input('fecha_inicio', Carbon::now()->format('Y-m-d'));
        $fechaFin = $fecha_fin ?? $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
                // Convertir las fechas a objetos Carbon
        $fechaInicio = Carbon::parse($fechaInicio);
        $fechaFin = Carbon::parse($fechaFin);
        // Calcular la diferencia en días
        $diasDeRango = $fechaInicio->diffInDays($fechaFin);
        $totalPages = 0;
        // Llamar al servicio para generar el reporte de ventas
        $ventas = $this->reporteVentasService->generarReporteVentas($limit, $offset, $fechaInicio, $fechaFin, $diasDeRango, $item_id);
      //  $ventas = $response['ventas']; // Asegúrate de que el servicio devuelva 'ventas'
      $totalVentas = $ventas['total_ventas'] ?? 0;
      $totalPages = $ventas['total_paginas'] ?? 1; // Ahora tomamos el valor del servicio
        // Renderizar la vista con los datos de ventas y la paginación
        return view('dashboard.order_report', [
            'ventas' => $ventas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'diasDeRango' => $diasDeRango,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'totalVentas' => $totalVentas,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function ventas_consolidadas(Request $request, $fecha_inicio = null, $fecha_fin = null)
{
    try {
        // Parámetros de paginación
        $limit = $request->input('limit', 50);
        $page = (int) $request->input('page', 1);

        // Fechas
        $fechaInicio = Carbon::parse($fecha_inicio ?? $request->input('fecha_inicio', Carbon::now()->format('Y-m-d')));
        $fechaFin = Carbon::parse($fecha_fin ?? $request->input('fecha_fin', Carbon::now()->format('Y-m-d')));
        $diasDeRango = $fechaInicio->diffInDays($fechaFin);

        // Generar una clave única para la caché basada en las fechas
        $cacheKey = "ventas_consolidadas_{$fechaInicio->format('Ymd')}_{$fechaFin->format('Ymd')}";

        // Intentar obtener las ventas desde la caché
        $ventas = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($fechaInicio, $fechaFin, $diasDeRango) {
            return $this->ReporteVentasConsolidadas->generarReporteVentasConsolidadas($fechaInicio, $fechaFin, $diasDeRango);
        });

        // Convertir los resultados en una colección ordenada por título
        $ventasCollection = collect($ventas['ventas'] ?? []);
        $ventasOrdenadas = $ventasCollection->sortBy('titulo')->values();

        // Crear una lista de ventas consolidadas
        $ventasConsolidadas = [];
        $ventaAnterior = null;
        $totalPorTitulo = [];
        $tituloContador = [];
        $imagenPorTitulo = [];


        foreach ($ventasOrdenadas as $venta) {
            // Guardar la primera imagen del título
            if (!isset($imagenPorTitulo[$venta['titulo']])) {
                $imagenPorTitulo[$venta['titulo']] = $venta['imagen'];
            }
            // Contar cuántas veces aparece cada título
            $tituloContador[$venta['titulo']] = ($tituloContador[$venta['titulo']] ?? 0) + 1;

            // Si el título cambió, agregar el total consolidado del título anterior y resetear los contadores
            if ($ventaAnterior && $venta['titulo'] != $ventaAnterior['titulo']) {
                // Solo agregar el total si hay más de una venta del mismo título
                if ($tituloContador[$ventaAnterior['titulo']] > 1) {
                    // Agregar el total consolidado por título
                    $ventasConsolidadas[] = [
                        'producto' => 'MLA' . rand(1000000000, 9999999999),
                        'titulo' => "{$ventaAnterior['titulo']} Total",
                        'cantidad_vendida' => $totalPorTitulo[$ventaAnterior['titulo']]['cantidad'],
                        'tipo_publicacion' => 'gold_special',
                        'fecha_venta' => now()->format('Y-m-d\TH:i:s.000-04:00'),
                        'order_status' => 'paid',
                        'seller_nickname' => 'TRTEK/TROTA',
                        'fecha_ultima_venta' => now()->format('Y-m-d\TH:i:s.000-04:00'),
                        'imagen' => $imagenPorTitulo[$ventaAnterior['titulo']],
                        'stock' => $totalPorTitulo[$ventaAnterior['titulo']]['stock'],
                        'sku' => 'No disp.',
                        'estado' => 'No disp.',
                        'url' => 'No disp',
                        'dias_stock' => round($totalPorTitulo[$ventaAnterior['titulo']]['stock'] / ($totalPorTitulo[$ventaAnterior['titulo']]['cantidad'] / $diasDeRango), 2),
                    ];
                }
                $totalPorTitulo[$ventaAnterior['titulo']] = ['cantidad' => 0, 'stock' => 0];  // Resetear el total por título
            }

            // Si es un nuevo título, agregar la venta
            if (!isset($totalPorTitulo[$venta['titulo']])) {
                $totalPorTitulo[$venta['titulo']] = ['cantidad' => 0, 'stock' => 0];
            }

            // Sumar la cantidad vendida y el stock por título
            $totalPorTitulo[$venta['titulo']]['cantidad'] += $venta['cantidad_vendida'];
            $totalPorTitulo[$venta['titulo']]['stock'] += $venta['stock'];

            // Agregar la venta individual (detalle)
            $ventasConsolidadas[] = $venta;
            $ventaAnterior = $venta;
        }

        // Agregar el total del último título
        if ($ventaAnterior) {
            // Solo agregar el total si hay más de una venta del mismo título
            if ($tituloContador[$ventaAnterior['titulo']] > 1) {
                // Agregar el total consolidado por título
                $ventasConsolidadas[] = [
                    'producto' => 'MLA' . rand(1000000000, 9999999999),
                    'titulo' => "{$ventaAnterior['titulo']} Total",
                    'cantidad_vendida' => $totalPorTitulo[$ventaAnterior['titulo']]['cantidad'],
                    'tipo_publicacion' => 'gold_special',
                    'fecha_venta' => now()->format('Y-m-d\TH:i:s.000-04:00'),
                    'order_status' => 'paid',
                    'seller_nickname' => 'TRTEK/TROTA',
                    'fecha_ultima_venta' => now()->format('Y-m-d\TH:i:s.000-04:00'),
                    'imagen' => $imagenPorTitulo[$ventaAnterior['titulo']],
                    'stock' => $totalPorTitulo[$ventaAnterior['titulo']]['stock'],
                    'sku' => 'No disp.',
                    'estado' => 'No disp.',
                    'url' => 'No disp',
                    'dias_stock' => round($totalPorTitulo[$ventaAnterior['titulo']]['stock'] / ($totalPorTitulo[$ventaAnterior['titulo']]['cantidad'] / $diasDeRango), 2),
                ];
            }
        }

        // Calcular el total de ventas y total de páginas
        $totalVentas = count($ventasConsolidadas);
        $totalPages = ceil($totalVentas / $limit);
        session(['ventas_consolidadas' => $ventasConsolidadas]);
        // Paginación manual usando la colección (sin hacer una nueva llamada a la API)
        $ventasPaginadas = collect($ventasConsolidadas)->forPage($page, $limit)->values();
        // Retornar la vista con los datos paginados
        return view('dashboard.ventasconsolidadas', [
            'ventas' => $ventasPaginadas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'diasDeRango' => $diasDeRango,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'totalVentas' => $totalVentas,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}








public function venta_consolidada(Request $request, $item_id = null, $fecha_inicio = null, $fecha_fin = null)
{
    try {
        $limit = $request->input('limit', 50);
        $page = (int) $request->input('page', 1); // Página actual
        $offset = ($page - 1) * $limit; // Cálculo del desplazamiento
       // $dias = $request->input('dias', 0); // Predeterminado: 10 días
        $fechaActual = Carbon::now();
        // Obtener fechas del request y convertirlas en instancias de Carbon
        // Obtener fechas desde la URL si están presentes, sino desde el formulario
        $fechaInicio = $fecha_inicio ?? $request->input('fecha_inicio', Carbon::now()->format('Y-m-d'));
        $fechaFin = $fecha_fin ?? $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
                // Convertir las fechas a objetos Carbon
        $fechaInicio = Carbon::parse($fechaInicio);
        $fechaFin = Carbon::parse($fechaFin);
        // Calcular la diferencia en días
        $diasDeRango = $fechaInicio->diffInDays($fechaFin);
        $totalPages = 0;
        // Llamar al servicio para generar el reporte de ventas
        $ventas = $this->ReporteVentaConsolidada->generarReporteVentaConsolidada($limit, $offset, $fechaInicio, $fechaFin, $diasDeRango, $item_id);
      //  $ventas = $response['ventas']; // Asegúrate de que el servicio devuelva 'ventas'
      $totalVentas = $ventas['total_ventas'] ?? 0;
      $totalPages = $ventas['total_paginas'] ?? 1; // Ahora tomamos el valor del servicio

        // Renderizar la vista con los datos de ventas y la paginación
        return view('dashboard.order_report', [
            'ventas' => $ventas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'diasDeRango' => $diasDeRango,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'totalVentas' => $totalVentas,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}




public function sincronizacion(Request $request)
    {
        $userId = auth()->id();
        $cuentas = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();

        return view('sincronizacion.index', [
            'cuentas' => $cuentas,
            'dateFromDefault' => Carbon::today()->format('Y-m-d'),
            'dateToDefault' => Carbon::today()->format('Y-m-d'),
        ]);
    }



/**
* Descarga todos los artículos de la datos de MercadoLibre.
*/
public function primeraSincronizacionDB(Request $request, $user_id)
{
    try {
        $usuarioAutenticado = auth()->id(); // Obtener el ID del usuario logueado

        // Verificar si el usuario autenticado tiene acceso a la cuenta de MercadoLibre seleccionada
        $cuenta = \App\Models\MercadoLibreToken::where('ml_account_id', $user_id)
                    ->where('user_id', $usuarioAutenticado)
                    ->first();

        if (!$cuenta) {
            return redirect()->back()->with('error', 'No tienes permiso para sincronizar esta cuenta.');
        }
        $token = $cuenta->access_token;

        // Si la cuenta pertenece al usuario, continuar con la sincronización
        $limit = (int) $request->input('limit', 50);
        $page = (int) $request->input('page', 1);
        $offset = ($page - 1) * $limit;
        $publications = $this->consultaService->DescargarArticulosDB($user_id, $token, $limit, $offset);

        foreach ($publications['items'] as $item) {
            \App\Models\Articulo::updateOrInsert(
                ['ml_product_id' => $item['ml_product_id']],
                [
                    'user_id' => $item['token_id'],
                    'titulo' => $item['titulo'] ?? 'Sin título',
                    'imagen' => $item['imagen'] ?? null,
                    'stock_actual' => $item['stockActual'] ?? 0,
                    'precio' => $item['precio'] ?? 0.0,
                    'estado' => $item['estado'] ?? 'Desconocido',
                    'permalink' => $item['permalink'] ?? '#',
                    'condicion' => $item['condicion'] ?? 'Desconocido',
                    'sku' => $item['sku'] ?? null,
                    'tipo_publicacion' => $item['tipoPublicacion'] ?? 'Desconocido',
                    'en_catalogo' => $item['enCatalogo'] ?? false,
                    'logistic_type' => $item['logistic_type'] ?? null,
                    'inventory_id' => $item['inventory_id'] ?? null,
                    'user_product_id' => $item['user_product_id'] ?? null,
                    'precio_original' => $item['precio_original'] ?? null,
                    'category_id' => $item['category_id'] ?? null,
                    'en_promocion' => $item['en_promocion'] ?? false,
                    'descuento_porcentaje' => $item['descuento_porcentaje'] ?? null,
                    'deal_ids' => $item['deal_ids'] ?? '[]',
                ]
            );
        }

        return redirect()->back()->with('success', 'Sincronización completada con éxito.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error al sincronizar los artículos: ' . $e->getMessage());
    }
}




     /**
     * Sincroniza los artículos de la datos de MercadoLibre.
     */
    public function actualizarArticulosDB(Request $request)
    {
        try {
            $userId = env('MERCADOLIBRE_USER_ID');
            // Parámetros de paginación desde el request
            $limit = (int) $request->input('limit', 50);
            $page = (int) $request->input('page', 1); // Página actual (por defecto 1)
            $offset = ($page - 1) * $limit;

            $syncService = $this->consultaService->sincronizarBaseDeDatos($userId, $limit, $page);

           // Mensaje de éxito
           return redirect()->back()->with('success', 'Sincronización completada con éxito.');
            } catch (\Exception $e) {
                // Mensaje de error
                return redirect()->back()->with('error', 'Error al sincronizar los artículos: ' . $e->getMessage());
            }
        }


}
