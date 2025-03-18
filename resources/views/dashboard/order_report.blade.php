@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Ventas Online</h2>

    <!-- Formulario para seleccionar el rango de días -->
    <form method="GET" action="{{ route('dashboard.ventas') }}" class="mb-4">
        <div class="form-row d-flex align-items-center gap-3">
            <div class="col-md-3 mb-2">
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3 mb-2">
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Filtros y buscador en el frontend -->
    <div class="filtros-container mb-4 p-3 bg-light rounded shadow-sm">
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar por título o SKU">
            </div>
            <div class="col-md-4 mb-2">
                <select id="estadoFilter" class="form-control">
                    <option value="paid">Pagadas</option>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <select id="estadoPublicacionFilter" class="form-control">
                    <option value="">Todos los estados de la publicación</option>
                    <option value="active">Activo</option>
                    <option value="paused">Pausado</option>
                    <option value="under_review">Revisión</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Mostrar las fechas seleccionadas -->
    <p class="mb-3">
        <strong>Rango de fechas:</strong>
        {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} -
        {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }} -
        {{ $diasDeRango }} días
    </p>

    <div class="filtros-container mb-4 p-3 bg-light rounded shadow-sm">
        <div id="restore-columns-order" class="mb-3 d-flex flex-wrap gap-2"></div>
    </div>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table id="orderTable" class="table table-hover modern-table">
            <thead>
                <tr>
                    <th data-column-name="Cuenta"><span>Cuenta</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Imagen"><span>Imagen</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Producto" data-sortable="true" data-column="producto"><span>Producto</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="SKU" data-sortable="true" data-column="sku"><span>SKU</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Título" data-sortable="true" data-column="titulo"><span>Título</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Ventas" data-sortable="true" data-column="ventas_diarias"><span>Ventas</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Publicación"><span>Publicación</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Stock" data-sortable="true" data-column="stock"><span>Stock</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Días de Stock" data-sortable="true" data-column="dias_stock"><span>Días de Stock</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Estado de la Orden"><span>Estado Orden</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Estado de la Publicación"><span>Estado Pub.</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Fecha de Última Venta"><span>Última Venta</span><i class="fas fa-eye toggle-visibility"></i></th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($ventas['ventas'] as $venta)
                    <tr>
                        <td data-column="Cuenta">{{ $venta['seller_nickname'] }}</td>
                        <td><img src="{{ $venta['imagen'] }}" alt="{{ $venta['titulo'] }}" class="table-img"></td>
                        <td data-column="producto">
                            <a href="{{ route('dashboard.ventaid', ['item_id' => $venta['producto'], 'fecha_inicio' => request('fecha_inicio', now()->format('Y-m-d')), 'fecha_fin' => request('fecha_fin', now()->format('Y-m-d'))]) }}" class="table-link">{{ $venta['producto'] }}</a>
                            <a href="{{ $venta['url'] }}" target="_blank" class="table-icon-link"><i class="fas fa-external-link-alt"></i></a>
                        </td>
                        <td data-column="sku">{{ $venta['sku'] }}</td>
                        <td data-column="titulo">{{ $venta['titulo'] }}</td>
                        <td data-column="ventas_diarias">{{ $venta['cantidad_vendida'] }}</td>
                        <td>{{ $venta['tipo_publicacion'] }}</td>
                        <td data-column="stock">{{ $venta['stock'] }}</td>
                        <td data-column="dias_stock">{{ $venta['dias_stock'] }}</td>
                        <td>{{ $venta['order_status'] }}</td>
                        <td>{{ $venta['estado'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($venta['fecha_ultima_venta'])->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Controles de paginación -->
    @include('layouts.pagination', [
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'limit' => $limit
    ])
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
jQuery(document).ready(function ($) {
    if ($.fn.DataTable.isDataTable('#orderTable')) {
        $('#orderTable').DataTable().destroy();
    }

    var table = $('#orderTable').DataTable({
        paging: false,
        searching: false,
        info: true,
        colReorder: true, // Habilitar arrastrar columnas
        autoWidth: false,
        responsive: true,
        scrollX: true,
        stateSave: false,
        processing: true,
        columnDefs: [
            { targets: '_all', className: 'shrink-text dt-center' },
            { targets: 4, width: '20%' } // Título
        ]
    });

    var restoreContainer = $('#restore-columns-order');

    $('th i.fas.fa-eye.toggle-visibility').on('click', function () {
        var th = $(this).closest('th');
        var columnName = th.data('column-name');
        var column = table.column(th.index());
        column.visible(!column.visible());
        table.columns.adjust().draw(false);

        if (!column.visible()) {
            addRestoreButton(th, columnName);
        }
    });

    function addRestoreButton(th, columnName) {
        var button = $(`<button class="btn btn-outline-secondary btn-sm">${columnName} <i class="fas fa-eye"></i></button>`);
        button.on('click', function () {
            table.column(th.index()).visible(true);
            table.columns.adjust().draw(false);
            $(this).remove();
        });
        restoreContainer.append(button);
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const headers = document.querySelectorAll('th[data-sortable="true"]');
    const tableBody = document.getElementById('table-body');

    headers.forEach(header => {
        header.addEventListener('click', () => {
            const column = header.getAttribute('data-column');
            const rows = Array.from(tableBody.querySelectorAll('tr'));
            const isAscending = header.classList.contains('ascending');

            rows.sort((rowA, rowB) => {
                const cellA = rowA.querySelector(`[data-column="${column}"]`).textContent.trim().toLowerCase();
                const cellB = rowB.querySelector(`[data-column="${column}"]`).textContent.trim().toLowerCase();

                if (!isNaN(parseFloat(cellA)) && !isNaN(parseFloat(cellB))) {
                    return isAscending ? parseFloat(cellA) - parseFloat(cellB) : parseFloat(cellB) - parseFloat(cellA);
                }
                return isAscending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            });

            headers.forEach(h => h.classList.remove('ascending', 'descending'));
            header.classList.add(isAscending ? 'descending' : 'ascending');
            rows.forEach(row => tableBody.appendChild(row));
        });
    });

    const searchInput = document.getElementById('searchInput');
    const estadoPublicacionFilter = document.getElementById('estadoPublicacionFilter');
    const rows = Array.from(tableBody.querySelectorAll('tr'));

    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        rows.forEach(row => {
            const productTitle = row.querySelector('[data-column="titulo"]').textContent.toLowerCase();
            const sku = row.querySelector('[data-column="sku"]').textContent.toLowerCase();
            row.style.display = (productTitle.includes(searchTerm) || sku.includes(searchTerm)) ? '' : 'none';
        });
    });

    estadoPublicacionFilter.addEventListener('change', () => {
        const selectedPublicationStatus = estadoPublicacionFilter.value;
        rows.forEach(row => {
            const publicationStatus = row.querySelector('td:nth-child(11)').textContent.trim().toLowerCase();
            row.style.display = (!selectedPublicationStatus || publicationStatus === selectedPublicationStatus) ? '' : 'none';
        });
    });
});
</script>
@endsection
