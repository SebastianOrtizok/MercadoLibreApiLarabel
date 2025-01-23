@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Publicaciones</h2>

    <!-- Tabla con clases de Bootstrap para un mejor diseño -->
    <table class="table table-striped table-bordered table-hover">
    <thead class="thead-dark">
        <tr>
            <th data-sortable="false">Imagen</th>
            <th data-sortable="true" data-column="titulo">Título</th>
            <th data-sortable="true" data-column="precio">Precio</th>
            <th data-sortable="true" data-column="condicion">Condición</th>
            <th data-sortable="true" data-column="stockActual">Stock Actual</th>
            <th data-sortable="true" data-column="estado">Estado</th>
            <th data-sortable="true" data-column="sku">SKU</th>
            <th data-sortable="true" data-column="tipoPublicacion">Tipo de Publicación</th>
            <th data-sortable="false">Catálogo</th>
            <th data-sortable="false">Categoría</th>
        </tr>
    </thead>
    <tbody id="table-body">
        @forelse($publications as $item)
            <tr>
                <td>
                    <div class="img-container">
                        @if(isset($item['imagen']) && $item['imagen'])
                            <img src="{{ $item['imagen'] }}" alt="Imagen del producto" class="img-fluid">
                        @else
                            <span>No disponible</span>
                        @endif
                    </div>
                </td>
                <td data-column="titulo">{{ $item['titulo'] }}<br>
                <span class="spanid">{{ $item['id'] }}</span><br>
                <a href="{{ $item['permalink'] }}" target="_blank" class="spanid">Ver publicación</a>
                </td>
                <td data-column="precio">${{ number_format($item['precio'], 2, ',', '.') }}</td>
                <td data-column="condicion">{{ ucfirst($item['condicion']) }}</td>
                <td data-column="stockActual">{{ $item['stockActual'] }}</td>
                <td data-column="estado">{{ ucfirst($item['estado']) }}</td>
                <td data-column="sku">{{ $item['sku'] ?? 'No disponible' }}</td>
                <td data-column="tipoPublicacion">{{ $item['tipoPublicacion'] ?? 'Desconocido' }}</td>
                <td>
                    @if($item['enCatalogo'] === true)
                        <span style="color: green; font-weight: bold;">En catálogo</span>
                    @else
                        <span style="color: red;">No</span>
                    @endif
                </td>
                <td>
                    <form method="POST" action="{{ route('dashboard.category.items', ['categoryId' => $item['categoryid']]) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Ver Categoría</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">No se encontraron publicaciones.</td>
            </tr>
        @endforelse
    </tbody>
</table>


    <!-- Controles de paginación -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                <a class="page-link" href="?page={{ $currentPage - 1 }}&limit={{ $limit }}" aria-label="Anterior">&laquo;</a>
            </li>

            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="?page={{ $i }}&limit={{ $limit }}">{{ $i }}</a>
                </li>
            @endfor

            <li class="page-item {{ $currentPage == $totalPages ? 'disabled' : '' }}">
                <a class="page-link" href="?page={{ $currentPage + 1 }}&limit={{ $limit }}" aria-label="Siguiente">&raquo;</a>
            </li>
        </ul>
    </nav>
</div>
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
            const cellA = rowA.querySelector(`[data-column="${column}"]`).textContent.trim() || '';
            const cellB = rowB.querySelector(`[data-column="${column}"]`).textContent.trim() || '';

            if (!isNaN(cellA) && !isNaN(cellB)) {
                return isAscending ? cellA - cellB : cellB - cellA;
            }

            return isAscending
                ? cellA.localeCompare(cellB)
                : cellB.localeCompare(cellA);
        });


            // Actualizar clases para orden ascendente/descendente
            headers.forEach(h => h.classList.remove('ascending', 'descending'));
            header.classList.add(isAscending ? 'descending' : 'ascending');

            // Renderizar las filas ordenadas
            rows.forEach(row => tableBody.appendChild(row));
        });
    });
});
</script>
