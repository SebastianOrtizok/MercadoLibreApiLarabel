@extends('layouts.dashboard')

@section('content')
    <div class="wrap">
        <h1 class="home">Dashboard Mercadolibre</h1>

        @php
            $hasToken = \App\Models\MercadoLibreToken::where('user_id', auth()->id())->exists();
        @endphp

        @if (!$hasToken)
            <!-- Mensaje para generar token -->
            <div class="token-message-container" style="background-color: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin: 20px 10px; text-align: center;">
                <h2 style="font-size: 1.5rem; color: #333; margin-bottom: 10px;">Configura tu Token de Mercado Libre</h2>
                <p style="font-size: 1rem; color: #666; margin-bottom: 20px; line-height: 1.5;">
                    Para comenzar a gestionar tus cuentas, publicaciones y ventas en Mercado Libre, necesitas generar un token de acceso. Este token permitirá que nuestra plataforma se conecte de forma segura a tu cuenta de Mercado Libre y sincronice tus datos automáticamente.
                </p>
                <a href="{{ route('tokens.create') }}" class="btn-generate-token" style="display: inline-block; padding: 12px 24px; background-color: #3483fa; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s;">
                    Generar Token Ahora
                </a>
            </div>
        @else
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
        @endif
    </div>
@endsection
