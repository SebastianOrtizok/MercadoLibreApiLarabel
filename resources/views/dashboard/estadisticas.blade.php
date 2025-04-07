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

        <!-- Ventas por Período -->
        <div class="card mb-4">
            <div class="card-header">Ventas y Facturación ({{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }})</div>
            <div class="card-body">
                <canvas id="ventasPeriodoChart"></canvas>
            </div>
        </div>

        <!-- Ventas por Día de la Semana -->
        <div class="card mb-4">
            <div class="card-header">Ventas por Día de la Semana</div>
            <div class="card-body">
                <canvas id="ventasDiaSemanaChart"></canvas>
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
        console.log('Ventas por Período:', @json($ventasPorPeriodo));
        console.log('Ventas por Día de la Semana:', @json($ventasPorDiaSemana));

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
            },
            options: { plugins: { legend: { position: 'top' } } }
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
            },
            options: { scales: { y: { beginAtZero: true } } }
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
            },
            options: { plugins: { legend: { position: 'top' } } }
        });

        // Ventas por Período
        const ventasPeriodoCtx = document.getElementById('ventasPeriodoChart').getContext('2d');
        new Chart(ventasPeriodoCtx, {
            type: 'line',
            data: {
                labels: @json($ventasPorPeriodo->pluck('fecha')),
                datasets: [
                    {
                        label: 'Artículos Vendidos',
                        data: @json($ventasPorPeriodo->pluck('total_vendido')),
                        borderColor: '#36A2EB',
                        yAxisID: 'y1',
                        fill: false
                    },
                    {
                        label: 'Total Facturado',
                        data: @json($ventasPorPeriodo->pluck('total_facturado')),
                        borderColor: '#FF6384',
                        yAxisID: 'y2',
                        fill: false
                    }
                ]
            },
            options: {
                scales: {
                    y1: { position: 'left', beginAtZero: true, title: { display: true, text: 'Artículos Vendidos' } },
                    y2: { position: 'right', beginAtZero: true, title: { display: true, text: 'Facturado ($)' } }
                }
            }
        });

        // Ventas por Día de la Semana
        const ventasDiaSemanaCtx = document.getElementById('ventasDiaSemanaChart').getContext('2d');
        new Chart(ventasDiaSemanaCtx, {
            type: 'bar',
            data: {
                labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                datasets: [{
                    label: 'Artículos Vendidos',
                    data: @json(array_values($ventasPorDiaSemana)),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    </script>
@endsection
