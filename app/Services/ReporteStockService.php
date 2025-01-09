<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;

class ReporteStockService
{
    private $consultaMercadoLibreService;

    public function __construct(ConsultaMercadoLibreService $consultaMercadoLibreService)
    {
        $this->consultaMercadoLibreService = $consultaMercadoLibreService;
    }

    public function generarReporteStock($userId, $limit = 50, $offset = 0)
    {
        try {
            // Obtener publicaciones del usuario
            $publicaciones = $this->consultaMercadoLibreService->getOwnPublications($userId, $limit, $offset);
            //dd($publicaciones);
            $items = $publicaciones['items'][0]['body'] ?? [];

            // Si $items es un solo artículo (array asociativo), conviértelo en un array de un elemento
            if (isset($items['id'])) {
                $items = [$items];
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

                // Obtener última venta (requiere endpoint adicional si no está disponible directamente)
                $ultimaVenta = $this->obtenerUltimaVenta($id);

                // Calcular ventasDiarias y stockEstimado (agrega los cálculos que falten)
                $ventasDiarias = 0; // Aquí puedes calcular las ventas diarias
                $stockEstimado = $stockActual; // Establecer el stock estimado (esto depende de la lógica que uses)

                // Agregar los datos al reporte
                $reporte[] = [
                    'imagen' => $imagen,
                    'titulo' => $titulo,
                    'descripcion' => $descripcion,
                    'ventasDiarias' => $ventasDiarias,
                    'stockActual' => $stockActual,
                    'stockEstimado' => round($stockEstimado, 2),
                    'estado' => $estado,
                    'ultimaVenta' => $ultimaVenta,
                ];
            }

            return $reporte;

        } catch (RequestException $e) {
            \Log::error("Error al generar el reporte de stock: " . $e->getMessage());
            throw $e;
        }
    }


    private function obtenerUltimaVenta($itemId)
    {
        // Lógica para consultar la última venta usando la API de MercadoLibre
        try {
            $response = Http::get("https://api.mercadolibre.com/items/{$itemId}/orders",  [
                'headers' => [
                    'Authorization' => "Bearer {$this->mercadoLibreService->getAccessToken($userId, $mlAccountId)}"
                ]]);

            if ($response->successful() && !empty($response['results'])) {
                $ultimaOrden = $response['results'][0];
                return $ultimaOrden['date_closed'] ?? 'Sin información';
            }

            return 'Sin información';
        } catch (\Exception $e) {
            \Log::error("Error al obtener última venta para {$itemId}: " . $e->getMessage());
            return 'Error';
        }
    }
}
