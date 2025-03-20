@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Artículos</h2>

    <!-- Formulario de filtros colapsado -->
    <div class="mb-4">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false" aria-controls="filtrosCollapse">
            <i class="fas fa-filter"></i> <span id="toggleText">Mostrar Filtros</span>
        </button>
        <div class="collapse" id="filtrosCollapse">
            <form method="GET" action="{{ route('dashboard.listado_articulos') }}" class="mt-3">
                <div class="filtros-container p-3 bg-light rounded shadow-sm">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label>Buscar (Título/SKU)</label>
                            <input type="text" name="search" class="form-control" placeholder="Buscar por título o SKU" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="active" {{ request('estado') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="paused" {{ request('estado') == 'paused' ? 'selected' : '' }}>Pausado</option>
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

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table id="articulosTable" class="table table-hover modern-table">
            <thead>
                <tr>
                    <th data-column-name="ID"><span>ID</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Imagen"><span>Imagen</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Título"><span>Título</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Precio"><span>Precio</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Stock"><span>Stock</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Estado"><span>Estado</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="SKU"><span>SKU Interno</span><i class="fas fa-eye toggle-visibility"></i></th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse ($articulos as $articulo)
                    <tr>
                        <td>{{ $articulo->id }}</td>
                        <td><img src="{{ $articulo->imagen ?? asset('images/default.png') }}" alt="{{ $articulo->titulo }}" class="table-img"></td>
                        <td><a href="{{ $articulo->permalink }}" target="_blank" class="table-link">{{ $articulo->titulo }}</a></td>
                        <td>{{ number_format($articulo->precio, 2) }}</td>
                        <td>{{ $articulo->stock_actual }}</td>
                        <td>{{ $articulo->estado }}</td>
                        <td>{{ $articulo->sku_interno ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No hay artículos para mostrar</td></tr>
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
<script>
jQuery(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#articulosTable')) {
        $('#articulosTable').DataTable().clear().destroy();
    }
    $('#articulosTable').DataTable({
        paging: false,
        searching: false,
        info: true,
        autoWidth: false,
        responsive: true,
        scrollX: true,
    });
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
