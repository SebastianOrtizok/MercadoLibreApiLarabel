<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ConsultaMercadoLibreService;
use App\Services\ReporteVentasService;
use App\Services\MercadoLibreService;
use Carbon\Carbon;

class AccountController extends Controller
{
    private $consultaService;
    private $mercadoLibreService;
    private $reporteVentasService;
    protected $client;

    public function __construct(ConsultaMercadoLibreService $consultaService, MercadoLibreService $mercadoLibreService, ReporteVentasService $reporteVentasService)
    {
        $this->consultaService = $consultaService;
        $this->mercadoLibreService = $mercadoLibreService;
        $this->reporteVentasService = $reporteVentasService;
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



    public function showInventory($sellerId, $limit = 10)
    {
        try {
            $inventory = $this->consultaService->getInventory($sellerId, $limit);
            dd($inventory);
            return response()->json($inventory);
        } catch (\Exception $e) {
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
        $search = $request->input('search'); // Término de búsqueda
        // Obteniendo publicaciones del servicio
        $publications = $this->consultaService->getOwnPublications($userId, $limit, $offset, $search);
       // $publications = $this->consultaService->getOwnPublications($userId, $limit, $offset);

        // Cálculo de total de páginas (opcional, si el servicio devuelve un total)
        $totalPublications = $publications['total'] ?? 0; // Asegúrate de que el servicio devuelva este dato
        $totalPages = ceil($totalPublications / $limit);
        // Datos para la vista
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


public function ShowSales(Request $request)
{
    try {
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);
       // $dias = $request->input('dias', 0); // Predeterminado: 10 días
        $fechaActual = Carbon::now();
        // Obtener fechas del request y convertirlas en instancias de Carbon
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Convertir las fechas a objetos Carbon
        $fechaInicio = Carbon::parse($fechaInicio);
        $fechaFin = Carbon::parse($fechaFin);
        // Calcular la diferencia en días
        $diasDeRango = $fechaInicio->diffInDays($fechaFin);

        // Llamar al servicio para generar el reporte de ventas
        $ventas = $this->reporteVentasService->generarReporteVentas($limit, $offset, $fechaInicio, $fechaFin, $diasDeRango);

        // Renderizar la vista con los datos
        return view('dashboard.order_report', compact('ventas', 'fechaInicio', 'fechaFin', 'diasDeRango'));
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
