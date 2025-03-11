@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Artículos en Catálogo ({{ $totalArticulos }})</h1>

    <!-- Formulario de filtros colapsado -->
    <div class="mb-4">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false" aria-controls="filtrosCollapse">
            <i class="fas fa-filter"></i> <span id="toggleText">Mostrar Filtros</span>
        </button>
        <div class="collapse" id="filtrosCollapse">
            <form method="GET" action="{{ route('dashboard.catalogo') }}" class="mt-3">
                <div class="filtros-container p-3 bg-light rounded shadow-sm">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label>Buscar (Título/SKU)</label>
                            <input type="text" name="search" class="form-control" placeholder="Buscar por título o SKU" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Cuenta ML</label>
                            <select name="cuenta_ml" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($cuentas_ml as $cuenta)
                                    <option value="{{ $cuenta }}" {{ request('cuenta_ml') == $cuenta ? 'selected' : '' }}>{{ $cuenta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Contenedor para columnas ocultas -->
    <div id="restore-columns" class="mb-3 d-flex flex-wrap gap-2"></div>

    @if($totalArticulos > 0)
        <!-- Tabla de resultados -->
        <div class="table-responsive">
            <table id="catalogTable" class="table table-hover modern-table">
                <thead>
                    <tr>
                        <th data-column-name="ID Producto"><span>ID Producto</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Título" data-sortable="true" data-column="titulo"><span>Título</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Precio" data-sortable="true" data-column="precio"><span>Precio</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Stock" data-sortable="true" data-column="stock_actual"><span>Stock</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Tipo Publicación"><span>Tipo Publicación</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Cuenta ML"><span>Cuenta ML</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Enlace"><span>Enlace</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Imagen"><span>Imagen</span><i class="fas fa-eye toggle-visibility"></i></th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($articulos as $articulo)
                        <tr>
                            <td data-column="ID Producto">
                                <a href="{{ route('dashboard.catalogo.competencia', $articulo->ml_product_id) }}" target="_blank" class="table-icon-link">
                                    {{ $articulo->ml_product_id }} <i class="fas fa-chart-line"></i>
                                </a>
                            </td>
                            <td data-column="titulo">{{ $articulo->titulo }}</td>
                            <td data-column="precio">${{ number_format($articulo->precio, 2, ',', '.') }}</td>
                            <td data-column="stock_actual">{{ $articulo->stock_actual }}</td>
                            <td data-column="Tipo Publicación">{{ $articulo->tipo_publicacion ?? 'N/A' }}</td>
                            <td data-column="Cuenta ML">{{ $articulo->cuenta_ml ?? 'Sin cuenta' }}</td>
                            <td data-column="Enlace">
                                <a href="{{ $articulo->permalink }}" target="_blank" class="table-icon-link">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </td>
                            <td data-column="Imagen">
                                <img src="{{ $articulo->imagen ?? asset('images/default.png') }}"
                                     alt="{{ $articulo->titulo }}"
                                     class="table-img">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @include('layouts.pagination', [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'limit' => $limit
        ])
    @else
        <div class="alert alert-info">
            No hay artículos activos en catálogo para tus cuentas de Mercado Libre.
        </div>
    @endif
</div>
@endsection

<!-- Scripts -->
@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
jQuery(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#catalogTable')) {
        $('#catalogTable').DataTable().clear().destroy();
    }

    var table = $('#catalogTable').DataTable({
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
            { targets: 1, width: '20%' } // Título
        ]
    });

    $('th i.fas.fa-eye.toggle-visibility').click(function () {
        var th = $(this).closest('th');
        var columnName = th.data('column-name');
        var column = table.column(th.index());
        column.visible(false);
        table.columns.adjust().draw(false);
        addRestoreButton(th, columnName);
    });

    function addRestoreButton(th, columnName) {
        var button = $(`<button class="btn btn-outline-secondary btn-sm">${columnName} <i class="fas fa-eye"></i></button>`);
        button.on('click', function () {
            table.column(th.index()).visible(true);
            table.columns.adjust().draw(false);
            $(this).remove();
        });
        $('#restore-columns').append(button);
    }
});

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
