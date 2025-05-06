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

    public function getItemsByCategory($userId, $mlAccountId, $categoryId, $limit = 50, $offset = 0)
    {
        try {
            \Log::info("Iniciando getItemsByCategory", ['user_id' => $userId, 'ml_account_id' => $mlAccountId, 'category_id' => $categoryId]);
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
                'total' => $data['paging']['total'] ?? 0
            ]);

            if (!isset($data['results']) || empty($data['results'])) {
                \Log::info("No se encontraron ítems para la categoría", ['category_id' => $categoryId]);
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
}
