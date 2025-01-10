<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ConsultaMercadoLibreService;
use App\Services\ReporteStockService;
use App\Services\MercadoLibreService;

class AccountController extends Controller
{
    private $consultaService;
    private $mercadoLibreService;
    protected $client;
    public function __construct(ConsultaMercadoLibreService $consultaService, MercadoLibreService $mercadoLibreService)
    {
        $this->consultaService = $consultaService;
        $this->mercadoLibreService = $mercadoLibreService;

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
            //dd($publications); // Usa `dd($publications)` para revisar qué datos estás recibiendo.

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

public function analyzeLowConversion(Request $request)
{
    try {
        $userId = env('MERCADOLIBRE_USER_ID');
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);

        // Obtener las publicaciones propias
        $publications = $this->consultaService->getOwnPublications($userId, $limit, $offset);
        // Obtener vistas de cada producto
        foreach ($publications['items'] as &$publication) {
            $itemId = $publication['body']['id']; // Asegúrate de que 'id' esté en el array
            $publication['visits'] = $this->consultaService->getProductVisits($itemId);

        }



        return view('dashboard.low_conversion', compact('publications'));
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
        $userId = env('MERCADOLIBRE_USER_ID');
        $reporteStockService = app(ReporteStockService::class);
        $reporte = $reporteStockService->generarReporteStock($userId);
//dd($reporte);
        return view('dashboard.stock_report', ['reporte' => $reporte]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


}
