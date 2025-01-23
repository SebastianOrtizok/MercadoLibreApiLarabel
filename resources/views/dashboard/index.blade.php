@extends('layouts.dashboard')

@section('content')
    <div class="wrap">
    <h1>Dashboard Mercadolibre</h1>

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
            <a  href="{{ route('dashboard.ventas') }}" class="icon-link" data-color="yellow">
                <i class="fas fa-dollar-sign"></i>
                <p>Ventas</p>
            </a>
            <a href="{{ route('sincronizacion.index') }}" class="icon-link" data-color="purple">
                <i class="fas fa-database"></i>
                <p>Sincronizar BD</p>
            </a>
        </div>
    </div>
</div>
@endsection
