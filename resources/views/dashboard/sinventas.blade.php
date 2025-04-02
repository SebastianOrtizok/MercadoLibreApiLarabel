@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Artículos Sin Ventas</h2>

    <!-- Botón para volver a Ventas Consolidadas -->
    <div class="mb-4">
        <a href="{{ route('dashboard.ventasconsolidadasdb') }}" class="btn btn-outline-secondary">
            <i class="fas fa-shopping-cart"></i> Ver Ventas Consolidadas
        </a>
    </div>

    <!-- Formulario de filtros colapsado -->
    <div class="mb-4">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false" aria-controls="filtrosCollapse">
            <i class="fas fa-filter"></i> <span id="toggleText">Mostrar Filtros</span>
        </button>
        <div class="collapse" id="filtrosCollapse">
            <form method="GET" action="{{ route('dashboard.sinventas') }}" class="mt-3">
                <div class="filtros-container p-3 bg-light rounded shadow-sm">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio', $fechaInicio->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin', $fechaFin->format('Y-m-d')) }}">
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

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table id="sinVentasTable" class="table table-hover modern-table">
            <thead>
                <tr>
                    <th data-column-name="Cuenta"><span>Cuenta</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Imagen"><span>Imagen</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Producto" data-sortable="true" data-column="ml_product_id"><span>Producto</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="SKU" data-sortable="true" data-column="sku"><span>SKU</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Título" data-sortable="true" data-column="titulo"><span>Título</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Ventas" data-sortable="true" data-column="cantidad_vendida"><span>Ventas</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Publicación"><span>Publicación</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Stock" data-sortable="true" data-column="stock_actual"><span>Stock</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Estado de la Publicación"><span>Estado Pub.</span><i class="fas fa-eye toggle-visibility"></i></th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse ($productosPorVentas as $producto)
                    <tr>
                        <td data-column="Cuenta">{{ $producto->seller_name ?? 'N/A' }}</td>
                        <td><img src="{{ $producto->imagen ?? asset('images/default.png') }}" alt="{{ $producto->titulo ?? 'Sin título' }}" class="table-img"></td>
                        <td data-column="ml_product_id">
                            @if ($consolidarPorSku)
                                {{ $producto->ml_product_id }} <!-- SKU consolidado -->
                            @else
                                <a href="{{ $producto->permalink }}" target="_blank" class="table-link">{{ $producto->ml_product_id }}</a>
                                <a href="{{ $producto->permalink }}" target="_blank" class="table-icon-link"><i class="fas fa-external-link-alt"></i></a>
                            @endif
                        </td>
                        <td data-column="sku">{{ $producto->sku ?? 'N/A' }}</td>
                        <td data-column="titulo">{{ $producto->titulo }}</td>
                        <td data-column="cantidad_vendida" class="highlight">{{ $producto->cantidad_vendida }}</td>
                        <td>{{ $producto->tipo_publicacion ?? 'N/A' }}</td>
                        <td data-column="stock_actual">{{ $producto->stock_actual ?? 'Sin stock' }}</td>
                        <td>{{ $producto->estado ?? 'Desconocido' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No hay artículos sin ventas en el período seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.pagination', [
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'limit' => $limit
    ])
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {
    // Manejar visibilidad de columnas
    var restoreContainer = $('#restore-columns-order');
    $('th i.fas.fa-eye.toggle-visibility').click(function (e) {
        e.stopPropagation();
        var th = $(this).closest('th');
        var columnName = th.data('column-name');
        var columnCells = $('td[data-column="' + columnName + '"], th[data-column-name="' + columnName + '"]');
        columnCells.toggle();
        addRestoreButton(th, columnName);
    });

    function addRestoreButton(th, columnName) {
        var button = $(`<button class="btn btn-outline-secondary btn-sm">${columnName} <i class="fas fa-eye"></i></button>`);
        button.on('click', function () {
            $('td[data-column="' + columnName + '"], th[data-column-name="' + columnName + '"]').show();
            $(this).remove();
        });
        restoreContainer.append(button);
    }

    // Script para el menú de filtros
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

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
jQuery(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#sinVentasTable')) {
        $('#sinVentasTable').DataTable().clear().destroy();
    }
    var table = $('#sinVentasTable').DataTable({
        paging: false,
        searching: false,
        info: true,
        colReorder: true,
        autoWidth: false,
        responsive: true,
        scrollX: true,
        stateSave: false,
        processing: true,
        width: '95%',
        columnDefs: [
            { targets: '_all', className: 'shrink-text dt-center' },
            { targets: [4], width: '20%' }, // Título
            { targets: [3], type: 'num' },  // SKU (índice 3)
            { targets: [5], type: 'num' },  // Ventas (índice 5)
            { targets: [7], type: 'num' }   // Stock (índice 7)
        ]
    });

    var restoreContainer = $('#restore-columns-sinventas');

    $('th i.fas.fa-eye.toggle-visibility').click(function () {
        var th = $(this).closest('th');
        var columnName = th.data('column-name');
        var column = table.column(th);
        column.visible(false);
        table.columns.adjust().draw(false);
        addRestoreButton(th, columnName);
    });

    function addRestoreButton(th, columnName) {
        var button = $(`<button class="btn btn-outline-secondary btn-sm">${columnName} <i class="fas fa-eye"></i></button>`);
        button.on('click', function () {
            table.column(th).visible(true);
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
