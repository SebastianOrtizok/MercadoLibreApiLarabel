<?php

namespace App\Services;

use App\Models\MercadoLibreToken;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ReporteVentasService
{
    public function generarReporteVentas($limit = 50, $offset = 0, $fechaInicio, $fechaFin)
    {
        $userId = auth()->id();
        \Log::info("Generando reporte de ventas para el usuario ID: {$userId}, desde {$fechaInicio} hasta {$fechaFin}.");

        $tokens = MercadoLibreToken::where('user_id', $userId)->get();

        if ($tokens->isEmpty()) {
            \Log::warning("No se encontraron cuentas asociadas para el usuario ID: {$userId}");
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        $ventasConsolidadas = [];

        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id ?? 'Cuenta desconocida';
            $accessToken = $token->access_token;

            \Log::info("Procesando cuenta: {$mlAccountId} con token asociado.");

            if (empty($accessToken)) {
                \Log::warning("El token de la cuenta {$mlAccountId} es inválido o está vacío.");
                continue;
            }

            $sellerId = $this->verificarSellerId($accessToken);
            if (!$sellerId) {
                \Log::warning("El token de la cuenta {$mlAccountId} no corresponde al vendedor correcto.");
                continue;
            }

            $paginaActual = 1;
            $maxPaginas = 7; // Limitar el procesamiento a 4 páginas por cuenta

            do {
                $ventas = $this->obtenerVentas($accessToken, $limit, $offset, $sellerId, $fechaInicio, $fechaFin);
                $totalItems = $ventas['paging']['total'] ?? 0;
                $totalPages = ceil($totalItems / $limit);

                \Log::info("Cuenta {$mlAccountId}: Página {$paginaActual} de {$totalPages}. Offset actual: {$offset}.");

                if (!isset($ventas['results']) || empty($ventas['results'])) {
                    \Log::info("No se encontraron ventas en la página {$paginaActual} para la cuenta {$mlAccountId}.");
                    break;
                }

                foreach ($ventas['results'] as $venta) {
                    $fechaUltimaVenta = $venta['date_closed'] ?? null;

                    foreach ($venta['order_items'] as $item) {
                        $mlProductId = $item['item']['id'] ?? null;
                        $titulo = $item['item']['title'] ?? null;
                        $cantidadVendida = $item['quantity'] ?? 0;
                        $tipoPublicacion = $item['listing_type_id'] ?? null;

                        if ($mlProductId) {
                            $articulo = \DB::table('articulos')
                                ->select('imagen', 'stock_actual', 'sku', 'estado')
                                ->where('ml_product_id', $mlProductId)
                                ->first();

                            $imagen = $articulo->imagen ?? null;
                            $stock = $articulo->stock_actual ?? 0;
                            $sku = $articulo->sku ?? 0;
                            $estado = $articulo->estado ?? null;

                            if (!isset($ventasConsolidadas[$mlProductId])) {
                                $ventasConsolidadas[$mlProductId] = [
                                    'producto' => $mlProductId,
                                    'titulo' => $titulo,
                                    'ventas_diarias' => 0,
                                    'fecha_ultima_venta' => $fechaUltimaVenta,
                                    'tipo_publicacion' => $tipoPublicacion,
                                    'imagen' => $imagen,
                                    'stock' => $stock,
                                    'sku' => $sku,
                                    'estado' => $estado,
                                    'dias_stock' => 0, // Nueva columna para los días de stock
                                ];
                            }

                            $ventasConsolidadas[$mlProductId]['ventas_diarias'] += $cantidadVendida;

                            // Actualiza la fecha de la última venta si es más reciente
                            if ($fechaUltimaVenta > $ventasConsolidadas[$mlProductId]['fecha_ultima_venta']) {
                                $ventasConsolidadas[$mlProductId]['fecha_ultima_venta'] = $fechaUltimaVenta;
                            }

                            // Calcular los días de stock restantes
                            $ventasDiarias = $ventasConsolidadas[$mlProductId]['ventas_diarias'];
                            if ($ventasDiarias > 0) {
                                $diasStock = round($stock / $ventasDiarias, 2); // Días de stock con dos decimales
                                $ventasConsolidadas[$mlProductId]['dias_stock'] = $diasStock;
                            }
                        }

                    }
                }

                $offset += $limit;
                $paginaActual++;
                usleep(500000); // Pausa de 500 milisegundos (0.5 segundos)
            } while ($paginaActual <= $totalPages && $paginaActual <= $maxPaginas);
        }

        return [
            'total_ventas' => count($ventasConsolidadas),
            'ventas' => array_values($ventasConsolidadas),
        ];
    }



    private function verificarSellerId($accessToken)
    {
        // Consultar el ID del vendedor usando el token
        $response = Http::withToken($accessToken)->get('https://api.mercadolibre.com/users/me');

        if ($response->successful()) {
            // Retornar el seller ID
            return $response->json()['id'];
        } else {
            // Si la respuesta no es exitosa, registrar el error
            \Log::error("Error al verificar el seller_id: " . $response->body());
            return null;
        }
    }

    private function obtenerVentas($accessToken, $limit, $offset, $sellerId, $fechaInicio, $fechaFin)
{
    // Zona horaria para Carbon
    Carbon::setLocale('es');
    date_default_timezone_set('America/Argentina/Buenos_Aires');

// Convertimos las fechas de inicio y fin a formato adecuado
$fechaInicio = Carbon::parse($fechaInicio)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
$fechaFin = Carbon::parse($fechaFin)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

// Parámetros para la consulta con el filtro de fecha de cierre
$params = [
    'seller' => $sellerId,  // ID del vendedor
    'offset' => $offset,  // Página de resultados
    'limit' => $limit,  // Límite de resultados
    'order.status' => 'paid',  // Solo ventas pagadas
    'order.date_created.from' => $fechaInicio, // Fecha de cierre desde
    'order.date_created.to' => $fechaFin,     // Fecha de cierre hasta
];

// Generamos la URL con los parámetros codificados
$url = "https://api.mercadolibre.com/orders/search?" . http_build_query($params);
\Log::info("Consultando ventas en la URL: {$url}");

    try {
        \Log::info("Usando token (parcial): " . substr($accessToken, 0, 40) . " para la URL: {$url}");
        // Realizar la solicitud HTTP a la API de MercadoLibre
        $response = Http::withToken($accessToken)->get($url);

        // Verificar si la respuesta fue exitosa
        if ($response->successful()) {
            return $response->json();
        } else {
            // Si la respuesta no es exitosa, registrar el error
            \Log::error("Error al obtener ventas: " . $response->body());
            return [];
        }
    } catch (\Exception $e) {
        // Manejar excepciones
        \Log::error("Excepción al consultar ventas: " . $e->getMessage());
        return [];
    }
}
}
