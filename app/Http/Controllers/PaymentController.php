<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suscripcion;
use App\Models\Pago;

class PaymentController extends Controller
{
    public function showPlans()
    {
        $plans = [
            'mensual' => 20,
            'trimestral' => 54,
            'anual' => 192,
        ];
        return view('payments.plans', compact('plans'));
    }

    public function createPayment(Request $request)
{
    $plan = $request->input('plan');
    $amounts = [
        'mensual' => 1000,
        'trimestral' => 2700,
        'anual' => 9600,
    ];
    $amount = $amounts[$plan] ?? 1000; // Valor por defecto si $plan no coincide

    $preference = new \MercadoPago\Preference();
    $item = new \MercadoPago\Item();
    $item->title = "Suscripción $plan";
    $item->quantity = 1;
    $item->unit_price = (float) $amount; // Aseguramos que sea float
    $preference->items = [$item];
    $preference->payer = new \MercadoPago\Payer();
    $preference->payer->email = auth()->user()->email;
    $preference->back_urls = [
        'success' => route('payment.success'),
        'failure' => route('payment.failure'),
        'pending' => route('payment.pending'),
    ];
    $preference->auto_return = 'approved';
    $preference->save();

    return redirect($preference->init_point); // Esto te lleva al checkout de MP
}

    public function success(Request $request)
    {
        $userId = $request->query('user_id');
        $plan = $request->query('plan');
        $txnId = $request->query('txn_id', 'SANDBOX_' . now()->timestamp); // ID temporal si no hay txn_id

        if ($userId && $plan) {
            $amount = $plan === 'mensual' ? 20 : ($plan === 'trimestral' ? 54 : 192);
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
                'metodo_pago' => 'paypal',
                'id_transaccion' => $txnId,
                'estado' => 'completado',
            ]);
        }

        return redirect()->route('dashboard')->with('success', '¡Pago realizado con éxito! Revisa tu suscripción.');
    }

    public function ipn(Request $request)
    {
        // Log para ver qué llega
        \Log::info('IPN recibido', $request->all());

        // Procesar si el pago está completado
        if ($request->input('payment_status') === 'Completed') {
            [$userId, $plan] = explode('|', $request->input('custom'));
            $amount = $request->input('mc_gross');
            $transactionId = $request->input('txn_id');

            // Crear suscripción
            $duracion = $plan === 'mensual' ? 'month' : ($plan === 'trimestral' ? '3 months' : 'year');
            $suscripcion = Suscripcion::create([
                'usuario_id' => $userId,
                'plan' => $plan,
                'monto' => $amount,
                'fecha_fin' => now()->add($duracion),
                'estado' => 'activo',
            ]);

            // Registrar pago
            Pago::create([
                'usuario_id' => $userId,
                'suscripcion_id' => $suscripcion->id,
                'monto' => $amount,
                'metodo_pago' => 'paypal',
                'id_transaccion' => $transactionId,
                'estado' => 'completado',
            ]);
        }

        return response('OK', 200);
    }
}
