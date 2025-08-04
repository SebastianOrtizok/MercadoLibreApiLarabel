<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MercadoLibreToken;
use App\Models\Subscription;
use Carbon\Carbon;

class TokenController extends Controller
{
    public function create()
    {
        $clientId = config('services.mercadolibre.client_id');
        $redirectUri = config('services.mercadolibre.redirect_uri', 'https://mercadolibreapi.onrender.com/tokens/callback');

        if (!$clientId) {
            Log::error('Client ID de Mercado Libre no está configurado en .env');
            return redirect()->route('dashboard')->with('error', 'Error de configuración. Contacta al administrador.');
        }

        $authUrl = "https://auth.mercadolibre.com.ar/authorization?response_type=code&client_id={$clientId}&redirect_uri=" . urlencode($redirectUri);

        Log::info('Generando URL de autorización de Mercado Libre', ['auth_url' => $authUrl]);

        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        $code = $request->query('code');
        $clientId = config('services.mercadolibre.client_id');
        $clientSecret = config('services.mercadolibre.client_secret');
        $redirectUri = config('services.mercadolibre.redirect_uri', 'https://mercadolibreapi.onrender.com/tokens/callback');

        if (!$code) {
            Log::error('Código de autorización de Mercado Libre no recibido', ['query' => $request->query()]);
            return redirect()->route('dashboard')->with('error', 'Error al autorizar la aplicación. Intenta nuevamente. Si el problema persiste, contacta al soporte de Mercado Libre.');
        }

        if (!$clientId || !$clientSecret) {
            Log::error('Credenciales de Mercado Libre no están configuradas en .env');
            return redirect()->route('dashboard')->with('error', 'Error de configuración. Contacta al administrador.');
        }

        $user = auth()->user();
        $subscription = Subscription::where('user_id', $user->id)->where('estado', 'activo')->first();
        $tokens = MercadoLibreToken::where('user_id', $user->id)->get();
        $tokenCount = $tokens->count();
        $maxAccounts = 1; // Default para mensual o prueba_gratuita

        // Determinar máximo según el plan
        if ($subscription) {
            switch ($subscription->plan) {
                case 'trimestral':
                    $maxAccounts = 2;
                    break;
                case 'anual':
                    $maxAccounts = 3;
                    break;
                case 'prueba_gratuita':
                case 'mensual':
                default:
                    $maxAccounts = 1;
            }
        }

        // Verificar si el usuario ha alcanzado el límite de cuentas
        if ($tokenCount >= $maxAccounts) {
            Log::warning('Límite de cuentas alcanzado para el usuario', ['user_id' => $user->id, 'token_count' => $tokenCount, 'max_accounts' => $maxAccounts]);
            return redirect()->route('dashboard')->with('error', 'Has alcanzado el límite de cuentas permitido por tu plan. Contacta al administrador.');
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

                // Verificar si el ml_account_id ya está vinculado a este usuario
                $existingToken = MercadoLibreToken::where('user_id', $user->id)->where('ml_account_id', $mlAccountId)->first();
                if ($existingToken) {
                    Log::warning('Intento de vincular una cuenta ya existente', ['user_id' => $user->id, 'ml_account_id' => $mlAccountId]);
                    return redirect()->route('dashboard')->with('error', 'Esta cuenta de Mercado Libre ya está vinculada. Usa una cuenta diferente.');
                }

                // Guardar el token en la base de datos
                MercadoLibreToken::updateOrCreate(
                    [
                        'user_id' => $user->id,
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
                    'user_id' => $user->id,
                    'ml_account_id' => $mlAccountId,
                ]);

                return redirect()->route('dashboard')->with('success', '¡Token generado exitosamente! Ahora puedes gestionar tu cuenta de Mercado Libre.');
            } else {
                Log::error('Error al intercambiar el código por un token en Mercado Libre', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
                return redirect()->route('dashboard')->with('error', 'Error al generar el token: ' . $response->json()['message']);
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
