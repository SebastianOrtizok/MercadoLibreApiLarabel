<?php

namespace App\Services;

use App\Models\MercadoLibreToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ItemVenta
{
    // Genera un reporte de ventas para el usuario autenticado
    public function item_venta($limit = 50, $offset = 0)
    {
        $userId = Auth::id();
        Log::info("Generando reporte de ventas para el usuario ID: {$userId}");

        $tokens = MercadoLibreToken::where('user_id', $userId)->get();

        if ($tokens->isEmpty()) {
            Log::warning("No se encontraron cuentas asociadas para el usuario ID: {$userId}");
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        // Obtener inventario consolidado
        $inventarioConsolidado = $this->obtenerInventario($limit);
        $inventarioIndexado = [];
        foreach ($inventarioConsolidado as $producto) {
            $itemId = $producto['id'] ?? null;
            if ($itemId) {
                $inventarioIndexado[$itemId] = $producto;
            }
        }

        $ventasConsolidadas = [];
        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id ?? 'Cuenta desconocida';
            $accessToken = $token->access_token;

            if (empty($accessToken)) {
                Log::warning("El token de la cuenta {$mlAccountId} es inválido o está vacío.");
                continue;
            }

            $sellerId = $this->verificarSellerId($accessToken);
            if (!$sellerId) {
                Log::warning("El token de la cuenta {$mlAccountId} no corresponde al vendedor correcto.");
                continue;
            }

            $ventas = $this->obtenerVentas($accessToken, $limit, $offset, $sellerId);
            if (!isset($ventas['results']) || empty($ventas['results'])) {
                Log::info("No se encontraron ventas para la cuenta: {$mlAccountId}");
                continue;
            }

            foreach ($ventas['results'] as $venta) {
                foreach ($venta['order_items'] as $item) {
                    $itemId = $item['item']['id'] ?? null;

                    // Vincular datos del inventario con el `itemID`
                    $inventarioDatos = $inventarioIndexado[$itemId] ?? [];

                    $producto = [
                        'id_producto' => $itemId,
                        'thumbnail' => $item['item']['thumbnail'] ?? null,
                        'titulo' => $item['item']['title'] ?? null,
                        'fecha_ultima_venta' => $venta['date_closed'] ?? null,
                        'stock_actual' => $inventarioDatos['available_quantity'] ?? null,
                        'estado_publicacion' => $inventarioDatos['status'] ?? null,
                    ];

                    if ($producto['id_producto']) {
                        $ventasConsolidadas[] = $producto;
                    }
                }
            }
        }
//dd($ventasConsolidadas);
        return [
            'total_ventas' => count($ventasConsolidadas),
            'ventas' => $ventasConsolidadas,
        ];
    }


    // Verifica que el token pertenece al vendedor correcto
    private function verificarSellerId($accessToken)
    {
        $response = Http::withToken($accessToken)->get('https://api.mercadolibre.com/users/me');

        if ($response->successful()) {
            return $response->json()['id'];
        } else {
            Log::error("Error al verificar el seller_id: " . $response->body());
            return null;
        }
    }

    // Obtiene las ventas del vendedor
    private function obtenerVentas($accessToken, $limit, $offset, $sellerId)
    {
        $url = "https://api.mercadolibre.com/orders/search?seller={$sellerId}&offset={$offset}&limit={$limit}";
        Log::info("Consultando ventas en la URL: {$url}");

        try {
            Log::info("Usando token (parcial): " . substr($accessToken, 0, 40) . " para la URL: {$url}");
            $response = Http::withToken($accessToken)->get($url);

            if ($response->successful()) {
                Log::info("Respuesta exitosa de la API para el token.");
                return $response->json();
            } else {
                Log::error("Error al obtener ventas: " . $response->body());
                return [];
            }
        } catch (\Exception $e) {
            Log::error("Excepción al consultar ventas: " . $e->getMessage());
            return [];
        }
    }

    // Obtiene el inventario de las publicaciones de un vendedor
    public function obtenerInventario($limit = 10)
    {
        $userId = Auth::id();
        $tokens = MercadoLibreToken::where('user_id', $userId)->get();

        $inventarioConsolidado = [];

        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id ?? 'Cuenta desconocida';
            $accessToken = $token->access_token;

            Log::info("Consultando inventario para la cuenta: {$mlAccountId}");

            if (empty($accessToken)) {
                Log::warning("El token de la cuenta {$mlAccountId} es inválido o está vacío.");
                continue;
            }

            $sellerId = $this->verificarSellerId($accessToken);
            if (!$sellerId) {
                Log::warning("El token de la cuenta {$mlAccountId} no corresponde al vendedor correcto.");
                continue;
            }

            $inventario = $this->obtenerInventarioCuenta($accessToken, $limit, $sellerId);
            Log::info("Inventario para la cuenta {$mlAccountId}: " . json_encode($inventario));

            // Agregar el inventario al inventario consolidado
            if (isset($inventario['results'])) {
                $inventarioConsolidado = array_merge($inventarioConsolidado, $inventario['results']);
            }
        }

        Log::info("Inventario consolidado obtenido.");

        return $inventarioConsolidado;
    }

    // Obtiene el inventario de una cuenta
    private function obtenerInventarioCuenta($accessToken, $limit, $sellerId)
    {
        $url = "https://api.mercadolibre.com/items/search?seller={$sellerId}&limit={$limit}";
        Log::info("Consultando inventario en la URL: {$url}");

        try {
            $response = Http::withToken($accessToken)->get($url);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error("Error al obtener el inventario de la cuenta: " . $response->body());
                return [];
            }
        } catch (\Exception $e) {
            Log::error("Excepción al consultar el inventario: " . $e->getMessage());
            return [];
        }
    }
}
