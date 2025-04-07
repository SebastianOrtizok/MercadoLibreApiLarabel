@extends('layouts.dashboard')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Estadísticas</h1>

        <!-- Formulario de Fechas -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="dateFilterForm" class="form-inline">
                    <div class="form-group mb-2 mr-3">
                        <label for="fecha_inicio" class="mr-2">Fecha Inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="form-group mb-2 mr-3">
                        <label for="fecha_fin" class="mr-2">Fecha Fin</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                </form>
            </div>
        </div>

        <!-- Cuadrícula de Gráficos -->
        <div class="row">
            <!-- Stock por Tipo -->
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Distribución de Stock</h5>
                        <div style="height: 200px;">
                            <canvas id="stockChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos en Promoción -->
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Productos en Promoción</h5>
                        <div style="height: 200px;">
                            <canvas id="promocionesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos por Estado -->
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Productos por Estado</h5>
                        <div style="height: 200px;">
                            <canvas id="estadoChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventas por Período -->
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Ventas y Facturación</h5>
                        <div style="height: 200px;">
                            <canvas id="ventasPeriodoChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventas por Día de la Semana -->
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Ventas por Día</h5>
                        <div style="height: 200px;">
                            <canvas id="ventasDiaSemanaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Pantalla Completa -->
        <div class="modal fade" id="fullscreenModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle"></h5>
                        <button type="button" class="close" id="closeModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <canvas id="fullscreenChart" style="height: 60vh;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Crítico -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Stock Crítico (Stock Fulfillment o depósito menor a 5)</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock Depósito</th>
                                <th>Stock Fulfillment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stockCritico as $item)
                                <tr>
                                    <td>{{ $item->titulo }}</td>
                                    <td>{{ $item->stock_deposito }}</td>
                                    <td>{{ $item->stock_fulfillment }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay productos con stock crítico.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Función para inicializar/destruir gráficos
    function initializeCharts() {
        // Destruir gráficos existentes si los hay
        Object.keys(charts).forEach(chartId => {
            if (charts[chartId]) {
                charts[chartId].destroy();
            }
        });

        // Inicializar nuevos gráficos
        charts.stockChart = new Chart(document.getElementById('stockChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Stock Actual', 'Stock Fulfillment', 'Stock Depósito'],
                datasets: [{
                    data: [{{ $stockPorTipo['stock_actual'] }}, {{ $stockPorTipo['stock_fulfillment'] }}, {{ $stockPorTipo['stock_deposito'] }}],
                    backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56']
                }]
            },
            options: {
                plugins: { legend: { position: 'top' } },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        charts.promocionesChart = new Chart(document.getElementById('promocionesChart').getContext('2d'), {
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
                scales: { y: { beginAtZero: true } },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        charts.estadoChart = new Chart(document.getElementById('estadoChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: @json($productosPorEstado->pluck('estado')),
                datasets: [{
                    data: @json($productosPorEstado->pluck('total')),
                    backgroundColor: ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56']
                }]
            },
            options: {
                plugins: { legend: { position: 'top' } },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        charts.ventasPeriodoChart = new Chart(document.getElementById('ventasPeriodoChart').getContext('2d'), {
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
                    y1: {
                        position: 'left',
                        beginAtZero: true,
                        title: { display: true, text: 'Artículos Vendidos' }
                    },
                    y2: {
                        position: 'right',
                        beginAtZero: true,
                        title: { display: true, text: 'Facturado ($)' }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        charts.ventasDiaSemanaChart = new Chart(document.getElementById('ventasDiaSemanaChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                datasets: [{
                    label: 'Artículos Vendidos',
                    data: @json(array_values($ventasPorDiaSemana)),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    const charts = {};
    initializeCharts(); // Inicializar al cargar la página

    // Modal para pantalla completa
    const modal = $('#fullscreenModal');
    const modalTitle = $('#modalTitle');
    const fullscreenChartCanvas = document.getElementById('fullscreenChart');
    let fullscreenChart = null;

    $('canvas').on('click', function() {
        const chartId = $(this).attr('id');
        const chartConfig = charts[chartId].config;
        modalTitle.text($(this).closest('.card').find('.card-title').text());
        if (fullscreenChart) fullscreenChart.destroy();
        fullscreenChart = new Chart(fullscreenChartCanvas.getContext('2d'), chartConfig);
        modal.modal('show');
    });

    $('#closeModal').on('click', function() {
        modal.modal('hide');
        if (fullscreenChart) fullscreenChart.destroy();
    });

    // Filtro de fechas dinámico
    $('#dateFilterForm').on('submit', function(e) {
        e.preventDefault();
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();

        fetch('/dashboard/estadisticas?' + new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        }), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('.container');
            document.querySelector('.container').replaceWith(newContent);
            initializeCharts(); // Reinicializar gráficos después de actualizar contenido
        })
        .catch(error => console.error('Error al actualizar gráficos:', error));
    });
</script>
@endsection
