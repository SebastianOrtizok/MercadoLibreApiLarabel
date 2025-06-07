<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suscripcion;
use App\Models\Pago;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function __construct()
    {
        $accessToken = config('services.mercadopago.access_token');
        if (is_null($accessToken)) {
            Log::error('Access Token de Mercado Pago no está configurado en .env');
            throw new \Exception('Access Token de Mercado Pago no configurado. Por favor, verifica el archivo .env.');
        }
        MercadoPagoConfig::setAccessToken($accessToken);
    }

    public function showPlans()
    {
        $plans = [
            'mensual' => 10000,
            'trimestral' => 27000,
            'anual' => 960000,
        ];
        return view('payments.plans', compact('plans'));
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:mensual,trimestral,anual',
        ]);

        $plan = $request->input('plan');
        $amounts = [
            'mensual' => 10000,
            'trimestral' => 27000,
            'anual' => 96000,
        ];
        $amount = $amounts[$plan] ?? 100;

        try {
            $client = new PreferenceClient();
            $preferenceData = [
                'items' => [
                    [
                        'title' => "Suscripción $plan",
                        'quantity' => 1,
                        'unit_price' => (float) $amount,
                        'currency_id' => 'ARS',
                    ],
                ],
                'payer' => [
                    'email' => auth()->user()->email,
                ],
                'back_urls' => [
                    'success' => route('payment.success', ['user_id' => auth()->id(), 'plan' => $plan]),
                    'failure' => route('payment.failure'),
                    'pending' => route('payment.pending'),
                ],
                'auto_return' => 'approved', // Reintroducimos auto_return para Render
                'external_reference' => auth()->id() . '|' . $plan,
            ];

            Log::info('Datos enviados a Mercado Pago para crear preferencia', [
                'preference_data' => $preferenceData,
                'user_id' => auth()->id(),
            ]);

            $preference = $client->create($preferenceData);

            Log::info('Preferencia Mercado Pago creada', [
                'preference_id' => $preference->id,
                'user_id' => auth()->id(),
                'plan' => $plan,
                'amount' => $amount,
            ]);

            return redirect($preference->init_point);
        } catch (MPApiException $e) {
            Log::error('Error al crear la preferencia de Mercado Pago', [
                'error' => $e->getMessage(),
                'response' => $e->getApiResponse() ? [
                    'status' => method_exists($e->getApiResponse(), 'getStatusCode') ? $e->getApiResponse()->getStatusCode() : null,
                    'content' => method_exists($e->getApiResponse(), 'getContent') ? $e->getApiResponse()->getContent() : null,
                ] : 'No response available',
            ]);
            return redirect()->route('plans')->with('error', 'Error al generar el pago. Intenta nuevamente.');
        }
    }

   public function success(Request $request)
{
    $userId = $request->query('user_id');
    $plan = $request->query('plan');
    $paymentId = $request->query('payment_id', 'MP_TEST_' . now()->timestamp);

    Log::info('Pago Mercado Pago exitoso', [
        'user_id' => $userId,
        'plan' => $plan,
        'payment_id' => $paymentId,
    ]);

    if ($userId && $plan && in_array($plan, ['mensual', 'trimestral', 'anual'])) {
        $amount = $plan === 'mensual' ? 10000 : ($plan === 'trimestral' ? 27000 : 96000);
        $daysToAdd = $plan === 'mensual' ? 30 : ($plan === 'trimestral' ? 90 : 360);

        try {
            $fechaInicio = now();
            $fechaFin = $fechaInicio->copy()->addDays($daysToAdd);

            // Busca y actualiza o crea la suscripción
            $suscripcion = Suscripcion::updateOrCreate(
                ['usuario_id' => $userId], // Condición para buscar
                [
                    'plan' => $plan,
                    'monto' => $amount,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'estado' => 'activo',
                ]
            );

            // Crea el registro de pago
            Pago::create([
                'usuario_id' => $userId,
                'suscripcion_id' => $suscripcion->id,
                'monto' => $amount,
                'metodo_pago' => 'mercadopago',
                'id_transaccion' => $paymentId,
                'estado' => 'completado',
            ]);

            // Actualiza el estado del usuario en la sesión (opcional)
            $user = auth()->user();
            if ($user && $user->id == $userId) {
                $user->estado = 'activo'; // Ajusta según tu modelo User
                $user->save();
            }

            return redirect()->route('dashboard')->with('success', '¡Pago realizado con éxito! Tu suscripción está activa.');
        } catch (\Exception $e) {
            Log::error('Error al guardar suscripción o pago', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'plan' => $plan,
                'payment_id' => $paymentId,
            ]);
            return redirect()->route('plans')->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    return redirect()->route('plans')->with('error', 'Error al procesar el pago. Intenta nuevamente.');
}

    public function failure(Request $request)
    {
        Log::warning('Pago Mercado Pago fallido', $request->all());
        return redirect()->route('plans')->with('error', 'El pago fue cancelado o falló. Intenta nuevamente.');
    }

    public function pending(Request $request)
    {
        $userId = $request->query('user_id');
        $plan = $request->query('plan');
        $paymentId = $request->query('payment_id', 'MP_TEST_' . now()->timestamp);

        Log::info('Pago Mercado Pago pendiente', [
            'user_id' => $userId,
            'plan' => $plan,
            'payment_id' => $paymentId,
        ]);

        if ($userId && $plan && in_array($plan, ['mensual', 'trimestral', 'anual'])) {
            $amount = $plan === 'mensual' ? 10000 : ($plan === 'trimestral' ? 27000 : 96000);

            Pago::create([
                'usuario_id' => $userId,
                'suscripcion_id' => null,
                'monto' => $amount,
                'metodo_pago' => 'mercadopago',
                'id_transaccion' => $paymentId,
                'estado' => 'pendiente',
            ]);
        }

        return redirect()->route('plans')->with('info', 'El pago está pendiente. Te notificaremos cuando se complete.');
    }
}
