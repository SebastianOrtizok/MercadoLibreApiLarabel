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
                        @forelse ($stockCritico as $item)
                            <tr>
                                <td>{{ $item->titulo }}</td>
                                <td>{{ $item->stock_actual }}</td>
                                <td>{{ $item->stock_fulfillment }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">No hay productos con stock crítico.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        console.log('Stock por Tipo:', @json($stockPorTipo));
        console.log('Productos en Promoción:', @json($productosEnPromocion));
        console.log('Productos por Estado:', @json($productosPorEstado));

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
            },
            options: {
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });

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
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const estadoCtx = document.getElementById('estadoChart').getContext('2d');
        new Chart(estadoCtx, {
            type: 'doughnut',
            data: {
                labels: @json($productosPorEstado->pluck('estado')),
                datasets: [{
                    data: @json($productosPorEstado->pluck('total')),
                    backgroundColor: ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56']
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    </script>
@endsection
