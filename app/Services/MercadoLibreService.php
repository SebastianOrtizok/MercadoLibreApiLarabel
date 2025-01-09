<?php

namespace App\Services;

use App\Models\MercadoLibreToken;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class MercadoLibreService
{
    private $client;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.mercadolibre.com/',
            'timeout'  => 30.0,
        ]);

        $this->clientId = env('MERCADOLIBRE_CLIENT_ID');
        $this->clientSecret = env('MERCADOLIBRE_CLIENT_SECRET');
    }

    public function getHttpClient()
    {
        return $this->client;
    }


    /**
     * Guarda o actualiza el token en la base de datos.
     */
    public function saveOrUpdateToken($userId, $mlAccountId, $accessToken, $refreshToken, $expiresIn)
    {
        $expiresAt = now()->addSeconds($expiresIn);

        MercadoLibreToken::updateOrCreate(
            [
                'user_id' => $userId,
                'ml_account_id' => $mlAccountId,
            ],
            [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_at' => $expiresAt,
            ]
        );
    }

    /**
     * Renueva el access token utilizando el refresh token.
     */
    public function refreshAccessToken($token)
    {
        try {
            $response = $this->client->post('oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $token->refresh_token,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            // Guardar el nuevo token en la base de datos
            $this->saveOrUpdateToken(
                $token->user_id,
                $token->ml_account_id,
                $data['access_token'],
                $data['refresh_token'],
                $data['expires_in']
            );

            return $data['access_token'];
        } catch (RequestException $e) {
            \Log::error("Error al renovar el Access Token: " . $e->getMessage(), [
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            throw $e;
        }
    }

    /**
     * Verifica si el token ha expirado.
     */
    public function isTokenExpired($token)
    {
        return now()->greaterThanOrEqualTo($token->expires_at);
    }

    /**
     * Obtiene el token para un usuario y cuenta de MercadoLibre.
     */
    public function getTokenByUserAndAccount($userId, $mlAccountId)
    {
        return MercadoLibreToken::where('user_id', $userId)
            ->where('ml_account_id', $mlAccountId)
            ->orderByDesc('expires_at')
            ->first();
    }

    public function getAccessToken($userId, $mlAccountId)
    {
        $token = MercadoLibreToken::where('user_id', $userId)
            ->where('ml_account_id', $mlAccountId)
            ->orderByDesc('expires_at')
            ->first();

        if (!$token) {
            throw new \Exception('Token no encontrado para el usuario y cuenta de MercadoLibre.');
        }

        if ($this->isTokenExpired($token)) {
            // Si el token ha expirado, lo renovamos
            return $this->refreshAccessToken($token);
        }

        // Si no ha expirado, devolvemos el token
        return $token->access_token;
    }
}
