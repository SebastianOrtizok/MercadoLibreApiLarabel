<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Articulo;
use App\Models\SyncTimestamp;
use Carbon\Carbon;

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


public function getOwnPublications($userId, $limit = 50, $offset = 0, $search = null, $status)
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
                //dd($details);
                foreach ($details as $item) {
                    $body = $item['body'] ?? [];

                    $processedItems[] = [
                        'id' => $body['id'] ?? null,
                        'titulo' => $body['title'] ?? 'Sin título',
                        'imagen' => $body['thumbnail'] ?? null,
                        'stockActual' => $body['available_quantity'] ?? 0,
                        'precio' => $body['price'] ?? null,
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

public function DescargarArticulosDB($userId, $limit = 50, $offset = 0)
{
    try {
        // Obtener todos los tokens asociados al usuario
        $userData = $this->getUserId();
        $userId = $userData['userId'];
        $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();

        $allItems = [];
        $total = 0;

        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id;
            $offset = 0; //
            do {
                // Obtener las publicaciones de la cuenta actual con paginación
                $response = $this->client->get("users/{$mlAccountId}/items/search", [
                    'headers' => [
                        'Authorization' => "Bearer {$this->mercadoLibreService->getAccessToken($userId, $mlAccountId)}"
                    ],
                    'query' => [
                        'include_attributes' => 'all',
                        'limit' => $limit,
                        'offset' => $offset
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if (!isset($data['results']) || empty($data['results'])) {
                    break; // Si no hay más resultados, terminamos el bucle
                }

                $itemIds = $data['results'];
                $chunks = array_chunk($itemIds, 20);

                foreach ($chunks as $chunk) {
                    $detailsResponse = $this->client->get("items", [
                        'headers' => [
                            'Authorization' => "Bearer {$this->mercadoLibreService->getAccessToken($userId, $mlAccountId)}"
                        ],
                        'query' => [
                            'ids' => implode(',', $chunk)
                        ]
                    ]);

                    $details = json_decode($detailsResponse->getBody(), true);
                    $allItems = array_merge($allItems, $details);
                }

                $total = $data['paging']['total'] ?? $total;
                $offset += $limit; // Incrementar el offset para obtener la siguiente página

                // Esperar antes de realizar la siguiente solicitud
                sleep(1);

            } while ($offset < $total); // Continuar hasta que hayamos obtenido todos los resultados
        }

        $processedItems = [];

        foreach ($allItems as $item) {
            $body = $item['body'] ?? [];
            $sellerId = $body['seller_id'] ?? null;

            $processedItems[] = [
                'ml_product_id' => $body['id'] ?? null,
                'titulo' => $body['title'] ?? 'Sin título',
                'imagen' => $body['thumbnail'] ?? null,
            //  'imagen' => $body['pictures'][0]['url'] ?? null,
                'stockActual' => $body['available_quantity'] ?? 0,
                'precio' => $body['price'] ?? null,
                'estado' => $body['status'] ?? 'Desconocido',
                'permalink' => $body['permalink'] ?? '#',
                'condicion' => $body['condition'] ?? 'Desconocido',
                'sku' => $body['user_product_id'] ?? null,
                'tipoPublicacion' => $body['listing_type_id'] ?? null,
                'enCatalogo' => $body['catalog_listing'] ?? null,
                'token_id' => $sellerId,
            ];
        }

        \Log::debug("Productos procesados:", $processedItems);

        return [
            'items' => $processedItems,
            'total' => $total
        ];

    } catch (RequestException $e) {
        \Log::error("Error al obtener publicaciones propias: " . $e->getMessage());
        throw $e;
    }
}

/**
 * SINCRONIZA LA BASE DE DATOS
 */
public function sincronizarBaseDeDatos(string $userId, int $limit, int $page)
{
    try {
        // Obtener el último timestamp de sincronización y convertirlo en Carbon
        $lastSync = new \Carbon\Carbon(\App\Models\SyncTimestamp::latest()->first()->timestamp ?? now());

        // Obtener todos los tokens asociados al usuario
        //$userData = $this->getUserId();
        //$userId = $userData['userId'];
        $userId = auth()->user()->id;
        $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();
        $offset = ($page - 1) * $limit;
        $allUpdatedItems = [];

        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id;
            $offset = 0; // Reiniciar el offset al comenzar con cada cuenta
            \Log::info("Procesando cuenta ML", ['mlAccountId' => $mlAccountId]);


            // Obtener los IDs de los artículos ordenados por `last_updated` de manera descendente
            do {
                $response = Http::withToken($this->mercadoLibreService->getAccessToken($userId, $mlAccountId))
                    ->get("https://api.mercadolibre.com/users/{$mlAccountId}/items/search", [
                        'limit' => 10,
                        'offset' => $offset,
                        'sort' => 'last_updated_desc',  // Ordenar por last_updated descendente
                    ]);

                if ($response->failed()) {
                    throw new \Exception("Error al obtener datos para la cuenta {$mlAccountId}.");
                }

                $data = $response->json();
                $itemIds = $data['results'] ?? [];
                if (empty($itemIds)) {
                    break; // No hay más resultados
                }

                // Dividir los IDs en grupos de 20 para obtener detalles masivos
                $chunks = array_chunk($itemIds, 20);

                foreach ($chunks as $chunk) {
                    $detailsResponse = Http::withToken($this->mercadoLibreService->getAccessToken($userId, $mlAccountId))
                        ->get("https://api.mercadolibre.com/items", [
                            'ids' => implode(',', $chunk),
                        ]);

                    if ($detailsResponse->failed()) {
                        throw new \Exception("Error al obtener detalles de artículos.");
                    }

                    $details = $detailsResponse->json();

                    // Indicador para saber si al menos un artículo fue actualizado
                    $anyUpdated = false;

                    foreach ($details as $item) {
                        $body = $item['body'] ?? [];

                        // Usar 'last_updated' para obtener la fecha de última actualización
                        $itemLastUpdated = isset($body['last_updated'])
                            ? new \Carbon\Carbon($body['last_updated'])
                            : null;

                        // Si no existe 'last_updated', continuamos con el siguiente artículo
                        if ($itemLastUpdated === null) {
                            continue;
                        }

                        // Comparar la fecha de la última actualización con el timestamp de sincronización
                        if ($itemLastUpdated->lessThan($lastSync)) {
                            \Log::info("Artículo no actualizado después de la última sincronización", [
                                'itemLastUpdated' => $itemLastUpdated->toDateTimeString(),
                                'lastSync' => $lastSync->toDateTimeString(),
                            ]);
                            // Si el artículo no ha sido actualizado después de la última sincronización, pasar al siguiente
                            continue;
                        }

                        // Si el artículo tiene una fecha de actualización más reciente que la sincronización
                        // Solo actualizar o crear el artículo en la base de datos en este caso
                        else {
                            Articulo::updateOrCreate(
                                ['ml_product_id' => $body['id']],
                                [
                                    'user_id' => $userId,
                                    'titulo' => $body['title'] ?? 'Sin título',
                                    'imagen' => $body['thumbnail'] ?? null,
                                    'stock_actual' => $body['available_quantity'] ?? 0,
                                    'precio' => $body['price'] ?? 0.0,
                                    'estado' => $body['status'] ?? 'Desconocido',
                                    'permalink' => $body['permalink'] ?? '#',
                                    'condicion' => $body['condition'] ?? 'Desconocido',
                                    'tipo_publicacion' => $body['listing_type_id'] ?? 'Desconocido',
                                    'en_catalogo' => $body['catalog_listing'] ?? false,
                                    'token_id' => $mlAccountId,
                                    'updated_at' => now(),
                                ]
                            );

                           // Indicamos que al menos un artículo fue actualizado
                            $anyUpdated = true;
                        }
                    }

                    // Si no se actualizó ningún artículo, rompemos el bucle para pasar a la siguiente cuenta
                    if (!$anyUpdated) {
                        break;
                    }
                }

                $offset += $limit;

                // Esperar antes de realizar la siguiente solicitud
                sleep(1);
            } while ($offset < ($data['paging']['total'] ?? 0));

            // Solo actualizamos el SyncTimestamp si fue modificada la base de datos
            $syncTimestamp = \App\Models\SyncTimestamp::latest()->first();
            if ($syncTimestamp) {
                $syncTimestamp->update(['timestamp' => now()]);
            } else {
                \App\Models\SyncTimestamp::create(['timestamp' => now()]);
            }
        }
        \Log::info("Cuenta procesada: {$mlAccountId}, artículos actualizados: ", $allUpdatedItems);


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
