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
            $isExpired = Carbon::now()->greaterThan($expirationDate);

            Log::info('CheckSubscription: Verificando vencimiento', [
                'user_id' => $user->id,
                'plan' => $subscription->plan,
                'fecha_fin' => $subscription->fecha_fin,
                'expiration_date' => $expirationDate->toDateTimeString(),
                'now' => Carbon::now()->toDateTimeString(),
                'is_expired' => $isExpired,
                'estado_actual' => $subscription->estado,
            ]);

            // Si está vencido y no está marcado como vencido → actualizar y redirigir
            if ($isExpired && $subscription->estado !== 'vencido') {
                Log::info('CheckSubscription: Actualizando estado a vencido', ['user_id' => $user->id]);
                $subscription->update(['estado' => 'vencido']);
                return redirect()->route('subscription.expired')->with('error', 'Tu suscripción ha vencido. Por favor, renueva tu plan.');
            }

            // Si NO está vencido pero quedó como "vencido" → corregir a "activo"
            if (!$isExpired && $subscription->estado === 'vencido') {
                Log::info('CheckSubscription: Corrigiendo estado a activo', ['user_id' => $user->id]);
                $subscription->update(['estado' => 'activo']);
            }

            // Si está vencido y ya está marcado como vencido → redirigir igual
            if ($isExpired && $subscription->estado === 'vencido') {
                Log::info('CheckSubscription: Ya vencido, redirigiendo', ['user_id' => $user->id]);
                return redirect()->route('subscription.expired')->with('error', 'Tu suscripción ha vencido. Por favor, renueva tu plan.');
            }
        }

        return $next($request);
    }
}
