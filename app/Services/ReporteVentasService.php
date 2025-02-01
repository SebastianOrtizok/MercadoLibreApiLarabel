<?php

namespace App\Services;

use App\Models\MercadoLibreToken;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ReporteVentasService
{
    public function generarReporteVentas($limit = 50, $offset = 0, $fechaInicio, $fechaFin,  $diasDeRango)    {
        if ( $diasDeRango == 0) {
            return [
                'total_ventas' => 0,
                'ventas' => [],
            ];
        }

        $userId = auth()->id();
        $tokens = MercadoLibreToken::where('user_id', $userId)->get();

        if ($tokens->isEmpty()) {
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        $ventasConsolidadas = [];

        // Paso 1: Recopilar todos los ml_product_id de las ventas
        $mlProductIds = [];

        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id ?? 'Cuenta desconocida';
            $accessToken = $token->access_token;

            if (empty($accessToken)) {
                continue;
            }

            $sellerId = $this->verificarSellerId($accessToken);
            if (!$sellerId) {
                continue;
            }

            $paginaActual = 1;
            $maxPaginas = 3; // Limitar el procesamiento a 3 páginas por cuenta

            do {
                $ventas = $this->obtenerVentas($accessToken, $limit, $offset, $sellerId, $fechaInicio, $fechaFin);
                $totalItems = $ventas['paging']['total'] ?? 0;
                $totalPages = ceil($totalItems / $limit);

                if (!isset($ventas['results']) || empty($ventas['results'])) {
                    break;
                }

                foreach ($ventas['results'] as $venta) {
                    foreach ($venta['order_items'] as $item) {
                        $mlProductId = $item['item']['id'] ?? null;
                        if ($mlProductId) {
                            $mlProductIds[] = $mlProductId;
                        }
                    }
                }

                $offset += $limit;
                $paginaActual++;
                usleep(500000); // Pausa de 500 milisegundos (0.5 segundos)
            } while ($paginaActual <= $totalPages && $paginaActual <= $maxPaginas);
        }

        // Paso 2: Obtener todos los detalles de los artículos en una sola consulta
        $articulos = \DB::table('articulos')
            ->select('ml_product_id', 'imagen', 'stock_actual', 'sku', 'estado', 'permalink')
            ->whereIn('ml_product_id', $mlProductIds)
            ->get()
            ->keyBy('ml_product_id'); // Convertir a un array asociativo por ml_product_id

        // Paso 3: Procesar las ventas y usar los datos de los artículos desde el array asociativo
        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id ?? 'Cuenta desconocida';
            $accessToken = $token->access_token;

            if (empty($accessToken)) {
                continue;
            }

            $sellerId = $this->verificarSellerId($accessToken);
            if (!$sellerId) {
                continue;
            }

            $paginaActual = 1;
            $maxPaginas = 3; // Limitar el procesamiento a 3 páginas por cuenta

            do {
                $ventas = $this->obtenerVentas($accessToken, $limit, $offset, $sellerId, $fechaInicio, $fechaFin);
                $totalItems = $ventas['paging']['total'] ?? 0;
                $totalPages = ceil($totalItems / $limit);

                if (!isset($ventas['results']) || empty($ventas['results'])) {
                    break;
                }

                foreach ($ventas['results'] as $venta) {
                    $fechaUltimaVenta = $venta['date_closed'] ?? null;
                    $orderStatus = $venta['status'] ?? 'unknown';

                    foreach ($venta['order_items'] as $item) {
                        $mlProductId = $item['item']['id'] ?? null;
                        $titulo = $item['item']['title'] ?? null;
                        $cantidadVendida = $item['quantity'] ?? 0;
                        $tipoPublicacion = $item['listing_type_id'] ?? null;

                        if ($mlProductId && isset($articulos[$mlProductId])) {
                            $articulo = $articulos[$mlProductId];

                            if (!isset($ventasConsolidadas[$mlProductId])) {
                                $ventasConsolidadas[$mlProductId] = [
                                    'url' => $articulo->permalink ?? null,
                                    'producto' => $mlProductId,
                                    'titulo' => $titulo,
                                    'ventas_diarias' => 0,
                                    'fecha_ultima_venta' => $fechaUltimaVenta,
                                    'tipo_publicacion' => $tipoPublicacion,
                                    'imagen' => $articulo->imagen ?? null,
                                    'stock' => $articulo->stock_actual ?? 0,
                                    'sku' => $articulo->sku ?? 0,
                                    'estado' => $articulo->estado ?? null,
                                    'dias_stock' => 0,
                                    'order_status' => $orderStatus,
                                ];
                            }

                            $ventasConsolidadas[$mlProductId]['ventas_diarias'] += $cantidadVendida;

                            if ($fechaUltimaVenta > $ventasConsolidadas[$mlProductId]['fecha_ultima_venta']) {
                                $ventasConsolidadas[$mlProductId]['fecha_ultima_venta'] = $fechaUltimaVenta;
                            }

                            $ventasDiarias = $ventasConsolidadas[$mlProductId]['ventas_diarias'];
                            if ($ventasDiarias > 0) {
                                $diasStock = round($ventasConsolidadas[$mlProductId]['stock'] / $ventasDiarias, 2);
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

    private function obtenerVentas($accessToken, $limit, $offset, $sellerId, $fechaInicio, $fechaFin, $estado = '', $search = '')
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
        //'order.status' => 'paid',  // Solo ventas pagadas
        'order.date_created.from' => $fechaInicio, // Fecha de cierre desde
        'order.date_created.to' => $fechaFin,     // Fecha de cierre hasta
    ];

    // Aplicar filtro de estado si está presente
   // if ($estado) {
   //     $params['order.status'] = $estado;
    //}

    // Aplicar búsqueda si está presente
  //  if ($search) {
  //      $params['q'] = $search;
   // }

    // Generamos la URL con los parámetros codificados
    $url = "https://api.mercadolibre.com/orders/search?" . http_build_query($params);
    //\Log::info("Consultando ventas en la URL: {$url}");

    try {
       // \Log::info("Usando token (parcial): " . substr($accessToken, 0, 40) . " para la URL: {$url}");
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
