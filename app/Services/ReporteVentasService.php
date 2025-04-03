<?php

namespace App\Services;

use App\Models\MercadoLibreToken;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReporteVentasService
{
    public function generarReporteVentas($limit = 50, $offset = 0, $fechaInicio, $fechaFin, $diasDeRango, $item_id) {
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

        $ventasGlobales = [];
        $mlProductIds = [];
        $totalItems = 0;
        $totalPages = 0;
        $ventasPorCuenta = [];

        // Obtener total de ventas de cada cuenta
        foreach ($tokens as $token) {
            $accessToken = $token->access_token;
            $sellerId = $token->ml_account_id;
            if (empty($accessToken) || empty($sellerId)) continue;

            $ventasIniciales = $this->obtenerVentas($accessToken, 1, 0, $sellerId, $fechaInicio, $fechaFin, $item_id);
            $totalVentasCuenta = $ventasIniciales['paging']['total'] ?? 0;
            $ventasPorCuenta[$sellerId] = ['total' => $totalVentasCuenta, 'offset' => 0];
            $totalItems += $totalVentasCuenta;
        }

        // Calcular páginas totales
        $totalPages = ceil($totalItems / $limit);

        // Determinar desde qué cuenta pedir según la página seleccionada
        $acumuladorOffset = 0;
        foreach ($ventasPorCuenta as $sellerId => &$cuenta) {
            if ($offset < ($acumuladorOffset + $cuenta['total'])) {
                $cuenta['offset'] = $offset - $acumuladorOffset;
                break;
            }
            $acumuladorOffset += $cuenta['total'];
        }

        // Obtener ventas paginadas correctamente
        foreach ($ventasPorCuenta as $sellerId => $cuenta) {
            if ($cuenta['total'] == 0) continue;

            $accessToken = $tokens->where('ml_account_id', $sellerId)->first()->access_token;
            $ventas = $this->obtenerVentas($accessToken, $limit, $cuenta['offset'], $sellerId, $fechaInicio, $fechaFin, $item_id);

            if (!isset($ventas['results']) || empty($ventas['results'])) continue;

            foreach ($ventas['results'] as $venta) {
                foreach ($venta['order_items'] as $item) {
                    $mlProductId = $item['item']['id'] ?? null;
                    if ($mlProductId) {
                        $mlProductIds[] = $mlProductId;
                        $ventasGlobales[] = [
                            'producto' => $mlProductId,
                            'titulo' => $item['item']['title'] ?? null,
                            'cantidad_vendida' => $item['quantity'] ?? 0,
                            'tipo_publicacion' => $item['listing_type_id'] ?? null,
                            'fecha_venta' => $venta['date_closed'] ?? null,
                            'order_status' => $venta['status'] ?? 'unknown',
                            'seller_nickname' => $venta['seller']['nickname'] ?? 'unknown',
                            'fecha_ultima_venta' => $venta['date_closed'] ?? null,
                            'descuento' => $venta['payments'][0]['coupon_id'] ?? null,
                            'descuento_detalle' => $venta['payments'][0]['coupon_amount'] ?? 0,
                            'precio_unitario' => $item['unit_price'] ?? 0, // Agregar precio unitario
                            'precio_total' => ($item['unit_price'] ?? 0) * ($item['quantity'] ?? 0), // Precio total (unitario * cantidad)
                        ];
                    }
                }
            }
        }

        // Obtener datos adicionales de artículos
        $articulos = \DB::table('articulos')
            ->select('ml_product_id', 'imagen', 'stock_actual', 'sku', 'estado', 'permalink')
            ->whereIn('ml_product_id', $mlProductIds)
            ->get()
            ->keyBy('ml_product_id');

        foreach ($ventasGlobales as &$venta) {
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
        return [
            'total_ventas' => $totalItems,
            'ventas' => $ventasGlobales,
            'total_paginas' => $totalPages,
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
    'order.status' => 'paid', // Solo ventas pagadas
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
