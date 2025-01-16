<?php

namespace App\Services;

use App\Models\MercadoLibreToken;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ReporteVentasService
{
    public function generarReporteVentas($limit = 50, $offset = 0, $fechaInicio, $fechaFin)
    {
        // Obtener el ID del usuario autenticado
        $userId = auth()->id();
        \Log::info("Generando reporte de ventas para el usuario ID: {$userId}, desde {$fechaInicio} hasta {$fechaFin}.");

        // Obtener los tokens asociados al usuario
        $tokens = MercadoLibreToken::where('user_id', $userId)->get();

        // Verificar si el usuario tiene tokens asociados
        if ($tokens->isEmpty()) {
            \Log::warning("No se encontraron cuentas asociadas para el usuario ID: {$userId}");
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        // Inicializar el array para almacenar las ventas consolidadas
        $ventasConsolidadas = [];

        // Iterar sobre los tokens de MercadoLibre
        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id ?? 'Cuenta desconocida';
            $accessToken = $token->access_token;

            \Log::info("Procesando cuenta: {$mlAccountId} con token asociado.");

            // Verificar si el token es válido
            if (empty($accessToken)) {
                \Log::warning("El token de la cuenta {$mlAccountId} es inválido o está vacío.");
                continue;
            }

            // Verificar si el token corresponde al vendedor correcto
            $sellerId = $this->verificarSellerId($accessToken);
            if (!$sellerId) {
                \Log::warning("El token de la cuenta {$mlAccountId} no corresponde al vendedor correcto.");
                continue;
            }

            // Obtener las ventas de la cuenta actual dentro del rango de fechas
            $ventas = $this->obtenerVentas($accessToken, $limit, $offset, $sellerId, $fechaInicio, $fechaFin);
            \Log::info("Respuesta de ventas para la cuenta {$mlAccountId}: " );

            // Verificar si se encontraron ventas
            if (!isset($ventas['results']) || empty($ventas['results'])) {
                \Log::info("No se encontraron ventas para la cuenta: {$mlAccountId}");
                continue;
            }

         // Procesar las ventas y extraer la información necesaria
         foreach ($ventas['results'] as $venta) {
            // Obtener la fecha de la última venta desde el campo 'date_closed' de la orden
            $fechaUltimaVenta = isset($venta['date_closed']) ? $venta['date_closed'] : null;

            // Recorrer los items de la orden
            foreach ($venta['order_items'] as $item) {
            // Preparar la información del producto
                $producto = [
                    'titulo' => $item['item']['title'] ?? null,  // Título del producto
                    'ventas_diarias' => $item['quantity'] ?? 0,  // Cantidad vendida
                    'fecha_ultima_venta' => $fechaUltimaVenta,  // Usamos 'date_closed' como fecha de última venta
                    'tipo_publicacion' => $item['listing_type_id'] ?? null,  // Tipo de publicación del producto
                ];

                // Agregar el producto al array de ventas consolidadas si tiene un título válido
                if ($producto['titulo']) {
                    // Agregar el producto al array de ventas consolidadas si tiene título y tipo de publicación válidos
                    $ventasConsolidadas[] = $producto;
            }
        }


        }
        \Log::info("Tipo de Publicación:", [$producto['tipo_publicacion']]);
        \Log::info("Reporte generado con un total de ventas: " . count($ventasConsolidadas));
        // Retornar las ventas consolidadas
        return [
            'total_ventas' => count($ventasConsolidadas),
            'ventas' => $ventasConsolidadas,
        ];
    }
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
