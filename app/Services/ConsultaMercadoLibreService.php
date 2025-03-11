<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Articulo;
use App\Models\SyncTimestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ConsultaMercadoLibreService
{
    private $client;
    private $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->client = $mercadoLibreService->getHttpClient();
        $this->mercadoLibreService = $mercadoLibreService;
    }

        // Verifica si el usuario está autenticado
        private function getUserId()
        {
            // Si no hay usuario autenticado, redirigir a login
            if (!Auth::check()) {
                // Redirige a la página de login si no está autenticado
                return redirect()->route('login');
            }

            // Obtener el userId del usuario autenticado
            $userId = Auth::id();

            // Consultar el ml_account_id desde la tabla 'mercadolibre_tokens' usando el user_id
            $mercadoLibreToken = \App\Models\MercadoLibreToken::where('user_id', $userId)->first(); // Asumiendo que el modelo es MercadoLibreToken y tiene la relación adecuada

            // Verifica si se encontró el ml_account_id
            if (!$mercadoLibreToken || !$mercadoLibreToken->ml_account_id) {
                // Maneja el caso si no hay ml_account_id
                return back()->with('error', 'No se encontró el dato');
            }

            // Devuelve el ml_account_id
            $mlAccountId = $mercadoLibreToken->ml_account_id;
            //dd("User ID: " . $userId . " | ML Account ID: " . $mlAccountId);

            return [
                'userId' => $userId,
                'mlAccountId' => $mercadoLibreToken->ml_account_id
            ];
        }



        public function getInventory($sellerId, $userProductId)
        {
            try {
                // Obtener los datos del usuario
                $userData = $this->getUserId();
                $userId = $userData['userId'];

                // Obtener el token de acceso
                $accessToken = $this->mercadoLibreService->getAccessToken($userId, $sellerId);

                // Llamada al endpoint para obtener los detalles del producto
                $response = $this->client->get("https://api.mercadolibre.com/user-products/{$userProductId}/stock", [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}"
                    ]
                ]);

                // Decodificar la respuesta JSON para obtener los detalles del producto
                $productDetails = json_decode($response->getBody(), true);
                // Verificar si la respuesta está vacía o contiene error
                if (!$productDetails || isset($productDetails['error'])) {
                    \Log::error("Datos del producto vacíos o con error: " . json_encode($productDetails));
                    throw new \Exception('Error al obtener los datos del producto');
                }

                return $productDetails;
            } catch (\Exception $e) {
                throw $e;
            }
        }





    public function getAccountInfo()
{
    try {
        $userData = $this->getUserId();
        $userId = $userData['userId'];

        // Obtener todos los tokens asociados al usuario
        $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();

        $accounts = [];
        foreach ($tokens as $token) {
            // Obtener información de la cuenta desde la API
            $response = $this->client->get('users/me', [
                'headers' => [
                    'Authorization' => "Bearer {$this->mercadoLibreService->getAccessToken($userId, $token->ml_account_id)}"
                ]
            ]);

            // Decodificar la respuesta y almacenar los datos
            $accountInfo = json_decode($response->getBody(), true);
            $accounts[] = [
                'ml_account_id' => $token->ml_account_id,
                'account_info' => $accountInfo
            ];
        }

        return $accounts;
    } catch (RequestException $e) {
        \Log::error("Error al obtener información de las cuentas: " . $e->getMessage());
        throw $e;
    }
}


