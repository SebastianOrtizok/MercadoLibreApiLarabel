<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suscripcion;
use App\Models\Pago;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Configurar el Access Token
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    public function showPlans()
    {
        $plans = [
            'mensual' => 100,
            'trimestral' => 270,
            'anual' => 960,
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
            'mensual' => 100,
            'trimestral' => 270,
            'anual' => 960,
        ];
        $amount = $amounts[$plan] ?? 100;

        try {
            // Crear el cliente de preferencias
            $client = new PreferenceClient();

            // Crear la preferencia
            $preference = $client->create([
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
                'auto_return' => 'approved',
                'external_reference' => auth()->id() . '|' . $plan,
            ]);

            Log::info('Preferencia Mercado Pago creada', [
                'preference_id' => $preference->id,
                'user_id' => auth()->id(),
                'plan' => $plan,
                'amount' => $amount,
            ]);

            // Redirigir al checkout de producción
            return redirect($preference->init_point);
        } catch (MPApiException $e) {
            Log::error('Error al crear la preferencia de Mercado Pago', [
                'error' => $e->getMessage(),
                'status' => $e->getApiResponse()->status,
                'content' => $e->getApiResponse()->content,
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
            $amount = $plan === 'mensual' ? 100 : ($plan === 'trimestral' ? 270 : 960);
            $duracion = $plan === 'mensual' ? 'month' : ($plan === 'trimestral' ? '3 months' : 'year');

            $suscripcion = Suscripcion::create([
                'usuario_id' => $userId,
                'plan' => $plan,
                'monto' => $amount,
                'fecha_fin' => now()->add($duracion),
                'estado' => 'activo',
            ]);

            Pago::create([
                'usuario_id' => $userId,
                'suscripcion_id' => $suscripcion->id,
                'monto' => $amount,
                'metodo_pago' => 'mercadopago',
                'id_transaccion' => $paymentId,
                'estado' => 'completado',
            ]);

            return redirect()->route('dashboard')->with('success', '¡Pago realizado con éxito! Tu suscripción está activa.');
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
        Log::info('Pago Mercado Pago pendiente', $request->all());
        return redirect()->route('plans')->with('info', 'El pago está pendiente. Te notificaremos cuando se complete.');
    }
}
