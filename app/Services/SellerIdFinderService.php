<?php

namespace App\Services;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class SellerIdFinderService
{
    protected $client;
    protected $scrapingClient;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->client = $mercadoLibreService->getHttpClient();
        $this->scrapingClient = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            ],
            'allow_redirects' => ['track_redirects' => true], // Seguimos redirecciones
        ]);
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
            // Si falla /users/me, continuamos con el scraping
        }

        // Si no es el usuario logueado, hacemos scraping
        return $this->scrapeSellerIdByNickname($nickname);
    }

    protected function scrapeSellerIdByNickname($nickname)
    {
        $url = "https://www.mercadolibre.com.ar/perfil/" . urlencode($nickname);

        Log::info("Realizando scraping para buscar seller_id", ['nickname' => $nickname, 'url' => $url]);

        try {
            $response = $this->scrapingClient->get($url, ['timeout' => 10]);
            $finalUrl = $response->getHeaderLine('X-Guzzle-Redirect-History') ?: $response->getEffectiveUri();

            // Extraer seller_id desde la URL redirigida si contiene _CustId_
            if (preg_match('/_CustId_(\d+)/', $finalUrl, $matches)) {
                $sellerId = $matches[1];
                Log::info("Seller ID encontrado en la redirección", ['nickname' => $nickname, 'seller_id' => $sellerId, 'url' => $finalUrl]);
                return $sellerId;
            }

            // Si no hay _CustId_, buscar en el HTML
            $html = $response->getBody()->getContents();

            if (preg_match('/"seller_id":"(\d+)"/', $html, $matches)) {
                $sellerId = $matches[1];
                Log::info("Seller ID encontrado en el HTML", ['nickname' => $nickname, 'seller_id' => $sellerId]);
                return $sellerId;
            }

            Log::warning("No se encontró el seller_id ni en la redirección ni en el HTML", ['nickname' => $nickname, 'url' => $finalUrl]);
            return null;
        } catch (RequestException $e) {
            Log::error("Error al realizar scraping para buscar seller_id", [
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
