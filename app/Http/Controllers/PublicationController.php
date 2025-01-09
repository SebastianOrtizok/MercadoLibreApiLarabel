<?php

namespace App\Http\Controllers;
use App\Services\ConsultaMercadoLibreService;
use App\Services\MercadoLibreService;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    protected $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function index(Request $request)
    {
        try {
            // Supongamos que tienes el userId almacenado en algún lado (por ejemplo, en un .env o base de datos)
            $userId = env('MERCADOLIBRE_USER_ID');

            // Opcional: Puedes obtener limit y offset desde el request
            $limit = $request->input('limit', 50);
            $offset = $request->input('offset', 0);

            // Obteniendo publicaciones
            $publications = $this->mercadoLibreService->getOwnPublications($userId, $limit, $offset);
           // dd($publications); // Usa `dd($publications)` para revisar qué datos estás recibiendo.

            return view('dashboard.publications', compact('publications'));
            // Retornamos un JSON con las publicaciones
            // return response()->json([
            //     'status' => 'success',
            //     'data' => $publications
            // ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
