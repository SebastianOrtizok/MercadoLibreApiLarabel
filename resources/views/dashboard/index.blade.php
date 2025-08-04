@extends('layouts.dashboard')

@section('content')
    <div class="wrap">
        <h1 class="home">Dashboard MLDataTrends</h1>

        @php
            $user = auth()->user();
            $subscription = \App\Models\Suscripcion::where('usuario_id', $user->id)->where('estado', 'activo')->first(); // Corregido a Suscripcion y usuario_id
            $tokens = \App\Models\MercadoLibreToken::where('user_id', $user->id)->get();
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
        @endphp

        @if ($tokenCount < $maxAccounts)
            <!-- Paso 1: Generar token -->
            <div class="token-message-container" style="background-color: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin: 20px 10px; text-align: center;">
                <h2 style="font-size: 1.5rem; color: #333; margin-bottom: 10px;">Paso 1: Configura tu Token de Mercado Libre</h2>
                <p style="font-size: 1rem; color: #666; margin-bottom: 20px; line-height: 1.5;">
                    Tu plan permite hasta {{$maxAccounts}} cuentas, y ya tienes {{$tokenCount}} vinculada(s). Durante el proceso de autorización, es posible que MercadoLibre muestre un mensaje indicando que nuestro sitio "no es confiable" o que "no está verificado". No te preocupes, esto es normal. [...]
                </p>
                @for ($i = $tokenCount; $i < $maxAccounts; $i++)
                    <a href="{{ route('tokens.create') }}" class="btn-generate-token" style="display: inline-block; padding: 12px 24px; background-color: #3483fa; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s; margin: 5px;">
                        Generar Token para Cuenta {{$i + 1}}
                    </a>
                @endfor
            </div>
        @elseif ($tokenCount >= $maxAccounts)
            <div class="token-message-container" style="background-color: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin: 20px 10px; text-align: center;">
                <h2 style="font-size: 1.5rem; color: #333; margin-bottom: 10px;">Límite de Cuentas Alcanzado</h2>
                <p style="font-size: 1rem; color: #666; margin-bottom: 20px; line-height: 1.5;">
                    Has alcanzado el límite de {{$maxAccounts}} cuentas. Contacta al administrador si necesitas más.
                </p>
            </div>
        @endif

        @if ($tokenCount > 0)
            <!-- Dashboard de accesos rápidos -->
            <div class="dashboard-container">
                <div class="icon-container">
                    <a href="{{ route('dashboard.account') }}" class="icon-link" data-color="blue">
                        <i class="fas fa-user-tag"></i>
                        <p>Cuentas</p>
                    </a>
                    <a href="{{ route('dashboard.publications') }}" class="icon-link" data-color="green">
                        <i class="fas fa-list"></i>
                        <p>Publicaciones</p>
                    </a>
                    <a href="{{ route('dashboard.ventas') }}" class="icon-link" data-color="yellow">
                        <i class="fas fa-dollar-sign"></i>
                        <p>Ventas</p>
                    </a>
                    <a href="{{ route('sincronizacion.index') }}" class="icon-link" data-color="purple">
                        <i class="fas fa-database"></i>
                        <p>Sincronizar BD</p>
                    </a>
                </div>
            </div>

            <!-- Paso 2: Recomendación de sincronización -->
            <div class="recommendation-message-container" style="background-color: #e6f0ff; border: 1px solid #d0e0ff; border-radius: 8px; padding: 20px; margin: 20px 10px; text-align: center;">
                <i class="fas fa-lightbulb fa-2x mb-3" style="color: #007bff;"></i>
                <h2 style="font-size: 1.5rem; color: #333; margin-bottom: 10px;">Paso 2: ¡Maximizá tu experiencia!</h2>
                <p style="font-size: 1rem; color: #666; margin-bottom: 20px; line-height: 1.5;">
                    Ahora que tenés tus cuentas vinculadas, te recomendamos visitar la sección de sincronización, donde podrás descargar tus publicaciones y ventas. [...]
                </p>
                <a href="{{ route('sincronizacion.index') }}" class="btn btn-primary" style="padding: 12px 24px; font-weight: bold; transition: background-color 0.3s;">
                    Explorar Ahora
                </a>
            </div>
        @endif
    </div>
@endsection
