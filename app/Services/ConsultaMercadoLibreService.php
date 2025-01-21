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

            // Muestra el ml_account_id (esto es para depuración, puedes eliminarlo luego)
            //dd("User ID: " . $userId . " | ML Account ID: " . $mlAccountId);

            return [
                'userId' => $userId,
                'mlAccountId' => $mercadoLibreToken->ml_account_id
            ];
        }



    public function getInventory($sellerId, $limit = 10)
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
                    'category' => 'MLA1234',
                    'limit' => $limit
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            \Log::error("Error al obtener el inventario: " . $e->getMessage());
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


public function getOwnPublications($userId, $limit = 50, $offset = 0)
{
    try {
        // Obtener todos los tokens asociados al usuario
        $userData = $this->getUserId();
        $userId = $userData['userId'];
        $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();

        $allItems = [];  // Array para almacenar todas las publicaciones
        $total = 0;  // Total de publicaciones

        // Iterar sobre los tokens (que corresponden a las cuentas de MercadoLibre)
        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id;

            // Obtener las publicaciones de la cuenta actual
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
                continue;  // Si no hay publicaciones para esta cuenta, pasamos a la siguiente
            }

            $itemIds = $data['results'];

            // Dividir los IDs en bloques de 20
            $chunks = array_chunk($itemIds, 20);

            foreach ($chunks as $chunk) {
                // Obtener los detalles de cada bloque de 20 elementos
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

            $total += $data['paging']['total'] ?? count($allItems);

            // Esperar un tiempo antes de hacer la siguiente petición
            sleep(1); // Espera de 1 segundo entre peticiones para evitar sobrecargar la API
        }

        // Procesar los datos de stock y última venta
        $processedItems = [];
        $allItemIds = array_map(function($item) {
            return $item['body']['id'] ?? null;
        }, $allItems);

        // Obtener las últimas ventas de todos los items
       // $lastSaleDates = $this->getLastSale($allItemIds);

        foreach ($allItems as $item) {
            $body = $item['body'] ?? []; // Asegúrate de acceder al 'body'
            //dd($body);
            $processedItems[] = [
                'id' => $body['id'] ?? null,
                'titulo' => $body['title'] ?? 'Sin título',
                'imagen' => $body['pictures'][0]['url'] ?? null,
                'stockActual' => $body['available_quantity'] ?? 0,
                'precio' => $body['price'] ?? null,
                'estado' => $body['status'] ?? 'Desconocido',
                'permalink' => $body['permalink'] ?? '#',
                'condicion' => $body['condition'] ?? 'Desconocido',
                'sku' => $body['user_product_id'] ?? null, // SKU del producto
                'tipoPublicacion' => $body['listing_type_id'] ?? null, // Tipo de publicación
                'enCatalogo' => $body['catalog_listing'] ?? null, // Si está en catálogo
                'categoryid' => $body['category_id']
            ];

        }
//dd($processedItems);
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
                'imagen' => $body['pictures'][0]['url'] ?? null,
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
        $userData = $this->getUserId();
        $userId = $userData['userId'];
        $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();

        $offset = ($page - 1) * $limit;
        $allUpdatedItems = [];

        foreach ($tokens as $token) {
            $mlAccountId = $token->ml_account_id;
            $offset = 0; // Reiniciar el offset al comenzar con cada cuenta


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
                                    'titulo' => $body['title'] ?? 'Sin título',
                                    'imagen' => $body['pictures'][0]['url'] ?? null,
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

                            // Log de artículo actualizado
                            \Log::info("Producto actualizado: ", [
                                'ml_product_id' => $body['id'],
                                'titulo' => $body['title'],
                                'stock_actual' => $body['available_quantity'],
                                'precio' => $body['price'],
                                'estado' => $body['status'],
                                'updated_at' => now(),
                                'fecha de mercadolibre' => $itemLastUpdated,
                            ]);

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

        \Log::debug("Artículos actualizados:", $allUpdatedItems);

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
                'offset' => $offset
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





public function getPublicationStats(array $itemIds)
{
    try {
        $userData = $this->getUserId();
        $userId = $userData['userId'];
        $mlAccountId = $userData['mlAccountId'];
        $response = $this->client->get("items", [
            'headers' => [
                'Authorization' => "Bearer {$this->mercadoLibreService->getAccessToken($userId, $mlAccountId)}"
            ],
            'query' => [
                'ids' => implode(',', $itemIds),
                'attributes' => 'id,visits,sold_quantity'
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        return collect($data)->map(function ($item) {
            return [
                'id' => $item['id'],
                'visits' => $item['visits'] ?? 0,
                'sold_quantity' => $item['sold_quantity'] ?? 0,
                'conversion_rate' => $item['visits'] > 0 ? ($item['sold_quantity'] / $item['visits']) * 100 : 0,
            ];
        })->toArray();
    } catch (RequestException $e) {
        \Log::error("Error al obtener estadísticas de publicaciones: " . $e->getMessage());
        throw $e;
    }
}


public function getProductVisits($itemId)
{
    try {
        $userData = $this->getUserId();
        $userId = $userData['userId'];
        $mlAccountId = $userData['mlAccountId'];
        \Log::info("Consultando visitas para Item ID: $itemId");

        $response = Http::get("https://api.mercadolibre.com/items/$itemId/visits");
      // Log de la respuesta para análisis
//dd($response);

        if ($response->successful()) {
            // Retorna la respuesta JSON si la solicitud es exitosa
            return $response->json();
        } else {
            // Log de error en caso de fallo en la respuesta
          return null; // Retorna null si la respuesta no es exitosa
        }
    } catch (\Exception $e) {
        \Log::error("Error al realizar la solicitud para el Item ID $itemId: " . $e->getMessage());
        return 0; // Retorna 0 en caso de error en la solicitud
    }
}


}
