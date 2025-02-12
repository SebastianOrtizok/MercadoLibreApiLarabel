<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ConsultaMercadoLibreService;
use App\Services\ReporteVentasService;
use App\Services\ReporteVentasConsolidadas;
use App\Services\MercadoLibreService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class AccountController extends Controller
{
    private $consultaService;
    private $mercadoLibreService;
    private $reporteVentasService;
    private $ReporteVentasConsolidadas;
    protected $client;

    public function __construct(ConsultaMercadoLibreService $consultaService, MercadoLibreService $mercadoLibreService, ReporteVentasService $reporteVentasService,ReporteVentasConsolidadas $ReporteVentasConsolidadas)
    {
        $this->consultaService = $consultaService;
        $this->mercadoLibreService = $mercadoLibreService;
        $this->reporteVentasService = $reporteVentasService;
        $this->ReporteVentasConsolidadas = $ReporteVentasConsolidadas;
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
        // Parámetros de paginación desde el request
        $limit = (int) $request->input('limit', 50);
        $page = (int) $request->input('page', 1); // Página actual (por defecto 1)
        $offset = ($page - 1) * $limit;
        $search = $request->input('search');
        $status = $request->input('status', 'active');
                // Si el valor de status es 'all', lo cambiamos a null o lo que tu API espera
                if ($status === 'all') {
                    $status = null;
                }
        // Obteniendo publicaciones del servicio
        $publicationsData = $this->consultaService->getOwnPublications($userId, $limit, $offset, $search, $status);

      // Procesamos las publicaciones y calculamos las páginas de cada cuenta
      $totalItems = 0;
      $totalPages = 0;
      foreach ($publicationsData['accounts'] as $account) {
          $totalItems += $account['total'];  // Total de publicaciones de la cuenta
          $totalPages += ceil($account['total'] / $limit);  // Calcula las páginas por cuenta
      }
        // Datos para la vista
        return view('dashboard.publications', [
            'publications' => $publicationsData['items'],
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'totalPublications' => $totalItems,
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
        $page = (int) $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        // Llama al servicio para obtener los items por categoría
        $response  = $this->consultaService->getItemsByCategory($categoryId, $limit, $offset);

            // Cálculo del total de páginas (opcional, si el servicio devuelve un total)
            $items = $response['items'];
            $totalItems = $response['total'] ?? 0; // Asegúrate de que el servicio devuelva este dato
            $totalPages = ceil($totalItems / $limit); // Cálculo de páginas totales

            return view('dashboard.category_items', [
                'items' => $items, // Usamos $data['items'] en lugar de $response['items']
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'limit' => $limit,
                'totalItems' => $totalItems,
                'categoryId' => $categoryId,
            ]);
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
        $ventas = $this->reporteVentasService->generarReporteVentas($limit, $offset, $fechaInicio, $fechaFin, $diasDeRango, $item_id);
      //  $ventas = $response['ventas']; // Asegúrate de que el servicio devuelva 'ventas'
      $totalVentas = $ventas['total_ventas'] ?? 0;
      $totalPages = $ventas['total_paginas'] ?? 1; // Ahora tomamos el valor del servicio

      Log::info("Total de ventas: $totalVentas, Total de páginas: $totalPages, Page seleccionada: $page");

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


public function ventas_consolidadas(Request $request, $item_id = null, $fecha_inicio = null, $fecha_fin = null)
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
        $ventas = $this->ReporteVentasConsolidadas->generarReporteVentasConsolidadas($limit, $offset, $fechaInicio, $fechaFin, $diasDeRango, $item_id);
      //  $ventas = $response['ventas']; // Asegúrate de que el servicio devuelva 'ventas'
      $totalVentas = $ventas['total_ventas'] ?? 0;
      $totalPages = $ventas['total_paginas'] ?? 1; // Ahora tomamos el valor del servicio

      Log::info("Total de ventas: $totalVentas, Total de páginas: $totalPages, Page seleccionada: $page");

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



public function sincronizacion()
{
    return view('sincronizacion.index'); // Vista para mostrar el estado de la sincronización
}


/**
* Descarga todos los artículos de la datos de MercadoLibre.
*/
public function primeraSincronizacionDB(Request $request)
{
    try {
        $userId = env('MERCADOLIBRE_USER_ID');
        // Parámetros de paginación desde el request
        $limit = (int) $request->input('limit', 50);
        $page = (int) $request->input('page', 1); // Página actual (por defecto 1)
        $offset = ($page - 1) * $limit;

        // Obteniendo publicaciones del servicio
        $publications = $this->consultaService->DescargarArticulosDB($userId, $limit, $page);

        // Almacenar o actualizar artículos en la base de datos
        foreach ($publications['items'] as $item) {
            \App\Models\Articulo::updateOrInsert(
                // Condición para identificar un registro existente
                ['ml_product_id' => $item['ml_product_id']],
                // Datos que se actualizarán o insertarán
                [
                    'user_id' => $item['token_id'],
                    'titulo' => $item['titulo'] ?? 'Sin título',
                    'imagen' => $item['imagen'] ?? null,
                    'stock_actual' => $item['stockActual'] ?? 0,
              //    'dias_stock' => $item['dias_stock'] ?? 0,
                    'precio' => $item['precio'] ?? 0.0,
                    'estado' => $item['estado'] ?? 'Desconocido',
                    'permalink' => $item['permalink'] ?? '#',
                    'condicion' => $item['condicion'] ?? 'Desconocido',
                    'sku' => $item['sku'] ?? null,
                    'tipo_publicacion' => $item['tipo_publicacion'] ?? 'Desconocido',
                    'en_catalogo' => $item['en_catalogo'] ?? false,
                ]
            );
        }

        return redirect()->back()->with('success', 'Sincronización completada con éxito.');
            } catch (\Exception $e) {
                // Redirige con un mensaje de error
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
