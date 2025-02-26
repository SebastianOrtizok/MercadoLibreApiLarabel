@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Ventas Consolidadas DB</h2>

    <!-- Formulario para seleccionar el rango de días -->
    <form method="GET" action="{{ route('dashboard.ventasconsolidadasdb') }}" class="mb-4">
        <div class="form-row d-flex align-items-center gap-3">
            <div class="col-md-3 mb-2">
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio', now()->subDays(30)->format('Y-m-d')) }}">
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
        <a href="{{ route('exportar.ventas') }}" class="btn btn-success mb-3">
            <i class="fas fa-file-excel"></i> Exportar a Excel
        </a>

        <!-- Tabla de resultados -->
        <div class="table-responsive">
            <table id="orderTable" class="table table-striped table-bordered table-hover">
                <thead class="thead-dark sticky-top">
                    <tr>
                        <th class="text-center" data-column-name="Cuenta"><i class="fas fa-eye"></i><br>Cuenta</th>
                        <th class="text-center" data-column-name="Imagen"><i class="fas fa-eye"></i><br>Imagen</th>
                        <th class="text-center" data-column-name="Producto" data-sortable="true" data-column="producto"><i class="fas fa-eye"></i><br>Producto</th>
                        <th class="text-center" data-column-name="SKU" data-sortable="true" data-column="sku"><i class="fas fa-eye"></i><br>SKU</th>
                        <th class="text-center" data-column-name="Título" data-sortable="true" data-column="titulo"><i class="fas fa-eye"></i><br>Título</th>
                        <th class="text-center" data-column-name="Ventas" data-sortable="true" data-column="ventas_diarias"><i class="fas fa-eye"></i><br>Ventas</th>
                        <th class="text-center" data-column-name="Publicación"><i class="fas fa-eye"></i><br>Publicación</th>
                        <th class="text-center" data-column-name="Stock" data-sortable="true" data-column="stock"><i class="fas fa-eye"></i><br>Stock</th>
                        <th class="text-center" data-column-name="Días de Stock" data-sortable="true" data-column="dias_stock"><i class="fas fa-eye"></i><br>Días de Stock</th>
                        <th class="text-center" data-column-name="Estado de la Orden"><i class="fas fa-eye"></i><br>Estado de la Orden</th>
                        <th class="text-center" data-column-name="Estado de la Publicación"><i class="fas fa-eye"></i><br>Estado de la Publicación</th>
                        <th class="text-center" data-column-name="Fecha de Última Venta"><i class="fas fa-eye"></i><br>Fecha de Última Venta</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($ventas as $venta)
                        <tr>
                            <td>
                                <div data-column="Cuenta">
                                    {{ $venta['ml_account_id'] ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <img src="{{ $venta['imagen'] ?? asset('images/default.png') }}"
                                    alt="Imagen de {{ $venta['titulo'] ?? 'Sin título' }}"
                                    class="img-fluid" style="max-width: 50px;">
                            </td>
                            <td data-column="producto">
                                <a href="{{ route('dashboard.ventaid', ['item_id' => $venta['producto'], 'fecha_inicio' => request('fecha_inicio', now()->format('Y-m-d')), 'fecha_fin' => request('fecha_fin', now()->format('Y-m-d'))]) }}">
                                    {{ $venta['producto'] }}
                                </a>
                                <br><br>
                                <a href="{{ $venta['url'] }}" target="_blank" class="spanid">
                                    <i class="fas fa-external-link-alt" style="font-size: 14px; color:rgb(62, 137, 58);"></i>
                                </a>
                            </td>
                            <td data-column="sku">{{ $venta['sku'] ?? 'N/A' }}</td>
                            <td data-column="titulo">{{ $venta['titulo'] }}</td>
                            <td data-column="ventas_diarias">{{ $venta['cantidad_vendida'] }}</td>
                            <td>{{ $venta['tipo_publicacion'] }}</td>
                            <td data-column="stock">{{ $venta['stock'] ?? 'Sin stock' }}</td>
                            <td data-column="dias_stock">{{ $venta['dias_stock'] ?? 'N/A' }}</td>
                            <td>{{ $venta['order_status'] }}</td>
                            <td>{{ $venta['estado'] ?? 'Desconocido' }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta['fecha_ultima_venta'])->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">No hay datos disponibles</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if ($totalPages > 1)
        <nav>
            <ul class="pagination">
                @for ($i = 1; $i <= $totalPages; $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link"
                            href="{{ route('dashboard.ventasconsolidadasdb', [
                                'fecha_inicio' => request('fecha_inicio'),
                                'fecha_fin' => request('fecha_fin'),
                                'page' => $i
                            ]) }}">
                            {{ $i }}
                        </a>
                    </li>
                @endfor
            </ul>
        </nav>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        jQuery(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#orderTable')) {
                $('#orderTable').DataTable().clear().destroy();
            }
            var table = $('#orderTable').DataTable({
                paging: false,
                searching: false,
                info: true,
                colReorder: true,
                autoWidth: false,
                responsive: true,
                scrollX: false,
                stateSave: false,
                ordering: false,
                processing: true,
                width: '95%',
                columnDefs: [
                    { targets: '_all', className: 'shrink-text dt-center' },
                    { targets: [4], width: '20%' }
                ]
            });

            var restoreContainer = $('#restore-columns-order');
            $('th i.fas.fa-eye').click(function () {
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

        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const estadoPublicacionFilter = document.getElementById('estadoPublicacionFilter');
            const tableBody = document.getElementById('table-body');
            const rows = Array.from(tableBody.querySelectorAll('tr'));

            searchInput.addEventListener('input', () => {
                const searchTerm = searchInput.value.toLowerCase();
                rows.forEach(row => {
                    const productTitle = row.querySelector('[data-column="titulo"]').textContent.toLowerCase();
                    const sku = row.querySelector('[data-column="sku"]').textContent.toLowerCase();
                    if (productTitle.includes(searchTerm) || sku.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            estadoPublicacionFilter.addEventListener('change', () => {
                const selectedPublicationStatus = estadoPublicacionFilter.value;
                rows.forEach(row => {
                    const publicationStatus = row.querySelector('td:nth-child(11)').textContent.trim().toLowerCase();
                    if (!selectedPublicationStatus || publicationStatus === selectedPublicationStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
