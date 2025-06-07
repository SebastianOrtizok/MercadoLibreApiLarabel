@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-primary fw-bold">Elige tu Plan</h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm text-center h-100">
                <div class="card-header bg-primary text-white">
                    <h3>Mensual</h3>
                </div>
                <div class="card-body d-flex flex-column">
                    <h4 class="mb-3">$10,000 ARS</h4>
                    <p class="mb-4">Por mes</p>
                    <ul class="list-group list-group-flush flex-grow-1">
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Múltiples cuentas</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Descargar publicaciones</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Agregar SKU propio</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Descargar ventas</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Exportar a Excel</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Mostrar estadísticas</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Control de stock crítico</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Publicaciones en catálogo</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Ítems en promoción</li>
                        <li class="list-group-item"><i class="fas fa-users text-success me-2"></i>5 competidores</li>
                    </ul>
                    <form action="{{ route('payment.create') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="plan" value="mensual">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-cart me-2"></i> Suscribirme
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm text-center h-100">
                <div class="card-header bg-primary text-white">
                    <h3>Trimestral</h3>
                </div>
                <div class="card-body d-flex flex-column">
                    <h4 class="mb-3">$27,000 ARS</h4>
                    <p class="mb-4">Cada 3 meses</p>
                    <ul class="list-group list-group-flush flex-grow-1">
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Múltiples cuentas</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Descargar publicaciones</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Agregar SKU propio</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Descargar ventas</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Exportar a Excel</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Mostrar estadísticas</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Control de stock crítico</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Publicaciones en catálogo</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Ítems en promoción</li>
                        <li class="list-group-item"><i class="fas fa-users text-success me-2"></i>15 competidores</li>
                    </ul>
                    <form action="{{ route('payment.create') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="plan" value="trimestral">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-cart me-2"></i> Suscribirme
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm text-center h-100">
                <div class="card-header bg-primary text-white">
                    <h3>Anual</h3>
                </div>
                <div class="card-body d-flex flex-column">
                    <h4 class="mb-3">$96,000 ARS</h4>
                    <p class="mb-4">Por año</p>
                    <ul class="list-group list-group-flush flex-grow-1">
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Múltiples cuentas</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Descargar publicaciones</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Agregar SKU propio</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Descargar ventas</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Exportar a Excel</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Mostrar estadísticas</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Control de stock crítico</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Publicaciones en catálogo</li>
                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Ítems en promoción</li>
                        <li class="list-group-item"><i class="fas fa-users text-success me-2"></i>60 competidores</li>
                    </ul>
                    <form action="{{ route('payment.create') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="plan" value="anual">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-cart me-2"></i> Suscribirme
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
