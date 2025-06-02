<?php

namespace App\Services;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class SellerIdFinderService
{
    protected $client;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->client = $mercadoLibreService->getHttpClient();
    }

    public function findSellerIdByNickname($nickname, $token)
    {
        $url = "https://api.mercadolibre.com/users/" . urlencode($nickname);

        Log::info("Buscando seller_id para el nickname", ['nickname' => $nickname, 'url' => $url]);

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'timeout' => 10,
            ]);

            Log::info("Respuesta recibida", ['status' => $response->getStatusCode(), 'url' => $url]);

            if ($response->getStatusCode() !== 200) {
                Log::warning("Código de estado no esperado: {$response->getStatusCode()}");
                return null;
            }

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['id'])) {
                $sellerId = $data['id'];
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