public function getOwnPublications($userId, $limit = 50, $offset = 0, $search = null, $status = 'active')
    {
        try {
            // Obtener los tokens de las cuentas vinculadas
            $userData = $this->getUserId();
            $userId = $userData['userId'];
            $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();

            $totalCuentas = $tokens->count();
            if ($totalCuentas === 0) {
                return ['items' => [], 'total' => 0];
            }

            $processedItems = [];
            $total = 0;

            // Repartir el `limit` entre las cuentas vinculadas
            $limitPorCuenta = (int) ceil($limit / $totalCuentas);
            $offsetPorCuenta = (int) ceil($offset / $totalCuentas);

            foreach ($tokens as $token) {
                $mlAccountId = $token->ml_account_id;

                $queryParams = [
                    'include_attributes' => 'all',
                    'limit' => $limitPorCuenta,
                    'offset' => $offsetPorCuenta,
                    'status' => $status ?? 'active'
                ];

                if ($search) {
                    $queryParams['q'] = $search;
                }

                $response = $this->client->get("users/{$mlAccountId}/items/search", [
                    'headers' => [
                        'Authorization' => "Bearer {$this->mercadoLibreService->getAccessToken($userId, $mlAccountId)}"
                    ],
                    'query' => $queryParams
                ]);
                $data = json_decode($response->getBody(), true);
                if (empty($data['results'])) continue;

                // Obtener detalles de los items en bloques de 20
                foreach (array_chunk($data['results'], 20) as $chunk) {
                    $detailsResponse = $this->client->get("items", [
                        'headers' => [
                            'Authorization' => "Bearer {$this->mercadoLibreService->getAccessToken($userId, $mlAccountId)}"
                        ],
                        'query' => ['ids' => implode(',', $chunk)]
                    ]);

                    $details = json_decode($detailsResponse->getBody(), true);
                    foreach ($details as $item) {
                        $body = $item['body'] ?? [];
                        $processedItems[] = [
                            'id' => $body['id'] ?? null,
                            'titulo' => $body['title'] ?? 'Sin título',
                            'imagen' => $body['thumbnail'] ?? null,
                            'stockActual' => $body['available_quantity'] ?? 0,
                            'precio' => $body['price'] ?? null,
                            'precio_original' => $body['original_price'] ?? null, // Precio original
                            'deal_ids' => $body['deal_ids'] ?? [], // IDs de promociones
                            'tags' => $body['tags'] ?? [], // Etiquetas que podrían indicar promoción
                            'estado' => $body['status'] ?? 'Desconocido',
                            'permalink' => $body['permalink'] ?? '#',
                            'condicion' => $body['condition'] ?? 'Desconocido',
                            'sku' => $body['user_product_id'] ?? null,
                            'tipoPublicacion' => $body['listing_type_id'] ?? null,
                            'enCatalogo' => $body['catalog_listing'] ?? null,
                            'categoryid' => $body['category_id'] ?? null,
                            'ml_account_id' => $body['seller_id'] ?? null,
                            'logistic_type' => $body['shipping']['logistic_type'] ?? '',
                            'inventory_id' => $body['inventory_id'] ?? '',
                            'user_product_id' => $body['user_product_id'] ?? '',
                        ];
                    }
                }
                $total += $data['paging']['total'] ?? 0;

                sleep(1); // Evitar rate limit de la API
            }

            // Ordenar los elementos para evitar duplicados
            $processedItems = array_slice($processedItems, 0, $limit);

            return [
                'items' => $processedItems,
                'total' => $total
            ];

        } catch (RequestException $e) {
            \Log::error("Error al obtener publicaciones propias: " . $e->getMessage());
            throw $e;
        }
    }





// DESCARGAR A LA BASE DE DATOS

