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

    public function findSellerIdByNickname($nickname, $token, $mlAccountId)
    {
        // Primero, obtenemos el nickname del usuario logueado con /users/me
        $userInfoUrl = "https://api.mercadolibre.com/users/me";
        try {
            $response = $this->client->get($userInfoUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'timeout' => 10,
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::warning("No se pudo obtener información del usuario logueado", ['status' => $response->getStatusCode()]);
                return null;
            }

            $userData = json_decode($response->getBody()->getContents(), true);
            $loggedInNickname = $userData['nickname'] ?? null;

            // Si el nickname ingresado es el del usuario logueado, devolvemos su ml_account_id
            if ($loggedInNickname && strtolower($loggedInNickname) === strtolower($nickname)) {
                Log::info("El nickname ingresado coincide con el usuario logueado", ['nickname' => $nickname, 'seller_id' => $mlAccountId]);
                return $mlAccountId;
            }
        } catch (RequestException $e) {
            Log::error("Error al obtener información del usuario logueado", [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response',
            ]);
            // Si falla /users/me, continuamos con el otro método
        }

        // Si no es el usuario logueado, buscamos publicaciones del competidor
        $url = "https://api.mercadolibre.com/sites/MLA/search?seller_nickname=" . urlencode($nickname);

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

            // Verificar si hay resultados y extraer el seller_id
            if (isset($data['results']) && !empty($data['results'])) {
                $sellerId = $data['results'][0]['seller']['id'];
                Log::info("Seller ID encontrado", ['nickname' => $nickname, 'seller_id' => $sellerId]);
                return $sellerId;
            }

            Log::warning("No se encontraron publicaciones para el nickname", ['nickname' => $nickname]);
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
