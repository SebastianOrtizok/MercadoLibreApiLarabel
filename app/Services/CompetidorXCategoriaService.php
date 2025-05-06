<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CompetidorXCategoriaService
{
    protected $client;
    protected $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->client = new Client(['base_uri' => 'https://api.mercadolibre.com/']);
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function getItemsByCategory($userId, $mlAccountId, $categoryId = 'MLA1051', $limit = 50, $offset = 0)
    {
        try {
            \Log::info("Iniciando getItemsByCategory", [
                'user_id' => $userId,
                'ml_account_id' => $mlAccountId,
                'category_id' => $categoryId,
                'limit' => $limit,
                'offset' => $offset
            ]);
            $region = 'MLA';
            $accessToken = $this->mercadoLibreService->getAccessToken($userId, $mlAccountId);
            \Log::info("Access token obtenido", ['access_token' => substr($accessToken, 0, 20) . '...']);

            $response = $this->client->get("sites/{$region}/search", [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
                'query' => [
                    'category' => $categoryId,
                    'limit' => $limit,
                    'offset' => $offset,
                    'sort' => 'sold_quantity_desc'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            \Log::info("Respuesta de la API", [
                'status' => $response->getStatusCode(),
                'results_count' => count($data['results'] ?? []),
                'total' => $data['paging']['total'] ?? 0,
                'endpoint' => "https://api.mercadolibre.com/sites/{$region}/search?category={$categoryId}&limit={$limit}&offset={$offset}&sort=sold_quantity_desc"
            ]);

            if (!isset($data['results']) || empty($data['results'])) {
                \Log::info("No se encontraron ítems para la categoría", ['category_id' => $categoryId]);
                return [
                    'items' => [],
                    'total' => $data['paging']['total'] ?? 0,
                    'stats' => $this->getStats([]),
                ];
            }

            $items = [];
            foreach ($data['results'] as $item) {
                $sellerResponse = $this->client->get("users/{$item['seller']['id']}", [
                    'headers' => ['Authorization' => "Bearer {$accessToken}"],
                ]);
                $sellerData = json_decode($sellerResponse->getBody(), true);
                $sellerNickname = $sellerData['nickname'] ?? 'Desconocido';

                $items[] = [
                    'item_id' => $item['id'],
                    'titulo' => $item['title'],
                    'precio' => $item['price'],
                    'seller_id' => $item['seller']['id'],
                    'seller' => $sellerNickname,
                    'cantidad_vendida' => $item['sold_quantity'] ?? 0,
                    'cantidad_disponible' => $item['available_quantity'] ?? 0,
                    'publication_type' => $item['listing_type_id'] ?? 'unknown',
                    'buying_mode' => $item['buying_mode'] ?? 'buy_it_now',
                    'envio_gratis' => isset($item['shipping']['free_shipping']) && $item['shipping']['free_shipping'],
                    'following' => false,
                ];
            }

            $stats = $this->getStats($items);

            \Log::info("Ítems obtenidos", ['items_count' => count($items)]);

            return [
                'items' => $items,
                'total' => $data['paging']['total'] ?? count($items),
                'stats' => $stats,
            ];
        } catch (RequestException $e) {
            $errorMessage = "Error al obtener ítems por categoría: " . $e->getMessage();
            if ($e->hasResponse()) {
                $errorMessage .= " - Respuesta de la API: " . $e->getResponse()->getBody()->getContents();
            }
            \Log::error($errorMessage);
            throw new \Exception($errorMessage);
        }
    }

    public function getCategories()
    {
        try {
            \Log::info("Iniciando getCategories");
            $response = $this->client->get('sites/MLA/categories');
            $categories = json_decode($response->getBody(), true);
            \Log::info("Categorías obtenidas desde la API", ['count' => count($categories)]);
            return $categories;
        } catch (RequestException $e) {
            $errorMessage = "Error al obtener categorías: " . $e->getMessage();
            if ($e->hasResponse()) {
                $errorMessage .= " - Respuesta de la API: " . $e->getResponse()->getBody()->getContents();
            }
            \Log::error($errorMessage);
            throw new \Exception($errorMessage);
        }
    }

    protected function getStats($items)
    {
        $sales = 0;
        $revenue = 0;
        $sellers = [];

        foreach ($items as $item) {
            $sales += $item['cantidad_vendida'];
            $revenue += ($item['precio'] * $item['cantidad_vendida']);

            if (isset($sellers[$item['seller_id']])) {
                $sellers[$item['seller_id']]['sales'] += $item['cantidad_vendida'];
                $sellers[$item['seller_id']]['revenue'] += ($item['precio'] * $item['cantidad_vendida']);
            } else {
                $sellers[$item['seller_id']] = [
                    'sales' => $item['cantidad_vendida'],
                    'revenue' => ($item['precio'] * $item['cantidad_vendida']),
                    'nickname' => $item['seller'],
                ];
            }
        }

        return [
            'total_sales' => $sales,
            'total_revenue' => $revenue,
            'sellers' => $sellers,
        ];
    }
}
