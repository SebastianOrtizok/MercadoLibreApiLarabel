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


public function getOwnPublications($userId, $limit = 50, $offset = 0, $search = null, $status = 'active', $mlaId = null)
{
    try {
        // Obtener los tokens de las cuentas vinculadas
        $userData = $this->getUserId();
        $userId = $userData['userId'];
        $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();

        $totalCuentas = $tokens->count();
        if ($totalCuentas === 0) {
            \Log::info("No se encontraron cuentas vinculadas para el usuario {$userId}");
            return ['items' => [], 'total' => 0];
        }

        $processedItems = [];
        $total = 0;

        // Log para verificar si se proporciona mla_id
        \Log::info("getOwnPublications llamado con mla_id: " . ($mlaId ?? 'No proporcionado'));

        // Si se proporciona un mla_id, buscar solo ese ítem
        if ($mlaId) {
            // Normalizar el mla_id a mayúsculas para consistencia
            $mlaId = strtoupper($mlaId);
            \Log::info("Buscando ítem específico con ID normalizado: {$mlaId}");

            foreach ($tokens as $token) {
                $mlAccountId = $token->ml_account_id;
                $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);

                try {
                    $response = $this->client->get("items/{$mlaId}", [
                        'headers' => [
                            'Authorization' => "Bearer {$accessToken}"
                        ],
                        'query' => [
                            'include_attributes' => 'all',
                        ]
                    ]);

                    $item = json_decode($response->getBody(), true);

                    // Verificar que el ítem existe y pertenece al usuario autenticado
                    if ($response->getStatusCode() === 200 && !empty($item) && $item['seller_id'] == $mlAccountId) {
                        \Log::info("Ítem encontrado: {$mlaId} pertenece a ml_account_id: {$mlAccountId}");
                        $processedItems[] = [
                            'id' => $item['id'] ?? null,
                            'titulo' => $item['title'] ?? 'Sin título',
                            'imagen' => $item['thumbnail'] ?? null,
                            'stockActual' => $item['available_quantity'] ?? 0,
                            'precio' => $item['price'] ?? null,
                            'precio_original' => $item['original_price'] ?? null,
                            'deal_ids' => $item['deal_ids'] ?? [],
                            'tags' => $item['tags'] ?? [],
                            'estado' => $item['status'] ?? 'Desconocido',
                            'permalink' => $item['permalink'] ?? '#',
                            'condicion' => $item['condition'] ?? 'Desconocido',
                            'sku' => $item['user_product_id'] ?? null,
                            'tipoPublicacion' => $item['listing_type_id'] ?? null,
                            'enCatalogo' => $item['catalog_listing'] ?? null,
                            'categoryid' => $item['category_id'] ?? null,
                            'ml_account_id' => $item['seller_id'] ?? null,
                            'logistic_type' => $item['shipping']['logistic_type'] ?? '',
                            'inventory_id' => $item['inventory_id'] ?? '',
                            'user_product_id' => $item['user_product_id'] ?? '',
                        ];
                        $total = 1;
                        \Log::info("Devolviendo solo el ítem {$mlaId}");
                        return [
                            'items' => $processedItems,
                            'total' => $total
                        ];
                    }
                } catch (RequestException $e) {
                    // Capturar errores específicos de la API (como 400 Bad Request)
                    \Log::warning("Error al buscar ítem {$mlaId}: " . $e->getMessage());
                    continue; // Continuar con la siguiente cuenta si falla
                }
            }

            // Si no se encontró el ítem en ninguna cuenta
            \Log::warning("No se encontró el ítem con ID {$mlaId} para el usuario {$userId}");
            return [
                'items' => [],
                'total' => 0
            ];
        }

        // Si no hay mla_id, proceder con la búsqueda normal
        \Log::info("Realizando búsqueda general para usuario {$userId}");
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
            if (empty($data['results'])) {
                \Log::info("No se encontraron resultados para ml_account_id: {$mlAccountId}");
                continue;
            }

            // Obtener detalles de los ítems en bloques de 20
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
                        'precio_original' => $body['original_price'] ?? null,
                        'deal_ids' => $body['deal_ids'] ?? [],
                        'tags' => $body['tags'] ?? [],
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

        \Log::info("Devolviendo búsqueda general con {$total} ítems totales, limitados a {$limit}");
        return [
            'items' => $processedItems,
            'total' => $total
        ];

    } catch (RequestException $e) {
        \Log::error("Error al obtener publicaciones propias: " . $e->getMessage());
        throw $e;
    }
}





    public function DescargarArticulosDB($userId, $token, $limit = 50, $offset = 0)
    {
        try {
            if (!$token) {
                \Log::error("No se encontró token para la cuenta de MercadoLibre: {$userId}");
                return ['items' => [], 'total' => 0];
            }

            $allItems = [];
            $total = 0;
            $currentOffset = $offset;
            $maxOffset = 950; // Límite seguro para offset (950 + 50 = 1000)

            do {
                if ($currentOffset > $maxOffset) {
                    \Log::info("Alcanzado el límite máximo de offset ($maxOffset) para {$userId}, deteniendo sincronización.");
                    break;
                }

                \Log::info("Descargando ítems para {$userId}", ['offset' => $currentOffset, 'limit' => $limit]);
                $response = $this->client->get("users/{$userId}/items/search", [
                    'headers' => ['Authorization' => "Bearer {$token}"],
                    'query' => [
                        'include_attributes' => 'all',
                        'limit' => $limit,
                        'offset' => $currentOffset
                    ]
                ]);

                $data = json_decode($response->getBody(), true);
                if (empty($data['results'])) {
                    \Log::info("No más resultados en offset {$currentOffset} para {$userId}");
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
                $currentOffset += $limit;
                sleep(1); // Evitar rate limiting
            } while ($currentOffset <= $maxOffset && $currentOffset < $total); // Condición ajustada

            \Log::info("Descargados ítems para {$userId}", [
                'items_count' => count($allItems),
                'total_reported_by_api' => $total,
                'stopped_at_offset' => $currentOffset
            ]);

            // Procesamiento de ítems (sin sku_interno)...
            $processedItems = [];
            foreach ($allItems as $item) {
                $body = $item['body'] ?? [];
                $sellerId = $body['seller_id'] ?? null;
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

            return ['items' => $processedItems, 'total' => $total];
        } catch (RequestException $e) {
            \Log::error("Error al obtener publicaciones de MercadoLibre ({$userId}): " . $e->getMessage());
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
