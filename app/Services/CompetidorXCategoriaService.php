<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CompetidorXCategoriaService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.mercadolibre.com/']);
    }

    public function analyzeCompetitors($userId, $mlAccountId, $categoryId, $limit = 50)
    {
        try {
            $region = 'MLA';
            $response = $this->client->get("sites/{$region}/search", [
                'headers' => [
                    'Authorization' => "Bearer {$this->getAccessToken($userId, $mlAccountId)}"
                ],
                'query' => [
                    'category' => $categoryId,
                    'limit' => $limit,
                    'sort' => 'sold_quantity_desc'
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['results']) || empty($data['results'])) {
                return [
                    'competitors' => [],
                    'total_items' => 0,
                    'category_id' => $categoryId,
                    'top_keywords' => []
                ];
            }

            $competitors = [];
            $totalItems = count($data['results']);

            foreach ($data['results'] as $item) {
                $sellerId = $item['seller']['id'] ?? 'unknown';
                if (!isset($competitors[$sellerId])) {
                    $competitors[$sellerId] = [
                        'seller_id' => $sellerId,
                        'item_count' => 0,
                        'average_price' => 0,
                        'total_price' => 0,
                        'free_shipping_percentage' => 0,
                        'free_shipping_count' => 0,
                        'titles' => []
                    ];
                }

                $competitors[$sellerId]['item_count']++;
                $competitors[$sellerId]['total_price'] += $item['price'] ?? 0;
                $competitors[$sellerId]['free_shipping_count'] += ($item['shipping']['free_shipping'] ?? false) ? 1 : 0;
                $competitors[$sellerId]['titles'][] = $item['title'] ?? '';

                $competitors[$sellerId]['average_price'] = $competitors[$sellerId]['total_price'] / $competitors[$sellerId]['item_count'];
                $competitors[$sellerId]['free_shipping_percentage'] = ($competitors[$sellerId]['free_shipping_count'] / $competitors[$sellerId]['item_count']) * 100;
            }

            $allTitles = array_merge(...array_column($competitors, 'titles'));
            $wordCounts = [];
            foreach ($allTitles as $title) {
                $words = array_filter(explode(' ', strtolower($title)));
                foreach ($words as $word) {
                    if (strlen($word) > 3) {
                        $wordCounts[$word] = ($wordCounts[$word] ?? 0) + 1;
                    }
                }
            }
            arsort($wordCounts);
            $topKeywords = array_slice($wordCounts, 0, 10);

            return [
                'competitors' => array_values($competitors),
                'total_items' => $totalItems,
                'category_id' => $categoryId,
                'top_keywords' => $topKeywords
            ];
        } catch (RequestException $e) {
            \Log::error("Error al analizar competidores por categoría: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCategories()
    {
        try {
            $response = $this->client->get('sites/MLA/categories');
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            \Log::error("Error al obtener categorías: " . $e->getMessage());
            throw $e;
        }
    }

    protected function getAccessToken($userId, $mlAccountId)
    {
        // Ajusta con tu lógica para obtener el token desde mercadolibre_tokens
        $user = \App\Models\User::find($userId);
        if (!$user) {
            throw new \Exception('Usuario no encontrado');
        }
        $tokenModel = $user->mercadolibreTokens()->first();
        if (!$tokenModel) {
            throw new \Exception('Token no encontrado');
        }
        return $tokenModel->access_token;
    }
}
