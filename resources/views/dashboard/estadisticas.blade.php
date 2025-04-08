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

        <!-- Total Facturado Acumulado -->
        <div class="alert alert-info mb-4" role="alert">
            <h4 class="alert-heading">Total Facturado en el Período</h4>
            <p class="mb-0">${{ number_format($totalFacturado, 2, ',', '.') }}</p>
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

            <!-- Top 10 Productos Más Vendidos -->
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Top 10 Productos Vendidos</h5>
                        <div style="height: 200px;">
                            <canvas id="topProductosChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 20 Productos con Más Ventas -->
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Top 20 Más Vendidos</h5>
                        <div style="height: 200px;">
                            <canvas id="topVentasChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            </div> <!-- Cierre del div.row -->

        <!-- Modal para Pantalla Completa -->
        <div class="modal fade" id="fullscreenModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle"></h5>
                        <button type="button" class="close" id="closeModal" aria-label="Close">
                            <span aria-hidden="true">×</span>
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
    function initializeCharts() {
        Object.keys(charts).forEach(chartId => {
            if (charts[chartId]) {
                charts[chartId].destroy();
            }
        });

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

        charts.topProductosChart = new Chart(document.getElementById('topProductosChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($topProductosVendidos->pluck('titulo')->map(function($titulo) {
                    return strlen($titulo) > 15 ? substr($titulo, 0, 15) . '...' : $titulo;
                })),
                datasets: [
                    {
                        label: 'Cantidad Vendida',
                        data: @json($topProductosVendidos->pluck('total_vendido')),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        yAxisID: 'y1'
                    },
                    {
                        label: 'Total Facturado ($)',
                        data: @json($topProductosVendidos->pluck('total_facturado')),
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        yAxisID: 'y2'
                    }
                ]
            },
            options: {
                scales: {
                    y1: {
                        position: 'left',
                        beginAtZero: true,
                        title: { display: true, text: 'Cantidad Vendida' }
                    },
                    y2: {
                        position: 'right',
                        beginAtZero: true,
                        title: { display: true, text: 'Facturado ($)' }
                    },
                    x: {
                        ticks: {
                            maxRotation: 90,
                            minRotation: 90,
                            font: { size: 10 }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return @json($topProductosVendidos->pluck('titulo'))[context[0].dataIndex];
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        charts.topVentasChart = new Chart(document.getElementById('topVentasChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json(collect($topVentasPorCuenta)->flatMap(function ($productos) {
                    return $productos->pluck('titulo');
                })->map(function ($titulo) {
                    return strlen($titulo) > 15 ? substr($titulo, 0, 15) . '...' : $titulo;
                })->unique()->take(20)),
                datasets: [{
                    label: 'Tasa de Conversión (%)',
                    data: @json(collect($topVentasPorCuenta)->flatMap(function ($productos) {
                        return $productos->pluck('tasa_conversion');
                    })->unique()->take(20)),
                    backgroundColor: 'rgba(153, 102, 255, 0.6)'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Tasa de Conversión (%)' }
                    },
                    x: {
                        ticks: {
                            maxRotation: 90,
                            minRotation: 90,
                            font: { size: 10 }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return @json(collect($topVentasPorCuenta)->flatMap(function ($productos) {
                                    return $productos->pluck('titulo');
                                })->unique()->take(20))[context[0].dataIndex] || 'Sin título';
                            },
                            label: function(context) {
                                const item = @json(collect($topVentasPorCuenta)->flatMap(function ($productos) {
                                    return $productos;
                                })->unique('ml_product_id')->take(20))[context.dataIndex] || {};
                                return [
                                    `Ventas: ${item.total_vendido || 0}`,
                                    `Visitas: ${item.visitas || 0}`,
                                    `Tasa: ${item.tasa_conversion || 0}%`
                                ];
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // bottomVentasChart fue eliminado porque no lo necesitamos
    }

    const charts = {};
    initializeCharts();

    const modal = $('#fullscreenModal');
    const modalTitle = $('#modalTitle');
    const fullscreenChartCanvas = document.getElementById('fullscreenChart');
    let fullscreenChart = null;

    $('.container').on('click', 'canvas', function() {
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
            initializeCharts();
        })
        .catch(error => console.error('Error al actualizar gráficos:', error));
    });
</script>
@endsection
