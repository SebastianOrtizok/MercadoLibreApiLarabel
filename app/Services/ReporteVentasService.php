<?php

namespace App\Services;

use App\Models\MercadoLibreToken;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReporteVentasService
{
    public function generarReporteVentas($limit = 50, $offset = 0, $fechaInicio, $fechaFin, $diasDeRango) {
        if ($diasDeRango == 0) {
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
        $mlProductIds = [];

        // Paso 1: Recopilar todos los datos de las ventas y artículos en una sola llamada
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
            do {
                Log::info('Offset:', ['offset' => $offset, 'limit' => $limit]);
                // Llamada a la API para obtener ventas
                $ventas = $this->obtenerVentas($accessToken, $limit, $offset, $sellerId, $fechaInicio, $fechaFin);
                $totalItems = $ventas['paging']['total'] ?? 0;
                $totalPages = ceil($totalItems / $limit);
                $ventas = $this->obtenerVentas($accessToken, $limit, $offset, $sellerId, $fechaInicio, $fechaFin);
                Log::info('Página actual: ' . $paginaActual);
                Log::info('Total Items:', ['totalItems' => $totalItems]);
                Log::info('Total Pages:', ['totalPages' => $totalPages]);


              //  Log::info('total Items de ventas:', $totalItems);
                if (!isset($ventas['results']) || empty($ventas['results'])) {
                    break;
                }

                foreach ($ventas['results'] as $venta) {
                    $fechaUltimaVenta = $venta['date_closed'] ?? null;
                    $orderStatus = $venta['status'] ?? 'unknown';
                    $sellernick = $venta['seller']['nickname'] ?? 'unknown';

                    foreach ($venta['order_items'] as $item) {
                        $mlProductId = $item['item']['id'] ?? null;
                        $titulo = $item['item']['title'] ?? null;
                        $cantidadVendida = $item['quantity'] ?? 0;
                        $tipoPublicacion = $item['listing_type_id'] ?? null;

                        if ($mlProductId) {
                            // Guardar los productos vendidos para después consultar sus detalles
                            $mlProductIds[] = $mlProductId;

                            if (!isset($ventasConsolidadas[$mlProductId])) {
                                $ventasConsolidadas[$mlProductId] = [
                                    'producto' => $mlProductId,
                                    'titulo' => $titulo,
                                    'ventas_diarias' => 0,
                                    'fecha_ultima_venta' => $fechaUltimaVenta,
                                    'tipo_publicacion' => $tipoPublicacion,
                                    'stock' => 0, // Inicializar el stock a 0
                                    'sku' => 0,   // Inicializar el SKU a 0
                                    'estado' => null,
                                    'order_status' => $orderStatus,
                                    'seller_nickname' => $sellernick,
                                    'dias_stock' => 0, // Inicializar días de stock
                                ];
                            }

                            // Acumular las ventas diarias
                            $ventasConsolidadas[$mlProductId]['ventas_diarias'] += $cantidadVendida;

                            if ($fechaUltimaVenta > $ventasConsolidadas[$mlProductId]['fecha_ultima_venta']) {
                                $ventasConsolidadas[$mlProductId]['fecha_ultima_venta'] = $fechaUltimaVenta;
                            }
                        }
                    }
                }

                $offset += $limit;
                $paginaActual++;

                usleep(500000); // Pausa de 500 milisegundos (0.5 segundos)
            } while ($paginaActual <= $totalPages && $totalPages > 0);

        }

        // Paso 2: Obtener todos los detalles de los artículos en una sola consulta
        $articulos = \DB::table('articulos')
            ->select('ml_product_id', 'imagen', 'stock_actual', 'sku', 'estado', 'permalink')
            ->whereIn('ml_product_id', $mlProductIds)
            ->get()
            ->keyBy('ml_product_id'); // Convertir a un array asociativo por ml_product_id

        // Paso 3: Completar los datos de cada producto y calcular el 'dias_stock'
        foreach ($ventasConsolidadas as $mlProductId => $venta) {
            if (isset($articulos[$mlProductId])) {
                $articulo = $articulos[$mlProductId];
                $ventasConsolidadas[$mlProductId]['imagen'] = $articulo->imagen;
                $ventasConsolidadas[$mlProductId]['stock'] = $articulo->stock_actual;
                $ventasConsolidadas[$mlProductId]['sku'] = $articulo->sku;
                $ventasConsolidadas[$mlProductId]['estado'] = $articulo->estado;
                $ventasConsolidadas[$mlProductId]['url'] = $articulo->permalink;

                // Calcular los días de stock
                if ($venta['ventas_diarias'] > 0 && $articulo->stock_actual > 0) {
                    $ventasConsolidadas[$mlProductId]['dias_stock'] = round($articulo->stock_actual / $venta['ventas_diarias'], 2);
                }
            }
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
      //  'sort' => 'item_id'  // Ordenar por ID del producto
    ];



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
