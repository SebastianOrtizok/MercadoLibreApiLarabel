<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


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
        foreach ($allItems as $item) {
            $body = $item['body'] ?? []; // Asegúrate de acceder al 'body'
            $processedItems[] = [
                'id' => $body['id'] ?? null,
                'titulo' => $body['title'] ?? 'Sin título',
                'imagen' => $body['pictures'][0]['url'] ?? null,
                'stockActual' => $body['available_quantity'] ?? 0,
                'precio' => $body['price'] ?? null,
                'estado' => $body['status'] ?? 'Desconocido',
                'permalink' => $body['permalink'] ?? '#',
                'condicion' => $body['condition'] ?? 'Desconocido',
                'ultimaVenta' => $this->getLastSale($body['id'] ?? null), // Asegúrate de implementar este método
            ];
        }

        return [
            'items' => $processedItems,
            'total' => $total
        ];

    } catch (RequestException $e) {
        \Log::error("Error al obtener publicaciones propias: " . $e->getMessage());
        throw $e;
    }
}

public function getLastSale($itemId)
{
    try {
        // Verificar que $itemId no sea nulo
        if (!$itemId) {
            return "ID de artículo no válido";
        }

        // Obtener el token del usuario (puedes ajustar esto según cómo gestionas los tokens)
        $userData = $this->getUserId();
        $userId = $userData['userId'];
        $tokens = \App\Models\MercadoLibreToken::where('user_id', $userId)->first();

        if (!$tokens) {
            return "No se encontró token asociado";
        }

        $mlAccountId = $tokens->ml_account_id;

        // Obtener las órdenes relacionadas al artículo
        $response = $this->client->get("orders/search", [
            'headers' => [
                'Authorization' => "Bearer {$this->mercadoLibreService->getAccessToken($userId, $mlAccountId)}"
            ],
            'query' => [
                'item_id' => $itemId,
                'sort' => 'date_desc', // Ordenar por fecha descendente para obtener la última venta
                'limit' => 1,         // Solo necesitamos la última venta
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        // Verificar si hay resultados
        if (!isset($data['results']) || empty($data['results'])) {
            return "Sin ventas registradas";
        }

        // Extraer la fecha de la última venta
        $lastSale = $data['results'][0];
        $dateCreated = $lastSale['date_created'] ?? "Fecha no disponible";

        return $dateCreated; // Retorna la fecha de la última venta

    } catch (RequestException $e) {
        \Log::error("Error al obtener la última venta del artículo $itemId: " . $e->getMessage());
        return "Error al obtener la última venta";
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
