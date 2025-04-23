<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MercadoLibreToken;
use Carbon\Carbon;

class TokenController extends Controller
{
    public function create()
    {
        $clientId = config('services.mercadolibre.client_id');
        $redirectUri = route('tokens.callback');

        if (!$clientId) {
            Log::error('Client ID de Mercado Libre no está configurado en .env');
            return redirect()->route('dashboard')->with('error', 'Error de configuración. Contacta al administrador.');
        }

        $authUrl = "https://auth.mercadolibre.com.ar/authorization?response_type=code&client_id={$clientId}&redirect_uri=" . urlencode($redirectUri);

        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        $code = $request->query('code');
        $clientId = config('services.mercadolibre.client_id');
        $clientSecret = config('services.mercadolibre.client_secret');
        $redirectUri = route('tokens.callback');

        if (!$code) {
            Log::error('Código de autorización de Mercado Libre no recibido', ['query' => $request->query()]);
            return redirect()->route('dashboard')->with('error', 'Error al autorizar la aplicación. Intenta nuevamente.');
        }

        if (!$clientId || !$clientSecret) {
            Log::error('Credenciales de Mercado Libre no están configuradas en .env');
            return redirect()->route('dashboard')->with('error', 'Error de configuración. Contacta al administrador.');
        }

        try {
            // Intercambiar el código por un access_token y refresh_token
            $response = Http::post('https://api.mercadolibre.com/oauth/token', [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                $refreshToken = $data['refresh_token'];
                $expiresIn = $data['expires_in']; // En segundos (6 horas = 21600 segundos)

                // Obtener información del usuario para ml_account_id y seller_name
                $userResponse = Http::withToken($accessToken)->get('https://api.mercadolibre.com/users/me');
                if (!$userResponse->successful()) {
                    Log::error('Error al obtener información del usuario de Mercado Libre', [
                        'status' => $userResponse->status(),
                        'response' => $userResponse->json(),
                    ]);
                    return redirect()->route('dashboard')->with('error', 'Error al obtener datos de la cuenta. Intenta nuevamente.');
                }

                $userData = $userResponse->json();
                $mlAccountId = $userData['id'];
                $sellerName = $userData['nickname'];

                // Guardar el token en la base de datos
                MercadoLibreToken::updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'ml_account_id' => $mlAccountId,
                    ],
                    [
                        'seller_name' => $sellerName,
                        'access_token' => $accessToken,
                        'refresh_token' => $refreshToken,
                        'expires_at' => Carbon::now()->addSeconds($expiresIn),
                    ]
                );

                Log::info('Token de Mercado Libre generado exitosamente', [
                    'user_id' => auth()->id(),
                    'ml_account_id' => $mlAccountId,
                ]);

                return redirect()->route('dashboard')->with('success', '¡Token generado exitosamente! Ahora puedes gestionar tu cuenta de Mercado Libre.');
            } else {
                Log::error('Error al intercambiar el código por un token en Mercado Libre', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
                return redirect()->route('dashboard')->with('error', 'Error al generar el token. Intenta nuevamente.');
            }
        } catch (\Exception $e) {
            Log::error('Excepción al procesar el callback de Mercado Libre', [
                'error' => $e->getMessage(),
                'query' => $request->query(),
            ]);
            return redirect()->route('dashboard')->with('error', 'Error inesperado al generar el token: ' . $e->getMessage());
        }
    }
}