public function DescargarArticulosDB($userId, $token, $limit = 50, $offset = 0)
{
    try {
        // Obtener solo el token de la cuenta específica
       // $token = \App\Models\MercadoLibreToken::where('ml_account_id', $userId)->first();

        if (!$token) {
            \Log::error("No se encontró token para la cuenta de MercadoLibre: {$userId}");
            return ['items' => [], 'total' => 0];
        }


        $allItems = [];
        $total = 0;
        $maxApiLimit = 1000; // Límite máximo de ítems paginables según la API

        $currentOffset = $offset;

        do {
            // Ajustar el límite si offset + limit supera el máximo permitido
            $limitAdjusted = min($limit, max(0, $maxApiLimit - $currentOffset));

            if ($limitAdjusted <= 0) {
                break; // No hay más ítems para paginar
            }

            // Llamada a la API de MercadoLibre
            $response = $this->client->get("users/{$userId}/items/search", [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'query' => [
                    'include_attributes' => 'all',
                    'limit' => $limitAdjusted,
                    'offset' => $currentOffset
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (empty($data['results'])) {
                break;
            }

            $itemIds = $data['results'];
            $chunks = array_chunk($itemIds, 20);

            foreach ($chunks as $chunk) {
                $detailsResponse = $this->client->get("items", [
                    'headers' => ['Authorization' => "Bearer {$token}"],
                    'query' => ['ids' => implode(',', $chunk)]
                ]);

                $details = json_decode($detailsResponse->getBody(), true);
                $allItems = array_merge($allItems, $details);
            }

            $total = $data['paging']['total'] ?? $total;
            $currentOffset += $limitAdjusted;

            sleep(1);
        } while ($currentOffset < $total && $currentOffset < $maxApiLimit);

        // Procesar artículos
        $processedItems = [];

        foreach ($allItems as $item) {
            $body = $item['body'] ?? [];
            $sellerId = $body['seller_id'] ?? null;

            // Calcular descuento si aplica
            $precio = $body['price'] ?? null;
            $precioOriginal = $body['original_price'] ?? null;
            $enPromocion = $precioOriginal && $precio && $precioOriginal > $precio;
            $descuentoPorcentaje = $enPromocion ? round((($precioOriginal - $precio) / $precioOriginal) * 100, 2) : null;

            $processedItems[] = [
                'ml_product_id' => $body['id'] ?? null,
                'titulo' => $body['title'] ?? 'Sin título',
                'imagen' => $body['thumbnail'] ?? null,
                'stockActual' => $body['available_quantity'] ?? 0,
                'precio' => $precio,
                'estado' => $body['status'] ?? 'Desconocido',
                'permalink' => $body['permalink'] ?? '#',
                'condicion' => $body['condition'] ?? 'Desconocido',
                'sku' => $body['user_product_id'] ?? null,
                'tipoPublicacion' => $body['listing_type_id'] ?? null,
                'enCatalogo' => $body['catalog_listing'] ?? null,
                'token_id' => $sellerId,
                'logistic_type' => $body['shipping']['logistic_type'] ?? null,
                'inventory_id' => $body['inventory_id'] ?? null,
                'user_product_id' => $body['user_product_id'] ?? null,
                'precio_original' => $precioOriginal,
                'category_id' => $body['category_id'] ?? null,
                'en_promocion' => $enPromocion,
                'descuento_porcentaje' => $descuentoPorcentaje,
                'deal_ids' => json_encode($body['deal_ids'] ?? []),
            ];
        }

        \Log::debug("Productos procesados para la cuenta {$userId}:", $processedItems);

        return ['items' => $processedItems, 'total' => $total];

    } catch (RequestException $e) {
        \Log::error("Error al obtener publicaciones de MercadoLibre ({$userId}): " . $e->getMessage());
        throw $e;
    }
}


/**
 * SINCRONIZA LA BASE DE DATOS
 */
public function sincronizarBaseDeDatos(string $userId, int $limit = 50, int $page)
{
    try {
        $lastSync = new \Carbon\Carbon(\App\Models\SyncTimestamp::latest()->first()->timestamp ?? now());
        $userId = auth()->user()->id;
        $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();
        $offset = ($page - 1) * $limit;

        // Límite máximo de ítems paginables según la API (1000)
        $maxApiLimit = 1000;

        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id;
            $currentOffset = $offset;
            \Log::info("Procesando cuenta ML", ['mlAccountId' => $mlAccountId]);

            do {
                // Asegurarse de que offset + limit no exceda el máximo permitido
                if ($currentOffset + $limit > $maxApiLimit) {
                    $limitAdjusted = $maxApiLimit - $currentOffset;
                    if ($limitAdjusted <= 0) {
                        break; // No hay más ítems que paginar dentro del límite
                    }
                } else {
                    $limitAdjusted = $limit;
                }

                $response = Http::withToken($this->mercadoLibreService->getAccessToken($userId, $mlAccountId))
                    ->get("https://api.mercadolibre.com/users/{$mlAccountId}/items/search", [
                        'limit' => $limitAdjusted,
                        'offset' => $currentOffset,
                        'sort' => 'last_updated_desc',
                    ]);

                if ($response->failed()) {
                    throw new \Exception("Error al obtener datos para la cuenta {$mlAccountId}: " . $response->body());
                }

                $data = $response->json();
                $itemIds = $data['results'] ?? [];
                if (empty($itemIds)) {
                    break;
                }

                $chunks = array_chunk($itemIds, 20);
                $anyUpdated = false;

                foreach ($chunks as $chunk) {
                    $detailsResponse = Http::withToken($this->mercadoLibreService->getAccessToken($userId, $mlAccountId))
                        ->get("https://api.mercadolibre.com/items", [
                            'ids' => implode(',', $chunk),
                        ]);

                    if ($detailsResponse->failed()) {
                        throw new \Exception("Error al obtener detalles de artículos: " . $detailsResponse->body());
                    }

                    $details = $detailsResponse->json();

                    foreach ($details as $item) {
                        $body = $item['body'] ?? [];
                        $itemLastUpdated = isset($body['last_updated'])
                            ? new \Carbon\Carbon($body['last_updated'])
                            : null;

                        if ($itemLastUpdated === null || $itemLastUpdated->lessThan($lastSync)) {
                            \Log::info("Artículo no actualizado después de la última sincronización", [
                                'itemLastUpdated' => $itemLastUpdated ? $itemLastUpdated->toDateTimeString() : 'N/A',
                                'lastSync' => $lastSync->toDateTimeString(),
                            ]);
                            continue;
                        }

                        $precio = $body['price'] ?? null;
                        $precioOriginal = $body['original_price'] ?? null;
                        $enPromocion = $precioOriginal && $precio && $precioOriginal > $precio;
                        $descuentoPorcentaje = $enPromocion ? round((($precioOriginal - $precio) / $precioOriginal) * 100, 2) : null;

                        Articulo::updateOrCreate(
                            ['ml_product_id' => $body['id']],
                            [
                                'user_id' => $userId,
                                'titulo' => $body['title'] ?? 'Sin título',
                                'imagen' => $body['thumbnail'] ?? null,
                                'stock_actual' => $body['available_quantity'] ?? 0,
                                'precio' => $precio,
                                'estado' => $body['status'] ?? 'Desconocido',
                                'permalink' => $body['permalink'] ?? '#',
                                'condicion' => $body['condition'] ?? 'Desconocido',
                                'tipo_publicacion' => $body['listing_type_id'] ?? 'Desconocido',
                                'en_catalogo' => $body['catalog_listing'] ?? false,
                                'logistic_type' => $body['shipping']['logistic_type'] ?? null,
                                'inventory_id' => $body['inventory_id'] ?? null,
                                'user_product_id' => $body['user_product_id'] ?? null,
                                'precio_original' => $precioOriginal,
                                'category_id' => $body['category_id'] ?? null,
                                'en_promocion' => $enPromocion,
                                'descuento_porcentaje' => $descuentoPorcentaje,
                                'deal_ids' => json_encode($body['deal_ids'] ?? []),
                                'updated_at' => now(),
                            ]
                        );

                        $anyUpdated = true;
                    }

                    if (!$anyUpdated) {
                        break;
                    }
                }

                $currentOffset += $limitAdjusted;
                sleep(1);
            } while ($currentOffset < ($data['paging']['total'] ?? 0) && $currentOffset < $maxApiLimit);

            $syncTimestamp = \App\Models\SyncTimestamp::latest()->first();
            if ($syncTimestamp) {
                $syncTimestamp->update(['timestamp' => now()]);
            } else {
                \App\Models\SyncTimestamp::create(['timestamp' => now()]);
            }

            \Log::info("Cuenta procesada: {$mlAccountId}, artículos actualizados.");
        }

    } catch (\Exception $e) {
        \Log::error("Error al sincronizar la base de datos: " . $e->getMessage());
        throw $e;
    }
}







public function getItemsByCategory($categoryId, $limit = 50, $offset = 0)
{
    try {
        $userData = $this->getUserId();
        $userId = $userData['userId'];
        $mlAccountId = $userData['mlAccountId'];
        $region = 'MLA'; // Región para Argentina
        $response = $this->client->get("sites/{$region}/search", [
            'headers' => [
                'Authorization' => "Bearer {$this->mercadoLibreService->getAccessToken($userId, $mlAccountId)}"
            ],
            'query' => [
                'category' => $categoryId,
                'limit' => $limit,
                'offset' => $offset,
                'sort' => 'sold_quantity_desc'
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (!isset($data['results']) || empty($data['results'])) {
            return [
                'items' => [],
                'total' => $data['paging']['total'] ?? 0
            ];
        }

        return [
            'items' => $data['results'],
            'total' => $data['paging']['total'] ?? count($data['results'])
        ];
    } catch (RequestException $e) {
        \Log::error("Error al obtener items por categoría: " . $e->getMessage());
        throw $e;
    }
}


 }
