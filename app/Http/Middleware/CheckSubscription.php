<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckSubscription
{
public function handle(Request $request, Closure $next)
{
    Log::info('CheckSubscription: Middleware ejecutado', ['route' => $request->path()]);

    $user = Auth::user()?->load('suscripcion');

    if ($user && !$user->is_admin && $user->suscripcion && !in_array($user->suscripcion->plan, ['test', 'prueba_gratuita'])) {
        $subscription = $user->suscripcion;

        $daysToAdd = match ($subscription->plan) {
            'trimestral' => 90,
            'anual' => 365,
            default => 30,
        };

        $expirationDate = $subscription->fecha_fin ?? Carbon::parse($subscription->fecha_inicio)->addDays($daysToAdd);

        Log::info('CheckSubscription: Verificando vencimiento', [
            'user_id' => $user->id,
            'plan' => $subscription->plan,
            'fecha_fin' => $subscription->fecha_fin,
            'expiration_date' => $expirationDate->toDateTimeString(),
            'now' => Carbon::now()->toDateTimeString(),
            'estado_actual' => $subscription->estado,
            'is_expired' => Carbon::now()->greaterThan($expirationDate),
        ]);

        if (Carbon::now()->greaterThan($expirationDate) && $subscription->estado !== 'vencido') {
            $subscription->update(['estado' => 'vencido']);
            Log::info('CheckSubscription: Estado actualizado a vencido.', ['user_id' => $user->id]);
        }
    }

    // Redirigir si está vencida, ya sea porque lo estaba o porque recién lo actualizamos
    if ($user && !$user->is_admin && $user->suscripcion && $user->suscripcion->estado === 'vencido') {
        Log::info('CheckSubscription: Suscripción vencida, redirigiendo.', ['user_id' => $user->id]);
        return redirect()->route('subscription.expired')->with('error', 'Tu suscripción ha vencido. Por favor, renueva tu plan.');
    }

    return $next($request);
}


}
