@extends('layouts.dashboard')
@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Estadísticas</h1>

        <!-- Formulario de Fechas -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
            <form id="dateFilterForm" class="flex flex-col sm:flex-row gap-4">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div class="self-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Filtrar</button>
                </div>
            </form>
        </div>

        <!-- Cuadrícula de Gráficos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Stock por Tipo -->
            <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-lg font-semibold mb-2 text-gray-700">Distribución de Stock</h2>
                <div class="relative h-48">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>

            <!-- Productos en Promoción -->
            <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-lg font-semibold mb-2 text-gray-700">Productos en Promoción</h2>
                <div class="relative h-48">
                    <canvas id="promocionesChart"></canvas>
                </div>
            </div>

            <!-- Productos por Estado -->
            <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-lg font-semibold mb-2 text-gray-700">Productos por Estado</h2>
                <div class="relative h-48">
                    <canvas id="estadoChart"></canvas>
                </div>
            </div>

            <!-- Ventas por Período -->
            <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-lg font-semibold mb-2 text-gray-700">Ventas y Facturación</h2>
                <div class="relative h-48">
                    <canvas id="ventasPeriodoChart"></canvas>
                </div>
            </div>

            <!-- Ventas por Día de la Semana -->
            <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-lg font-semibold mb-2 text-gray-700">Ventas por Día de la Semana</h2>
                <div class="relative h-48">
                    <canvas id="ventasDiaSemanaChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Modal para Pantalla Completa -->
        <div id="fullscreenModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg max-w-4xl w-full">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="modalTitle" class="text-xl font-semibold text-gray-800"></h2>
                    <button id="closeModal" class="text-gray-600 hover:text-gray-800">&times;</button>
                </div>
                <canvas id="fullscreenChart" class="w-full h-[60vh]"></canvas>
            </div>
        </div>

        <!-- Stock Crítico -->
        <div class="mt-6 bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2 text-gray-700">Stock Crítico</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">Producto</th>
                            <th class="px-4 py-2">Stock Actual</th>
                            <th class="px-4 py-2">Stock Fulfillment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockCritico as $item)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $item->titulo }}</td>
                                <td class="px-4 py-2">{{ $item->stock_actual }}</td>
                                <td class="px-4 py-2">{{ $item->stock_fulfillment }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-center">No hay productos con stock crítico.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        // Almacenar configuraciones de gráficos
        const charts = {};

        // Stock por Tipo
        charts.stockChart = new Chart(document.getElementById('stockChart').getContext('2d'), {
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
            options: { plugins: { legend: { position: 'top' } }, responsive: true, maintainAspectRatio: false }
        });

        // Productos en Promoción
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
            options: { scales: { y: { beginAtZero: true } }, responsive: true, maintainAspectRatio: false }
        });

        // Productos por Estado
        charts.estadoChart = new Chart(document.getElementById('estadoChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: @json($productosPorEstado->pluck('estado')),
                datasets: [{
                    data: @json($productosPorEstado->pluck('total')),
                    backgroundColor: ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56']
                }]
            },
            options: { plugins: { legend: { position: 'top' } }, responsive: true, maintainAspectRatio: false }
        });

        // Ventas por Período
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
                    y1: { position: 'left', beginAtZero: true, title: { display: true, text: 'Artículos Vendidos' } },
                    y2: { position: 'right', beginAtZero: true, title: { display: true, text: 'Facturado ($)' } }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Ventas por Día de la Semana
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
            options: { scales: { y: { beginAtZero: true } }, responsive: true, maintainAspectRatio: false }
        });

        // Manejo de Pantalla Completa
        const modal = document.getElementById('fullscreenModal');
        const modalTitle = document.getElementById('modalTitle');
        const fullscreenChartCanvas = document.getElementById('fullscreenChart');
        const closeModal = document.getElementById('closeModal');
        let fullscreenChart = null;

        document.querySelectorAll('canvas').forEach(canvas => {
            canvas.addEventListener('click', () => {
                const chartId = canvas.id;
                const chartConfig = charts[chartId].config;
                modalTitle.textContent = canvas.closest('.bg-white').querySelector('h2').textContent;
                if (fullscreenChart) fullscreenChart.destroy();
                fullscreenChart = new Chart(fullscreenChartCanvas.getContext('2d'), chartConfig);
                modal.classList.remove('hidden');
            });
        });

        closeModal.addEventListener('click', () => {
            modal.classList.add('hidden');
            if (fullscreenChart) fullscreenChart.destroy();
        });

        // Filtro de Fechas Dinámico
        document.getElementById('dateFilterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;

            fetch('/dashboard/estadisticas?' + new URLSearchParams({ fecha_inicio: fechaInicio, fecha_fin: fechaFin }), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.container');
                document.querySelector('.container').replaceWith(newContent);

                // Re-inicializar gráficos después de recargar contenido
                // Esto es necesario porque el DOM se reemplazó
                Object.keys(charts).forEach(key => charts[key].destroy());
                // Ejecutar de nuevo el script de gráficos (puedes moverlo a una función externa si prefieres)
                eval(doc.querySelector('script').textContent);
            })
            .catch(error => console.error('Error al actualizar gráficos:', error));
        });
    </script>
@endsection
