@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1 class="main-title my-4">📊 Dashboard de Promociones</h1>

    <!-- Resumen General -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="summary-card">
                <div class="card-body">
                    <h5 class="card-title">Total Promociones</h5>
                    <p class="card-value">{{ count($promotions) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="card-body">
                    <h5 class="card-title">Promociones Activas</h5>
                    <p class="card-value">{{ count(array_filter($promotions, fn($p) => $p['status'] === 'active')) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="card-body">
                    <h5 class="card-title">Promedio de Descuento</h5>
                    <p class="card-value">{{ number_format(array_sum(array_column($promotions, 'discount')) / max(1, count($promotions)), 2) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Promociones -->
    <div class="card modern-card">
        <div class="card-header">Lista de Promociones</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table modern-table" id="promotionsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Descuento</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotions as $promo)
                            <tr>
                                <td>{{ $promo['id'] }}</td>
                                <td>{{ $promo['title'] }}</td>
                                <td>{{ ucfirst($promo['type']) }}</td>
                                <td class="highlight">{{ $promo['discount'] }}%</td>
                                <td>{{ \Carbon\Carbon::parse($promo['start_date'])->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($promo['end_date'])->format('d M Y') }}</td>
                                <td>
                                    <span class="status-badge {{ $promo['status'] === 'active' ? 'active' : 'inactive' }}">
                                        {{ ucfirst($promo['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
