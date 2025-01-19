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
            //dd($accountsInfo);
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
        // Supongamos que tienes el userId almacenado en algún lado (por ejemplo, en un .env o base de datos)
        $userId = env('MERCADOLIBRE_USER_ID');

        // Parámetros de paginación desde el request
        $limit = (int) $request->input('limit', 50);
        $page = (int) $request->input('page', 1); // Página actual (por defecto 1)
        $offset = ($page - 1) * $limit;

        // Obteniendo publicaciones del servicio
        $publications = $this->consultaService->getOwnPublications($userId, $limit, $offset);

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
        $offset = $request->input('offset', 0);

        // Llama al servicio para obtener los items por categoría
        $items = $this->consultaService->getItemsByCategory($categoryId, $limit, $offset);
        dd($items);
        // Retorna una vista con los datos
        return view('dashboard.category_items', compact('items', 'categoryId'));
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

public function generarReporte(Request $request)
{
    try {
        // Obtener las cuentas del usuario (suponiendo que las tienes almacenadas)
        $cuentas = $this->getUserAccounts(); // Obtén los tokens de las cuentas
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);
        $reporte = [];

        foreach ($cuentas as $cuenta) {
            $accessToken = $cuenta['access_token'] ?? null;
            $aliasCuenta = $cuenta['alias'] ?? 'Cuenta desconocida';

            if (empty($accessToken)) {
                \Log::warning("La cuenta {$aliasCuenta} no tiene un token válido.");
                continue;
            }

            // Obtener ventas de la cuenta actual
            $ventas = $this->getSales($accessToken, $limit, $offset);

            if (!isset($ventas['results']) || empty($ventas['results'])) {
                \Log::info("No se encontraron ventas para la cuenta: {$aliasCuenta}");
                continue;
            }

            // Procesar ventas
            foreach ($ventas['results'] as $venta) {
                $reporte[] = [
                    'id' => $venta['id'] ?? 'Desconocido',
                    'producto' => $venta['title'] ?? 'Sin título',
                    'cantidad' => $venta['quantity'] ?? 0,
                    'precio' => $venta['price'] ?? 0.0,
                    'fecha' => $venta['date_created'] ?? 'Sin fecha',
                    'estado' => $venta['status'] ?? 'Desconocido',
                    'cuenta' => $aliasCuenta,
                ];
            }
        }

        // Retornar la vista con el reporte
        return view('dashboard.stock_report', ['reporte' => $reporte]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function ShowSales(Request $request)
{
    try {
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);
        $dias = $request->input('dias', 1); // Predeterminado: 10 días

        // Obtener la fecha actual
        $fechaActual = Carbon::now();

        // Calcular la fecha de inicio según los días seleccionados

        $fechaInicio = $fechaActual->copy()->subDays($dias)->startOfDay();
        $fechaFin = $fechaActual->copy()->endOfDay();  // Hacer una copia para que no modifique $fechaActual
        $diasDeRango = round($fechaInicio->diffInDays($fechaFin));

  // Llamar al servicio para generar el reporte de ventas
  $ventas = $this->reporteVentasService->generarReporteVentas($limit, $offset, $fechaInicio, $fechaFin);

        // Renderizar la vista con los datos
        return view('dashboard.order_report', compact('ventas', 'fechaInicio', 'fechaFin', 'dias', 'diasDeRango'));
      } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function index()
{
    return view('sincronizacion.index'); // Vista para mostrar el estado de la sincronización
}


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

        // Almacenar artículos en la base de datos sin mostrar nada en la interfaz
        foreach ($publications['items'] as $item) {
            \App\Models\Articulo::create([
                'user_id' => $item['token_id'],
                'ml_product_id' => $item['ml_product_id'],
                'titulo' => $item['titulo'] ?? 'Sin título',
                'imagen' => $item['imagen'] ?? null,
                'stock_actual' => $item['stockActual'] ?? 0,
                'dias_stock' => $item['dias_stock'] ?? 0,
                'precio' => $item['precio'] ?? 0.0,
                'estado' => $item['estado'] ?? 'Desconocido',
                'permalink' => $item['permalink'] ?? '#',
                'condicion' => $item['condicion'] ?? 'Desconocido',
                'sku' => $item['sku'] ?? null,
                'tipo_publicacion' => $item['tipo_publicacion'] ?? 'Desconocido',
                'en_catalogo' => $item['en_catalogo'] ?? false,
            ]);
        }


        // Respuesta de éxito sin mostrar los artículos
        return response()->json([
            'status' => 'success',
            'message' => 'Artículos sincronizados y guardados correctamente.',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}




}
