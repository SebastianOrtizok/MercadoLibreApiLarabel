<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ConsultaMercadoLibreService;
use App\Services\ReporteVentasService;
use App\Services\MercadoLibreService;

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

            // Opcional: Puedes obtener limit y offset desde el request
            $limit = $request->input('limit', 50);
            $offset = $request->input('offset', 0);

            // Obteniendo publicaciones
            $publications = $this->consultaService->getOwnPublications($userId, $limit, $offset);
           //echo gettype($publications);
           //dd($publications);
             return view('dashboard.publications', compact('publications'));

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
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

            // Llamar al servicio para obtener las ventas
            $ventas = $this->reporteVentasService->generarReporteVentas($limit, $offset);

            // Mostrar los datos en pantalla temporalmente
            dd($ventas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
