@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Ventas Consolidadas</h2>

    <!-- Formulario para seleccionar el rango de días -->
    <form method="GET" action="{{ route('dashboard.ventasconsolidadas') }}" class="mb-4">
    <div class="form-row d-flex align-items-center gap-3">
        <!-- Columna para el campo de fecha de inicio -->
        <div class="col-md-3 mb-2">
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio', now()->format('Y-m-d')) }}">
        </div>

        <!-- Columna para el campo de fecha de fin -->
        <div class="col-md-3 mb-2">
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin', now()->format('Y-m-d')) }}">
        </div>

        <!-- Columna para el botón de búsqueda -->
        <div class="col-md-2 mb-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search"></i> <!-- Ícono de búsqueda -->
            </button>
        </div>
    </div>

    </form>


    <!-- Filtros y buscador en el frontend -->
    <div class="filtros-container mb-4 p-3 bg-light rounded shadow-sm">
        <div class="row">
            <!-- Buscador por título o SKU -->
            <div class="col-md-4 mb-2">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar por título o SKU">
            </div>

            <!-- Filtro por estado de la orden -->
            <div class="col-md-4 mb-2">
                <select id="estadoFilter" class="form-control">
                    <option value="paid">Pagadas</option>
                </select>
            </div>

            <!-- Filtro por estado de la publicación -->
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
    <table id="orderTableold" class="table table-striped table-bordered table-hover">
    <thead class="thead-dark sticky-top">
        <tr>
            <th class="text-center" data-column-name="Cuenta"><i class="fas fa-eye" ></i><br>Cuenta</th>
            <th class="text-center" data-column-name="Imagen"><i class="fas fa-eye" ></i><br>Imagen</th>
            <th class="text-center" data-column-name="Producto" data-sortable="true" data-column="producto"><i class="fas fa-eye" ></i><br>Producto</th>
            <th class="text-center" data-column-name="SKU" data-sortable="true" data-column="sku"><i class="fas fa-eye" ></i><br>SKU</th>
            <th class="text-center" data-column-name="Título" data-sortable="true" data-column="titulo"><i class="fas fa-eye" ></i><br>Título</th>
            <th class="text-center" data-column-name="Ventas" data-sortable="true" data-column="ventas_diarias"><i class="fas fa-eye" ></i><br>Ventas</th>
            <th class="text-center" data-column-name="Publicación"><i class="fas fa-eye" ></i><br>Publicación</th>
            <th class="text-center" data-column-name="Stock" data-sortable="true" data-column="stock"><i class="fas fa-eye" ></i><br>Stock</th>
            <th class="text-center" data-column-name="ImDías de Stock" data-sortable="true" data-column="dias_stock"><i class="fas fa-eye" ></i><br>Días de Stock</th>
            <th class="text-center" data-column-name="Estado de la Orden"><i class="fas fa-eye" ></i><br>Estado de la Orden</th>
            <th class="text-center" data-column-name="Estado de la Publicación"><i class="fas fa-eye" ></i><br>Estado de la Publicación</th>
            <th class="text-center" data-column-name="Fecha de Última Venta"><i class="fas fa-eye" ></i><br>Fecha de Última Venta</th>
        </tr>
    </thead>
    <tbody id="table-body">
        @forelse ($ventas as $venta)
            <tr style="background-color: {{ $venta['seller_nickname'] === 'TRTEK/TROTA' ? '#d3f9d8' : 'transparent' }};">
                <td>
                    <div data-column="Cuenta">
                        {{ $venta['seller_nickname'] }}
                    </div>
                </td>
                <!-- Mostrar imagen del producto -->
                <td>
                    <img src="{{ $venta['imagen'] ?? asset('images/default.png') }}"
                        alt="Imagen de {{ $venta['titulo'] ?? 'Sin título' }}"
                        class="img-fluid" style="max-width: 50px;">
                </td>

                <!-- Producto -->
                <td data-column="producto">
                    <a href="{{ route('dashboard.ventaid', ['item_id' => $venta['producto'], 'fecha_inicio' => request('fecha_inicio', now()->format('Y-m-d')), 'fecha_fin' => request('fecha_fin', now()->format('Y-m-d'))]) }}">
                        {{ $venta['producto'] }}
                    </a>
                    <br><br>
                    <a href="{{ $venta['url'] }}" target="_blank" class="spanid">
                        <i class="fas fa-external-link-alt" style="font-size: 14px; color:rgb(62, 137, 58);"></i>
                    </a>
                </td>
                <!-- Mostrar SKU -->
                <td data-column="sku">{{ $venta['sku'] ?? 'N/A' }}</td>
                <!-- Mostrar título del producto con enlace a MercadoLibre -->
                <td data-column="titulo">
                    {{ $venta['titulo'] }}
                </td>
                <!-- Mostrar ventas diarias -->
                <td data-column="ventas_diarias">{{ $venta['cantidad_vendida'] }}</td>
                <!-- Mostrar el tipo de publicación -->
                <td>{{ $venta['tipo_publicacion'] }}</td>
                <!-- Mostrar stock -->
                <td data-column="stock">{{ $venta['stock'] ?? 'Sin stock' }}</td>
                <!-- Mostrar días de stock -->
                <td data-column="dias_stock">{{ $venta['dias_stock'] }}</td>
                <!-- Estado de la orden -->
                <td>{{ $venta['order_status'] }}</td>
                <!-- Estado de la publicación -->
                <td>{{ $venta['estado'] ?? 'Desconocido' }}</td>
                <!-- Mostrar fecha de la última venta -->
                <td>{{ \Carbon\Carbon::parse($venta['fecha_ultima_venta'])->format('d/m/Y H:i') }}</td>
            </tr>
        @empty

        @endforelse
    </tbody>
