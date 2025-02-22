@extends('layouts.dashboard')


@section('content')
<div class="container">
    <h1 class="my-4">📊 Dashboard de Promociones</h1>

    <!-- Resumen General -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Promociones</h5>
                    <p class="card-text display-4">{{ count($promotions) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Promociones Activas</h5>
                    <p class="card-text display-4">{{ count(array_filter($promotions, fn($p) => $p['status'] === 'active')) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Promedio de Descuento</h5>
                    <p class="card-text display-4">{{ number_format(array_sum(array_column($promotions, 'discount')) / max(1, count($promotions)), 2) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Promociones -->
    <div class="card">
        <div class="card-header">Lista de Promociones</div>
        <div class="card-body">
            <table class="table table-striped" id="promotionsTable">
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
                            <td>{{ $promo['discount'] }}%</td>
                            <td>{{ \Carbon\Carbon::parse($promo['start_date'])->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($promo['end_date'])->format('d M Y') }}</td>
                            <td><span class="badge bg-{{ $promo['status'] === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($promo['status']) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection

<!-- Script para DataTables -->
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new DataTable("#promotionsTable");
    });
</script>
@endsection
