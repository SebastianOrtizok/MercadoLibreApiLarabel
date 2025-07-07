@extends('layouts.dashboard')

@section('content')
    <div class="wrap">
        <h1 class="home">Dashboard MLDataTrends</h1>

        @php
            $hasToken = \App\Models\MercadoLibreToken::where('user_id', auth()->id())->exists();
        @endphp

        @if (!$hasToken)
            <!-- Paso 1: Generar token -->
            <div class="token-message-container" style="background-color: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin: 20px 10px; text-align: center;">
                <h2 style="font-size: 1.5rem; color: #333; margin-bottom: 10px;">Paso 1: Configura tu Token de Mercado Libre</h2>
                <p style="font-size: 1rem; color: #666; margin-bottom: 20px; line-height: 1.5;">
                    Para comenzar a gestionar tus cuentas, publicaciones y ventas en Mercado Libre, necesitas generar un token de acceso. Durante el proceso de autorización, es posible que MercadoLibre muestre un mensaje indicando que nuestro sitio "no es confiable" o que "no está verificado". No te preocupes, esto es completamente normal. Este mensaje aparece porque MLDataTrends es una aplicación externa que interactúa con la API de MercadoLibre, y es parte del procedimiento estándar de seguridad de MercadoLibre para proteger a sus usuarios. Te aseguramos que nuestra plataforma cumple con los estándares de seguridad y que tus datos están protegidos.
Una vez que generes el token, podrás disfrutar de todas las herramientas de MLDataTrends sin inconvenientes. Si tienes alguna duda o necesitas ayuda durante el proceso, nuestro equipo de soporte está disponible para ayudarte. Escríbenos a soporte@mldatatrends.com o consulta nuestra sección de <a href="{{ url('/preguntas-frecuentes') }}">Preguntas Frecuentes</a> en el sitio.
.
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

            <!-- Paso 2: Recomendación de sincronización -->
            <div class="recommendation-message-container" style="background-color: #e6f0ff; border: 1px solid #d0e0ff; border-radius: 8px; padding: 20px; margin: 20px 10px; text-align: center;">
                <i class="fas fa-lightbulb fa-2x mb-3" style="color: #007bff;"></i>
                <h2 style="font-size: 1.5rem; color: #333; margin-bottom: 10px;">Paso 2: ¡Maximizá tu experiencia!</h2>
                <p style="font-size: 1rem; color: #666; margin-bottom: 20px; line-height: 1.5;">
                    Ahora que tenés tu token, te recomendamos visitar la sección de sincronización, donde podrás descargar todas tus publicaciones para visualizarlas directamente sin conexión, así como tus ventas para trabajarlas offline. Además, podrás anexar SKUs propios, sincronizar promociones, gestionar stock y mucho más. ¡Explorá todas las herramientas disponibles para potenciar tus ventas!
                </p>
                <a href="{{ route('sincronizacion.index') }}" class="btn btn-primary" style="padding: 12px 24px; font-weight: bold; transition: background-color 0.3s;">
                    Explorar Ahora
                </a>
            </div>
        @endif
    </div>
@endsection