</table>

    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<!-- Controles de paginación -->
<!-- Paginación -->
@if ($totalPages > 1)
    <nav>
        <ul class="pagination">
            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link"
                        href="{{ route('dashboard.ventasconsolidadas', [
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




@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- script para ordenar las columnas y ocultar -->
<script>
jQuery(document).ready(function () {

    if ($.fn.DataTable.isDataTable('#orderTable')) {
    $('#orderTable').DataTable().clear().destroy();
}
    var table = $('#orderTable').DataTable({
        paging: false,
   // transform: scale(0.95);
    searching: false,
    info: true,
    colReorder: true,
    autoWidth: false,
    responsive: true,
    scrollX: false,
    stateSave: false,
    ordering: false,
    processing: true,
    width: '95%',   // Forzar que la tabla ocupe el 100% del ancho
    columnDefs: [
        { targets: '_all', className: 'shrink-text dt-center' },  // Aplica 'shrink-text' a todas las columnas
        { targets: [4], width: '20%' }  // Aumenta el ancho de la columna 5 (índice 4) a un 20%plica la clase 'titulo-columna' solo a la primera columna
    ]
    });

    var restoreContainer = $('#restore-columns-order');

    // Ocultar columna al hacer clic en el ícono del ojo
    $('th i.fas.fa-eye').click(function () {
        var th = $(this).closest('th');
        var columnName = th.data('column-name');

        // Obtener la columna usando el nodo th directamente
        var column = table.column(th);

        console.log(`Ocultando columna: ${columnName}`);

        // Ocultar la columna
        column.visible(false);
        table.columns.adjust().draw(false);

        // Agregar botón para restaurar la columna
        addRestoreButton(th, columnName);
    });

    // Función para agregar el botón de restauración
    function addRestoreButton(th, columnName) {
        var button = $(`<button class="btn btn-outline-secondary btn-sm">${columnName} <i class="fas fa-eye"></i></button>`);
        button.on('click', function () {
            console.log(`Restaurando columna: ${columnName}`);

            // Restaurar la columna usando el mismo th
            table.column(th).visible(true);
            table.columns.adjust().draw(false);

            // Remover el botón de restauración
            $(this).remove();
        });
        restoreContainer.append(button);
    }
});

</script>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const estadoPublicacionFilter = document.getElementById('estadoPublicacionFilter');
        const tableBody = document.getElementById('table-body');
        const rows = Array.from(tableBody.querySelectorAll('tr'));

        // Filtro de búsqueda por SKU o título
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

        // Filtro de estado de la publicación
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
