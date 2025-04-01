@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Ventas Consolidadas</h2>

    <!-- Formulario de filtros colapsado -->
    <div class="mb-4">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false" aria-controls="filtrosCollapse">
            <i class="fas fa-filter"></i> <span id="toggleText">Mostrar Filtros</span>
        </button>
        <div class="collapse" id="filtrosCollapse">
            <form method="GET" action="{{ route('dashboard.ventasconsolidadasdb') }}" class="mt-3">
                <div class="filtros-container p-3 bg-light rounded shadow-sm">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio', now()->subDays(30)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Buscar (SKU/Título)</label>
                            <input type="text" name="search" class="form-control" placeholder="Buscar por SKU o título" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Cuenta</label>
                            <select name="ml_account_id" class="form-control">
                                <option value="">Todas las cuentas</option>
                                @foreach (\DB::table('mercadolibre_tokens')->where('user_id', auth()->id())->select('ml_account_id', 'seller_name')->distinct()->get() as $account)
                                    <option value="{{ $account->ml_account_id }}" {{ request('ml_account_id') == $account->ml_account_id ? 'selected' : '' }}>
                                        {{ $account->seller_name ?? $account->ml_account_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Tipo de Publicación</label>
                            <select name="tipo_publicacion" class="form-control">
                                <option value="">Todos los tipos</option>
                                <option value="classic" {{ request('tipo_publicacion') == 'classic' ? 'selected' : '' }}>Clásica</option>
                                <option value="premium" {{ request('tipo_publicacion') == 'premium' ? 'selected' : '' }}>Premium</option>
                                <option value="gold_special" {{ request('tipo_publicacion') == 'gold_special' ? 'selected' : '' }}>Gold Special</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Tipo de Stock</label>
                            <select name="stock_type" class="form-control">
                                <option value="stock_actual" {{ request('stock_type', 'stock_actual') == 'stock_actual' ? 'selected' : '' }}>Stock Actual</option>
                                <option value="stock_deposito" {{ request('stock_type') == 'stock_deposito' ? 'selected' : '' }}>Stock Depósito</option>
                                <option value="stock_fulfillment" {{ request('stock_type') == 'stock_fulfillment' ? 'selected' : '' }}>Stock Fulfillment</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Estado de la Orden</label>
                            <select name="order_status" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="paid" {{ request('order_status') == 'paid' ? 'selected' : '' }}>Pagada</option>
                                <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>Enviada</option>
                                <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Estado de la Publicación</label>
                            <select name="estado_publicacion" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="active" {{ request('estado_publicacion') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="paused" {{ request('estado_publicacion') == 'paused' ? 'selected' : '' }}>Pausado</option>
                                <option value="under_review" {{ request('estado_publicacion') == 'under_review' ? 'selected' : '' }}>En revisión</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Consolidar por SKU</label>
                            <div class="form-check">
                                <input type="checkbox" name="consolidar_por_sku" value="true" class="form-check-input" {{ request('consolidar_por_sku') === 'true' ? 'checked' : '' }}>
                                <label class="form-check-label">Agrupar por SKU Interno</label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Botón para ir a "Sin Ventas" -->
    <div class="mb-4">
        <a href="{{ route('dashboard.sinventas') }}" class="btn btn-outline-secondary">
            <i class="fas fa-box-open"></i> Ver Artículos Sin Ventas
        </a>
    </div>

    <!-- Sección de resumen por cuenta -->
    <div class="card modern-card mb-4">
        <div class="card-header">
            <h4 class="mb-0">
                Resumen de Ventas ({{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }})
                <span class="days-range">{{ $diasDeRango }} días</span>
            </h4>
            <a href="{{ route('exportar.ventas') }}" class="btn btn-outline-primary btn-sm export-btn">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
        </div>
        <div class="card-body">
            @if(isset($resumenPorCuenta) && !empty($resumenPorCuenta))
                <div class="d-flex flex-wrap gap-3">
                    @foreach($resumenPorCuenta as $cuenta => $total)
                        <div class="progress-container" style="width: 100%; max-width: 400px;">
                            <div class="progress-label d-flex justify-content-between align-items-center mb-1">
                                <span>{{ $cuenta }}</span>
                                <span class="fw-bold">{{ $total }} ventas</span>
                            </div>
                            <div class="progress modern-progress">
                                <div class="progress-bar"
                                    role="progressbar"
                                    style="width: {{ log($total + 1) / log($maxVentasTotal + 1) * 100 }}%;"
                                    aria-valuenow="{{ $total }}"
                                    aria-valuemin="0"
                                    aria-valuemax="{{ $maxVentasTotal }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center">No hay datos de ventas para mostrar en este período.</p>
            @endif
            <div id="restore-columns-order" class="mt-3 d-flex flex-wrap gap-2"></div>
        </div>
    </div>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table id="orderTable" class="table table-hover modern-table">
            <thead>
                <tr>
                    <th data-column-name="Cuenta"><span>Cuenta</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Imagen"><span>Imagen</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Producto" data-sortable="true" data-column="producto">
                        <span>Producto</span><i class="fas fa-sort {{ $sortColumn == 'producto' ? ($sortDirection == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : '' }}"></i><i class="fas fa-eye toggle-visibility"></i>
                    </th>
                    <th data-column-name="SKU" data-sortable="true" data-column="sku">
                        <span>SKU</span><i class="fas fa-sort {{ $sortColumn == 'sku' ? ($sortDirection == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : '' }}"></i><i class="fas fa-eye toggle-visibility"></i>
                    </th>
                    <th data-column-name="Título" data-sortable="true" data-column="titulo">
                        <span>Título</span><i class="fas fa-sort {{ $sortColumn == 'titulo' ? ($sortDirection == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : '' }}"></i><i class="fas fa-eye toggle-visibility"></i>
                    </th>
                    <th data-column-name="Ventas" data-sortable="true" data-column="cantidad_vendida">
                        <span>Ventas</span><i class="fas fa-sort {{ $sortColumn == 'cantidad_vendida' ? ($sortDirection == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : '' }}"></i><i class="fas fa-eye toggle-visibility"></i>
                    </th>
                    <th data-column-name="Publicación"><span>Publicación</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Stock" data-sortable="true" data-column="stock">
                        <span>Stock ({{ request('stock_type', 'stock_actual') }})</span><i class="fas fa-sort {{ $sortColumn == 'stock' ? ($sortDirection == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : '' }}"></i><i class="fas fa-eye toggle-visibility"></i>
                    </th>
                    <th data-column-name="Días de Stock" data-sortable="true" data-column="dias_stock">
                        <span>Días de Stock</span><i class="fas fa-sort {{ $sortColumn == 'dias_stock' ? ($sortDirection == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : '' }}"></i><i class="fas fa-eye toggle-visibility"></i>
                    </th>
                    <th data-column-name="Estado de la Orden"><span>Estado Orden</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Estado de la Publicación"><span>Estado Pub.</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Fecha de Última Venta" data-sortable="true" data-column="fecha_ultima_venta">
                        <span>Última Venta</span><i class="fas fa-sort {{ $sortColumn == 'fecha_ultima_venta' ? ($sortDirection == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : '' }}"></i><i class="fas fa-eye toggle-visibility"></i>
                    </th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse ($data ?? collect() as $item)
                    <tr>
                        <td data-column="Cuenta">{{ $item['seller_name'] ?? 'N/A' }}</td>
                        <td><img src="{{ $item['imagen'] ?? asset('images/default.png') }}" alt="{{ $item['titulo'] ?? 'Sin título' }}" class="table-img"></td>
                        <td data-column="producto">
                            <a href="{{ route('dashboard.ventaid', ['item_id' => $item['producto'], 'fecha_inicio' => request('fecha_inicio'), 'fecha_fin' => request('fecha_fin')]) }}" class="table-link">{{ $item['producto'] }}</a>
                            <a href="{{ $item['url'] }}" target="_blank" class="table-icon-link"><i class="fas fa-external-link-alt"></i></a>
                        </td>
                        <td data-column="sku">{{ $item['sku'] ?? 'N/A' }}</td>
                        <td data-column="titulo">{{ $item['titulo'] }}</td>
                        <td data-column="cantidad_vendida">{{ $item['cantidad_vendida'] }}</td>
                        <td>{{ $item['tipo_publicacion'] ?? 'N/A' }}</td>
                        <td data-column="stock">{{ $item['stock'] ?? 'Sin stock' }}</td>
                        <td data-column="dias_stock" class="highlight">{{ $item['dias_stock'] ?? 'N/A' }}</td>
                        <td>{{ $item['order_status'] ?? 'N/A' }}</td>
                        <td>{{ $item['estado'] ?? 'Desconocido' }}</td>
                        <td>{{ $item['fecha_ultima_venta'] ? \Carbon\Carbon::parse($item['fecha_ultima_venta'])->format('d/m/Y H:i') : 'N/A' }}</td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @include('layouts.pagination', [
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'limit' => $limit
    ])
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
$(document).ready(function () {
    // Inicializar DataTables con las funcionalidades deseadas
    var table = $('#orderTable').DataTable({
        paging: false, // Desactivamos la paginación de DataTables porque usamos la del backend
        searching: false, // Desactivamos la búsqueda de DataTables
        info: false, // Ocultamos la info de "Showing 1 to 10 of X entries"
        colReorder: true, // Permitir mover columnas
        autoWidth: false, // Desactivar ajuste automático de ancho
        responsive: true, // Habilitar responsive (desplazamiento en móviles)
        scrollX: true, // Habilitar desplazamiento horizontal
        stateSave: false, // No guardar estado
        processing: true, // Mostrar "Processing" si hay demora
        columnDefs: [
            { targets: '_all', className: 'shrink-text dt-center' }, // Centrar texto
            { targets: 4, width: '20%' }, // Ajustar ancho de "Título"
            // Definir tipos numéricos para ordenamiento correcto en frontend (aunque no lo usamos mucho)
            { targets: 3, type: 'num' }, // SKU
            { targets: 5, type: 'num' }, // Ventas
            { targets: 7, type: 'num' }, // Stock
            { targets: 8, type: 'num' }  // Días de Stock
        ],
        order: [] // Sin ordenamiento inicial por DataTables, lo maneja el backend
    });

    // Manejar clics en las columnas ordenables (backend)
    $('th[data-sortable="true"]').on('click', function (e) {
        e.stopPropagation(); // Evitar que DataTables interfiera
        var column = $(this).data('column');
        var currentSortColumn = '{{ $sortColumn }}';
        var currentSortDirection = '{{ $sortDirection }}';
        var newDirection = (column === currentSortColumn && currentSortDirection === 'asc') ? 'desc' : 'asc';

        // Construir la URL con los parámetros actuales y los nuevos de ordenamiento
        var url = new URL(window.location.href);
        url.searchParams.set('sort_column', column);
        url.searchParams.set('sort_direction', newDirection);
        url.searchParams.set('page', 1); // Resetear a la primera página al ordenar
        window.location.href = url.toString();
    });

    // Manejar visibilidad de columnas
    var restoreContainer = $('#restore-columns-order');
    $('th i.fas.fa-eye.toggle-visibility').click(function (e) {
        e.stopPropagation(); // Evitar que el clic dispare el ordenamiento
        var th = $(this).closest('th');
        var columnIndex = table.column(th).index();
        var column = table.column(columnIndex);
        var columnName = th.data('column-name');

        // Alternar visibilidad
        column.visible(!column.visible());
        table.columns.adjust().draw(false);

        // Agregar botón para restaurar
        if (!column.visible()) {
            addRestoreButton(columnIndex, columnName);
        }
    });

    function addRestoreButton(columnIndex, columnName) {
        var button = $(`<button class="btn btn-outline-secondary btn-sm">${columnName} <i class="fas fa-eye"></i></button>`);
        button.on('click', function () {
            table.column(columnIndex).visible(true);
            table.columns.adjust().draw(false);
            $(this).remove();
        });
        restoreContainer.append(button);
    }
});

// Script para el menú de filtros
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('[data-bs-target="#filtrosCollapse"]');
    const toggleText = toggleBtn ? toggleBtn.querySelector('#toggleText') : null;
    const collapseElement = document.getElementById('filtrosCollapse');

    if (toggleBtn && toggleText && collapseElement) {
        toggleText.textContent = collapseElement.classList.contains('show') ? 'Ocultar Filtros' : 'Mostrar Filtros';

        collapseElement.addEventListener('shown.bs.collapse', function () {
            toggleText.textContent = 'Ocultar Filtros';
        });
        collapseElement.addEventListener('hidden.bs.collapse', function () {
            toggleText.textContent = 'Mostrar Filtros';
        });
    }
});
</script>
@endsection
