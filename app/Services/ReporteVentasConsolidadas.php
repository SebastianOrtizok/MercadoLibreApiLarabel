<?php

namespace App\Services;

use App\Models\MercadoLibreToken;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReporteVentasConsolidadas
{
    public function generarReporteVentasConsolidadas($limit = 50, $offset = 0, $fechaInicio, $fechaFin, $diasDeRango, $item_id) {
        if ($diasDeRango == 0) {
            return [
                'total_ventas' => 0,
                'ventas' => [],
                'total_paginas' => 0,
            ];
        }

        $userId = auth()->id();
        $tokens = MercadoLibreToken::where('user_id', $userId)->get();
        if ($tokens->isEmpty()) {
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        $ventasConsolidadas = [];
        $totalItems = 0;

        // Recorrer todas las cuentas de MercadoLibre
        foreach ($tokens as $token) {
            $accessToken = $token->access_token;
            $sellerId = $token->ml_account_id;
            if (empty($accessToken) || empty($sellerId)) continue;

            // Obtener la cantidad total de órdenes para esta cuenta
            $ventasIniciales = $this->obtenerVentas($accessToken, 1, 0, $sellerId, $fechaInicio, $fechaFin, $item_id);
            $totalVentasCuenta = $ventasIniciales['paging']['total'] ?? 0;

            // Calcular cuántas páginas hay que recorrer
            $pagina = 0;
            while ($pagina * 50 < $totalVentasCuenta) {
                $ventas = $this->obtenerVentas($accessToken, 50, $pagina * 50, $sellerId, $fechaInicio, $fechaFin, $item_id);

                if (!isset($ventas['results']) || empty($ventas['results'])) break;

                // Procesar cada venta y consolidar los productos
                foreach ($ventas['results'] as $venta) {
                    foreach ($venta['order_items'] as $item) {
                        $mlProductId = $item['item']['id'] ?? null;
                        if (!$mlProductId) continue;

                        if (!isset($ventasConsolidadas[$mlProductId])) {
                            $ventasConsolidadas[$mlProductId] = [
                                'producto' => $mlProductId,
                                'titulo' => $item['item']['title'] ?? null,
                                'cantidad_vendida' => 0,
                                'tipo_publicacion' => $item['listing_type_id'] ?? null,
                                'fecha_venta' => $venta['date_closed'] ?? null,
                                'order_status' => $venta['status'] ?? 'unknown',
                                'seller_nickname' => $venta['seller']['nickname'] ?? 'unknown',
                                'fecha_ultima_venta' => $venta['date_closed'] ?? null,
                            ];
                        }

                        // Sumar la cantidad vendida
                        $ventasConsolidadas[$mlProductId]['cantidad_vendida'] += $item['quantity'] ?? 0;

                        // Actualizar la fecha de última venta si es más reciente
                        if (strtotime($venta['date_closed']) > strtotime($ventasConsolidadas[$mlProductId]['fecha_ultima_venta'])) {
                            $ventasConsolidadas[$mlProductId]['fecha_ultima_venta'] = $venta['date_closed'];
                        }
                    }
                }

                $pagina++;
            }
        }

        // Obtener información adicional de los productos
        $articulos = \DB::table('articulos')
            ->select('ml_product_id', 'imagen', 'stock_actual', 'sku', 'estado', 'permalink')
            ->whereIn('ml_product_id', array_keys($ventasConsolidadas))
            ->get()
            ->keyBy('ml_product_id');

        foreach ($ventasConsolidadas as &$venta) {
            $mlProductId = $venta['producto'];
            if (isset($articulos[$mlProductId])) {
                $articulo = $articulos[$mlProductId];
                $venta['imagen'] = $articulo->imagen;
                $venta['stock'] = $articulo->stock_actual;
                $venta['sku'] = $articulo->sku;
                $venta['estado'] = $articulo->estado;
                $venta['url'] = $articulo->permalink;
                $venta['dias_stock'] = ($venta['cantidad_vendida'] > 0 && $articulo->stock_actual > 0) ?
                    round($articulo->stock_actual / $venta['cantidad_vendida'], 2) : null;
            }
        }

        // Recalcular el total de ítems consolidados
        $totalItems = count($ventasConsolidadas);
        $totalPaginas = ceil($totalItems / $limit);

        // Aplicar paginación después de consolidar
        $ventasPaginadas = array_slice(array_values($ventasConsolidadas), $offset, $limit);

        return [
            'total_ventas' => $totalItems,
            'ventas' => $ventasPaginadas,
            'total_paginas' => $totalPaginas,
        ];
    }




    private function obtenerVentas($accessToken, $limit, $offset, $sellerId, $fechaInicio, $fechaFin, $item_id, $estado = '', $search = '')
{
    // Zona horaria para Carbon
    Carbon::setLocale('es');
    date_default_timezone_set('America/Argentina/Buenos_Aires');

    // Convertimos las fechas de inicio y fin a formato adecuado
    $fechaInicio = Carbon::parse($fechaInicio)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
    $fechaFin = Carbon::parse($fechaFin)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
    Log::info("ESTOY EN OBTENER VENTAS : $sellerId, limit: $limit, Offset: $offset");

// Preparar los parámetros para la API
$params = [
    'seller' => $sellerId,  // ID del vendedor
    'offset' => $offset,  // Página de resultados
    'limit' => $limit,  // Límite de resultados
    //'order.status' => 'paid' Solo ventas pagadas
    'order.date_created.from' => $fechaInicio, // Fecha de creación desde
    'order.date_created.to' => $fechaFin,     // Fecha de creación hasta
];


// Si 'itemId' tiene un valor, agregarlo a los parámetros de búsqueda
if (!empty($item_id)) {
    $params['q'] = $item_id;  // Filtrar por ID del producto
}



    // Generamos la URL con los parámetros codificados
    $url = "https://api.mercadolibre.com/orders/search?" . http_build_query($params);
    Log::info("Consultando ventas en la URL: {$url}");

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
