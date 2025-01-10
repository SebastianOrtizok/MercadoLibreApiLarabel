<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;


class ReporteStockService
{
    private $consultaMercadoLibreService;
    private $mercadoLibreService;

    public function __construct(
        ConsultaMercadoLibreService $consultaMercadoLibreService,
        MercadoLibreService $mercadoLibreService
    ) {
        $this->consultaMercadoLibreService = $consultaMercadoLibreService;
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function generarReporteStock($limit = 50, $offset = 0)
    {
        try {

           // Obtener el userId utilizando Auth
            $userId = Auth::id();  // userId correcto
           // $userData = User::find($userId);  // Obtener los datos del usuario desde la base de datos

            dd('User ID: ' . $userId);
            // Obtener publicaciones del usuario
            $publicaciones = $this->consultaMercadoLibreService->getOwnPublications($userId, $limit, $offset);

            // Validar si las publicaciones fueron recuperadas correctamente
            $items = $publicaciones['items'][0]['body'] ?? [];
            if (isset($items['id'])) {
                $items = [$items]; // Si es un solo artículo, lo convertimos a un array
            }

            $reporte = [];

            // Recorremos los artículos
            foreach ($items as $item) {
                $id = $item['id'] ?? null;
                $titulo = $item['title'] ?? 'Sin título';
                $descripcion = $item['description'] ?? 'Sin descripción';
                $imagen = $item['pictures'][0]['url'] ?? null;
                $stockActual = $item['available_quantity'] ?? 0;
                $estado = $item['status'] ?? 'Desconocido';
                $permalink = $item['permalink'] ?? '#';

                // Llamada para obtener la última venta
                $ultimaVenta = $this->obtenerUltimaVenta($id, $userId, $mlAccountId);

                $ventasDiarias = 0;  // Aquí puedes agregar lógica para calcular ventas diarias
                $stockEstimado = $stockActual;  // O también calcular el stock estimado

                // Agregar los datos al reporte
                $reporte[] = [
                    'id' => $id,
                    'imagen' => $imagen,
                    'titulo' => $titulo,
                    'descripcion' => $descripcion,
                    'ventasDiarias' => $ventasDiarias,
                    'stockActual' => $stockActual,
                    'stockEstimado' => round($stockEstimado, 2),
                    'estado' => $estado,
                    'ultimaVenta' => $ultimaVenta,
                    'permalink' => $permalink,
                ];
            }

            return $reporte;

        } catch (RequestException $e) {
            \Log::error("Error al generar el reporte de stock: " . $e->getMessage());
            throw $e;
        }
    }




    private function obtenerUltimaVenta($itemId, $userId, $mlAccountId)
{
    try {
        // Verificamos si itemId está vacío

        // Obtener el accessToken directamente desde el servicio MercadoLibre
        $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);

        // Imprimimos las variables para depurar


        if (empty($accessToken)) {
            \Log::error("Token no encontrado para el usuario y cuenta de MercadoLibre.");
            return 'Token no encontrado';
        }

        // Llamada a la API para obtener las órdenes
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}"
        ])->get("https://api.mercadolibre.com/items/{$itemId}/sales");  // Cambio de "orders" por "sales"


        if ($response->successful()) {
            $results = $response->json('results') ?? [];

           // dd("Respuesta de la API:", $results);  // Depuración de la respuesta

            if (!empty($results)) {
                $ultimaOrden = $results[0];
                return $ultimaOrden['date_closed'] ?? 'Sin información';
            } else {
                return 'Sin ventas registradas';
            }
        }

        \Log::error("Error en la respuesta de la API: " . $response->body());
        return 'Error en la API';
    } catch (\Exception $e) {
        \Log::error("Excepción al obtener última venta para {$itemId}: " . $e->getMessage());
        dd("Excepción al obtener última venta:", $e->getMessage(), $itemId, $userId, $mlAccountId); // Detenemos el flujo en caso de error
        return 'Error';
    }
}



}
