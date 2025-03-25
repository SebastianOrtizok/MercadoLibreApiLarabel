@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Sincronización de Artículos, Promociones y Órdenes</h1>

    <!-- Mensajes de éxito o error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Sección de sincronización de artículos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Sincronización de Artículos</h2>
        </div>
        <div class="card-body">
            <p class="text-muted">Seleccione una cuenta para sincronizar artículos o actualice todas las cuentas:</p>

            @php
                $userId = auth()->id();
                $cuentas = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();
            @endphp

            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($cuentas as $cuenta)
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('sincronizacion.primera', ['user_id' => $cuenta->ml_account_id]) }}" class="text-decoration-none">
                        <button class="btn btn-outline-danger">
                            <i class="fas fa-sync-alt me-2"></i>Sincronizar {{ $cuenta->ml_account_id }}
                        </button>
                    </a>
                    <form action="{{ route('missing.articles.sync', ['mlAccountId' => $cuenta->ml_account_id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sync-alt me-2"></i>Sincronizar Faltantes {{ $cuenta->ml_account_id }}
                        </button>
                    </form>
                </div>
                @endforeach
            </div>

            <a href="{{ route('articulos.sync') }}" class="text-decoration-none">
                <button class="btn btn-warning w-100">
                    <i class="fas fa-sync-alt me-2"></i>Actualizar Todos los Artículos
                </button>
            </a>
        </div>
    </div>

    <!-- Sección de sincronización de órdenes -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h2 class="mb-0">Sincronización de Órdenes</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('sync.orders.db') }}" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label for="date_from" class="form-label">Desde:</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFromDefault ?? now()->subDays(7)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_to" class="form-label">Hasta:</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateToDefault ?? now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="order_status" class="form-label">Estado de la Orden:</label>
                        <select name="order_status" id="order_status" class="form-control">
                            <option value="paid" {{ request('order_status', 'paid') === 'paid' ? 'selected' : '' }}>Pagada</option>
                            <option value="pending" {{ request('order_status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="cancelled" {{ request('order_status') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            <option value="shipped" {{ request('order_status') === 'shipped' ? 'selected' : '' }}>Enviada</option>
                            <option value="delivered" {{ request('order_status') === 'delivered' ? 'selected' : '' }}>Entregada</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-download me-2"></i>Descargar Órdenes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

   <!-- Sección de sincronización de promociones -->
<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        <h2 class="mb-0">Sincronización de Promociones</h2>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <!-- Botón unificado para sincronizar promociones -->
            <a href="{{ route('sync.promotions.db') }}" class="text-decoration-none">
                <button class="btn btn-success">
                    <i class="fas fa-sync-alt me-2"></i>Sincronizar Promociones
                </button>
            </a>
            <!-- Botón para ver promociones -->
            <a href="{{ route('dashboard.item_promotions') }}" class="text-decoration-none">
                <button class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>Ver Promociones Sincronizadas
                </button>
            </a>
        </div>
    </div>
</div>

 <!-- Sección de sincronización de stock -->

    <div class="card shadow-sm mt-4">
    <div class="card-header bg-warning text-white">
        <h2 class="mb-0">Sincronización de Stock</h2>
    </div>
    <div class="card-body">
        <p class="text-muted">Sincroniza manualmente el stock de fulfillment y depósito:</p>

        <a href="{{ route('dashboard.stock.syncventas') }}" class="text-decoration-none">
            <button class="btn btn-warning">
                <i class="fas fa-sync-alt me-2"></i>Sincronizar Stock Ahora
            </button>
        </a>
        <a href="{{ route('dashboard.sync.ventas.stock') }}" class="text-decoration-none ml-2">
            <button class="btn btn-primary">
                <i class="fas fa-sync-alt me-2"></i>Sincronizar Ventas y Stock Ahora
            </button>
        </a>
    </div>
</div>

<!-- Estilos personalizados -->
<style>
    .card {
        border: none;
        border-radius: 10px;
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card-header {
        border-radius: 10px 10px 0 0;
        padding: 15px;
    }
    .card-body {
        padding: 20px;
    }
    .btn {
        border-radius: 5px;
        padding: 10px 20px;
        font-size: 16px;
    }
    .btn-outline-danger:hover {
        color: #fff;
    }
    .alert {
        margin-bottom: 20px;
        border-radius: 5px;
    }
</style>
@endsection
