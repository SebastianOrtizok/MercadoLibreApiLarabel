@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h1>Estadísticas</h1>

        <!-- Stock por Tipo -->
        <div class="card mb-4">
            <div class="card-header">Distribución de Stock</div>
            <div class="card-body">
                <canvas id="stockChart"></canvas>
            </div>
        </div>

        <!-- Productos en Promoción -->
        <div class="card mb-4">
            <div class="card-header">Productos en Promoción</div>
            <div class="card-body">
                <canvas id="promocionesChart"></canvas>
            </div>
        </div>

        <!-- Productos por Estado -->
        <div class="card mb-4">
            <div class="card-header">Productos por Estado</div>
            <div class="card-body">
                <canvas id="estadoChart"></canvas>
            </div>
        </div>

        <!-- Stock Crítico -->
        <div class="card">
            <div class="card-header">Stock Crítico</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Stock Actual</th>
                            <th>Stock Fulfillment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockCritico as $item)
                            <tr>
                                <td>{{ $item->titulo }}</td>
                                <td>{{ $item->stock_actual }}</td>
                                <td>{{ $item->stock_fulfillment }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts para Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Stock por Tipo
        const stockCtx = document.getElementById('stockChart').getContext('2d');
        new Chart(stockCtx, {
            type: 'doughnut',
            data: {
                labels: ['Stock Actual', 'Stock Fulfillment', 'Stock Depósito'],
                datasets: [{
                    data: [
                        {{ $stockPorTipo['stock_actual'] }},
                        {{ $stockPorTipo['stock_fulfillment'] }},
                        {{ $stockPorTipo['stock_deposito'] }}
                    ],
                    backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56']
                }]
            }
        });

        // Productos en Promoción
        const promocionesCtx = document.getElementById('promocionesChart').getContext('2d');
        new Chart(promocionesCtx, {
            type: 'bar',
            data: {
                labels: @json($productosEnPromocion->pluck('titulo')),
                datasets: [{
                    label: 'Descuento (%)',
                    data: @json($productosEnPromocion->pluck('descuento_porcentaje')),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }]
            }
        });

        // Productos por Estado
        const estadoCtx = document.getElementById('estadoChart').getContext('2d');
        new Chart(estadoCtx, {
            type: 'doughnut',
            data: {
                labels: @json($productosPorEstado->pluck('estado')),
                datasets: [{
                    data: @json($productosPorEstado->pluck('total')),
                    backgroundColor: ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56']
                }]
            }
        });
    </script>
@endsection
