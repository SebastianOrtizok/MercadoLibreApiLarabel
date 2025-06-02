<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class SellerIdFinderService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function findSellerIdByNickname($nickname)
    {
        $url = "https://api.mercadolibre.com/sites/MLA/search?seller_nickname=" . urlencode($nickname);

        Log::info("Buscando seller_id para el nickname", ['nickname' => $nickname, 'url' => $url]);

        try {
            $response = $this->client->get($url, ['timeout' => 10]);
            Log::info("Respuesta recibida", ['status' => $response->getStatusCode(), 'url' => $url]);

            if ($response->getStatusCode() !== 200) {
                Log::warning("Código de estado no esperado: {$response->getStatusCode()}");
                return null;
            }

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['seller']['id'])) {
                $sellerId = $data['seller']['id'];
                Log::info("Seller ID encontrado", ['nickname' => $nickname, 'seller_id' => $sellerId]);
                return $sellerId;
            }

            Log::warning("No se encontró seller_id para el nickname", ['nickname' => $nickname]);
            return null;
        } catch (RequestException $e) {
            Log::error("Error al buscar seller_id para el nickname", [
                'nickname' => $nickname,
                'error' => $e->getMessage(),
                'url' => $url,
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response',
            ]);
            return null;
        }
    }
}
