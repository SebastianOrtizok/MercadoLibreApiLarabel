@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">SKU INTERNO</h2>

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
            <form method="GET" action="{{ route('dashboard.sku') }}" class="mt-3">
                <div class="filtros-container p-3 bg-light rounded shadow-sm">
                    <div class="row">
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
        <table id="productTable" class="table table-hover modern-table">
            <thead>
                <tr>
                    <th data-column-name="Usuario"><span>Usuario</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Imagen"><span>Imagen</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Título" data-sortable="true" data-column="titulo"><span>Título</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Precio" data-sortable="true" data-column="precio"><span>Precio</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Stock Actual" data-sortable="true" data-column="stock_actual"><span>Stock Actual</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Estado"><span>Estado</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="SKU" data-sortable="true" data-column="sku"><span>SKU_Interno</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Tipo de Publicación"><span>Tipo de Pub.</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Catálogo"><span>Catálogo</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Categoría"><span>Categoría</span><i class="fas fa-eye toggle-visibility"></i></th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse ($productos as $producto)
                    <tr>
                        <td data-column="Usuario">{{ $producto->usuario ?? 'N/A' }}</td>
                        <td><img src="{{ $producto->imagen ?? asset('images/default.png') }}" alt="{{ $producto->titulo ?? 'Sin título' }}" class="table-img"></td>
                        <td data-column="titulo">
                            <a href="{{ $producto->permalink }}" target="_blank" class="table-link">{{ $producto->titulo }}</a>
                        </td>
                        <td data-column="precio">{{ $producto->precio ? number_format($producto->precio, 2) : 'N/A' }}</td>
                        <td data-column="stock_actual">{{ $producto->stock_actual ?? 'Sin stock' }}</td>
                        <td>{{ $producto->estado ?? 'Desconocido' }}</td>
                        <td data-column="sku">
                            <form action="{{ route('dashboard.sku.update-sku') }}" method="POST" class="d-flex align-items-center">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="ml_product_id" value="{{ $producto->ml_product_id }}">
                                <input type="text" name="sku_interno" value="{{ $producto->sku ?? '' }}" class="form-control form-control-sm me-2" style="width: 120px;">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i></button>
                            </form>
                        </td>
                        <td>{{ $producto->tipo_publicacion ?? 'N/A' }}</td>
                        <td>{{ $producto->catalogo ?? 'N/A' }}</td>
                        <td>{{ $producto->categoria ?? 'N/A' }}</td>
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
<script>
jQuery(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#productTable')) {
        $('#productTable').DataTable().clear().destroy();
    }
    var table = $('#productTable').DataTable({
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
            { targets: [2], width: '20%' } // Título
        ]
    });

    var restoreContainer = $('#restore-columns-productdb');

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
