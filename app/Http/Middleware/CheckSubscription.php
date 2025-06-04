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
        // Depuración inicial
        Log::info('CheckSubscription: Middleware ejecutado', ['route' => $request->path()]);

        $user = Auth::user()->load('suscripcion');

        if ($user && !$user->is_admin && $user->suscripcion && !in_array($user->suscripcion->plan, ['test', 'prueba_gratuita'])) {
            $subscription = $user->suscripcion;

            $daysToAdd = 30;
            if ($subscription->plan === 'trimestral') {
                $daysToAdd = 90;
            } elseif ($subscription->plan === 'anual') {
                $daysToAdd = 365;
            }

            $expirationDate = $subscription->fecha_fin ?? Carbon::parse($subscription->fecha_inicio)->addDays($daysToAdd);

            Log::info('CheckSubscription: Verificando vencimiento', [
                'user_id' => $user->id,
                'plan' => $subscription->plan,
                'fecha_fin' => $subscription->fecha_fin,
                'expiration_date' => $expirationDate->toDateTimeString(),
                'now' => Carbon::now()->toDateTimeString(),
                'is_expired' => Carbon::now()->greaterThan($expirationDate),
            ]);

            if (Carbon::now()->greaterThan($expirationDate)) {
                $subscription->update(['estado' => 'vencido']);
                return redirect()->route('subscription.expired')->with('error', 'Tu suscripción ha vencido. Por favor, renueva tu plan.');
            }
        }

        return $next($request);
    }
}
