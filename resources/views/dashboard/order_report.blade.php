@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Ventas</h2>

    <!-- Formulario para seleccionar el rango de días -->
    <form method="GET" action="{{ route('dashboard.ventas') }}" class="mb-4">
        <div class="form-row d-flex align-items-center">
            <!-- Columna para el campo de días -->
            <div class="col-md-3 mb-2">
                <select name="dias" id="dias" class="form-control custom-select">
                    <option value="" disabled selected>Cantidad de días</option>
                    @for($i = 1; $i <= 30; $i++)
                        <option value="{{ $i }}" {{ request('dias') == $i ? 'selected' : '' }}>
                            {{ $i }} {{ $i > 1 ? 'días' : 'día' }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Columna para el botón con el ícono de lupa alineado a la derecha -->
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> <!-- Lupa de color azul -->
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
                    <option value="">Todos los estados de la orden</option>
                    <option value="paid">Pagadas</option>
                    <option value="pending">Pendientes</option>
                    <option value="cancelled">Canceladas</option>
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
    <p class="mb-3"><strong>Rango de fechas:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }} - {{ $diasDeRango - 1 }} días</p>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Imagen</th>
                    <th data-sortable="true" data-column="producto">Producto</th>
                    <th data-sortable="true" data-column="sku">SKU</th>
                    <th data-sortable="true" data-column="titulo">Título</th>
                    <th data-sortable="true" data-column="ventas_diarias">Ventas Diarias</th>
                    <th>Publicación</th>
                    <th data-sortable="true" data-column="stock">Stock</th>
                    <th data-sortable="true" data-column="dias_stock">Días de Stock</th>
                    <th>Estado de la Orden</th>
                    <th>Estado de la Publicación</th>
                    <th>Fecha de Última Venta</th>
                </tr>
            </thead>
            <tbody id="table-body">
            @forelse($ventas['ventas'] as $venta)
                <tr>
                    <!-- Mostrar imagen del producto -->
                    <td>
                        <div class="img-container">
                            <img src="{{ $venta['imagen'] }}" alt="Imagen de {{ $venta['titulo'] }}" class="img-fluid" style="max-width: 50px;">
                        </div>
                    </td>
                    <!-- Producto -->
                    <td data-column="producto">
                        {{ $venta['producto'] }}
                        <a href="{{ $venta['url'] }}" target="_blank" class="spanid">
                             <i class="fas fa-external-link-alt" style="font-size: 14px; rgb(62, 137, 58);"></i> <!-- Ícono de FontAwesome -->
                        </a>
                    </td>
                    <!-- Mostrar SKU -->
                    <td data-column="sku">{{ $venta['sku'] }}</td>
                    <!-- Mostrar título del producto con enlace a MercadoLibre -->
                    <td data-column="titulo">
                        {{ $venta['titulo'] }}
                    </td>
                    <!-- Mostrar ventas diarias -->
                    <td data-column="ventas_diarias">{{ $venta['ventas_diarias'] }}</td>
                    <!-- Mostrar el tipo de publicación -->
                    <td>{{ $venta['tipo_publicacion'] }}</td>
                    <!-- Mostrar stock -->
                    <td data-column="stock">{{ $venta['stock'] }}</td>
                    <!-- Mostrar días de stock -->
                    <td data-column="dias_stock">{{ $venta['dias_stock'] }}</td>
                    <!-- Estado de la orden -->
                    <td>{{ $venta['order_status'] }}</td>
                    <!-- Estado de la publicación -->
                    <td>{{ $venta['estado'] }}</td>
                    <!-- Mostrar fecha de la última venta -->
                    <td>{{ \Carbon\Carbon::parse($venta['fecha_ultima_venta'])->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-danger text-center">No hay ventas para este rango de fechas.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const estadoFilter = document.getElementById('estadoFilter');
        const estadoPublicacionFilter = document.getElementById('estadoPublicacionFilter');
        const tableBody = document.getElementById('table-body');
        const rows = Array.from(tableBody.querySelectorAll('tr'));

        // Función para filtrar la tabla
        const filterTable = () => {
            const searchText = searchInput.value.toLowerCase();
            const estado = estadoFilter.value;
            const estadoPublicacion = estadoPublicacionFilter.value;

            rows.forEach(row => {
                const titulo = row.querySelector('[data-column="titulo"]').textContent.toLowerCase();
                const sku = row.querySelector('[data-column="sku"]').textContent.toLowerCase();
                const estadoRow = row.querySelector('td:nth-child(9)').textContent.toLowerCase();
                const estadoPublicacionRow = row.querySelector('td:nth-child(10)').textContent.toLowerCase();

                const matchesSearch = titulo.includes(searchText) || sku.includes(searchText);
                const matchesEstado = estado === '' || estadoRow === estado;
                const matchesEstadoPublicacion = estadoPublicacion === '' || estadoPublicacionRow === estadoPublicacion;

                if (matchesSearch && matchesEstado && matchesEstadoPublicacion) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        };

        // Event listeners para los filtros
        searchInput.addEventListener('input', filterTable);
        estadoFilter.addEventListener('change', filterTable);
        estadoPublicacionFilter.addEventListener('change', filterTable);
    });
</script>

@endsection


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const headers = document.querySelectorAll('th[data-sortable="true"]');
        const tableBody = document.getElementById('table-body');

        headers.forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-column');
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                const isAscending = header.classList.contains('ascending');

                // Ordenar las filas
                rows.sort((rowA, rowB) => {
                    const cellA = rowA.querySelector(`[data-column="${column}"]`).textContent.trim().toLowerCase();
                    const cellB = rowB.querySelector(`[data-column="${column}"]`).textContent.trim().toLowerCase();

                    if (!isNaN(parseFloat(cellA)) && !isNaN(parseFloat(cellB))) {
                        return isAscending ? parseFloat(cellA) - parseFloat(cellB) : parseFloat(cellB) - parseFloat(cellA);
                    }

                    return isAscending
                        ? cellA.localeCompare(cellB)
                        : cellB.localeCompare(cellA);
                });

                // Actualizar las clases de orden en los encabezados
                headers.forEach(h => h.classList.remove('ascending', 'descending'));
                header.classList.add(isAscending ? 'descending' : 'ascending');

                // Renderizar las filas ordenadas
                rows.forEach(row => tableBody.appendChild(row));
            });
        });
    });
</script>
