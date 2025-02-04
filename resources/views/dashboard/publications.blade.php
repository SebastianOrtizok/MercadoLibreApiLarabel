@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Publicaciones</h2>
    <div class="filtros-container mb-4 p-3 bg-light rounded shadow-sm">
    <!-- Campo de búsqueda -->
    <form method="POST" action="{{ route('dashboard.publications') }}" class="mb-0 w-50">
             @csrf
            <div class="input-group">
                <input type="text" name="search" class="me-3 form-control" placeholder="Buscar por título..." value="{{ request('search') }}">
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> <!-- Lupa de color azul -->
                    </button>
                </div>
            </div>
        </form>
    </div>
 <!-- Contenedor de botones para mostrar columnas ocultas -->
 <div id="restore-columns" class="mb-3 d-flex flex-wrap gap-2"></div>

 <div class="filtros-container mb-4 p-3 bg-light rounded shadow-sm">
    <div class="table-responsive">
        <table id="publicationsTable" class="table table-sm table-bordered table-hover">
            <thead class="thead-dark sticky-top">
                <tr>
                    <th class="text-center" data-column-name="Usuario" data-sortable="false"><i class="fas fa-eye" id="toggleUsuario"></i><br> Usuario</th>
                    <th class="text-center" data-column-name="Imagen" data-sortable="false"><i class="fas fa-eye" id="toggleImagen"></i><br> Imagen</th>
                    <th class="text-center" data-column-name="Título" data-sortable="true"><i class="fas fa-eye" id="toggleTitulo"></i><br> Título</th>
                    <th class="text-center" data-column-name="Precio" data-sortable="true"><i class="fas fa-eye" id="togglePrecio"></i><br> Precio</th>
                    <th class="text-center" data-column-name="Condición" data-sortable="false"><i class="fas fa-eye" id="toggleCondicion"></i><br> Condición</th>
                    <th class="text-center" data-column-name="Stock Actual" data-sortable="true"><i class="fas fa-eye" id="toggleStockActual"></i><br> Stock Actual</th>
                    <th class="text-center" data-column-name="Estado" data-sortable="true"><i class="fas fa-eye" id="toggleEstado"></i><br> Estado</th>
                    <th class="text-center" data-column-name="SKU" data-sortable="true"><i class="fas fa-eye" id="toggleSku"></i><br> SKU</th>
                    <th class="text-center" data-column-name="Tipo de Publicación" data-sortable="true"><i class="fas fa-eye" id="toggleTipoPublicacion"></i><br> Tipo de Publicación</th>
                    <th class="text-center" data-column-name="Catálogo" data-sortable="true"><i class="fas fa-eye" id="toggleCatalogo"></i><br> Catálogo</th>
                    <th class="text-center" data-column-name="Categoría" data-sortable="false"><i class="fas fa-eye" id="toggleCategoria"></i><br> Categoría</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($publications as $item)
                    <tr>
                        <td>{{ $item['ml_account_id'] }}</td>
                        <td>
                            <div class="img-container">
                                @if(isset($item['imagen']) && $item['imagen'])
                                    <img class="text-center" src="{{ $item['imagen'] }}" alt="Imagen del producto" class="img-fluid">
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $item['titulo'] }}<br>
                            <span class="spanid">{{ $item['id'] }}</span>
                            <a href="{{ $item['permalink'] }}" target="_blank" class="spanid">
                                <i class="fas fa-external-link-alt" style="font-size: 14px; color:rgb(62, 137, 58);"></i>
                            </a>
                        </td>
                        <td>${{ number_format($item['precio'], 2, ',', '.') }}</td>
                        <td class="text-center">{{ ucfirst($item['condicion']) }}</td>
                        <td  class="text-center">{{ $item['stockActual'] }}</td>
                        <td class="text-center">{{ ucfirst($item['estado']) }}</td>
                        <td>{{ $item['sku'] ?? 'No disponible' }}</td>
                        <td class="text-center">{{ $item['tipoPublicacion'] ?? 'Desconocido' }}</td>
                        <td class="text-center">
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
                        <td colspan="11" class="text-center">No se encontraron publicaciones.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>



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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- script para ordenar las columnas y ocultar -->
<script>
jQuery(document).ready(function () {
    var table = $('#publicationsTable').DataTable({
    paging: false,
   // transform: scale(0.95);
    searching: false,
    info: true,
    colReorder: true,
    autoWidth: false,
    responsive: true,
    scrollX: false,
    stateSave: true,
    processing: true,
    width: '95%',   // Forzar que la tabla ocupe el 100% del ancho
    columnDefs: [
        { targets: '_all', className: 'shrink-text dt-center' }  // Aplica 'shrink-text' a todas las columnas
    ]
    });

    var restoreContainer = $('#restore-columns');

    // Ocultar columna al hacer clic en el ícono del ojo
    $('th i.fas.fa-eye').on('click', function (){
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
    const headers = document.querySelectorAll('th[data-sortable="true"]');
    const tableBody = document.getElementById('table-body');

    headers.forEach(header => {
        header.addEventListener('click', () => {
            const column = header.getAttribute('data-column');
            const rows = Array.from(tableBody.querySelectorAll('tr'));
            const isAscending = header.classList.contains('ascending');

            // Ordenar las filas
            rows.sort((rowA, rowB) => {
                const cellA = rowA.querySelector(`[data-column="${column}"]`);
                const cellB = rowB.querySelector(`[data-column="${column}"]`);

                // Verificar si las celdas están visibles
                if (cellA && cellB && cellA.offsetParent !== null && cellB.offsetParent !== null) {
                    const textA = cellA.textContent.trim() || '';
                    const textB = cellB.textContent.trim() || '';

                    if (!isNaN(textA) && !isNaN(textB)) {
                        return isAscending ? textA - textB : textB - textA;
                    }

                    return isAscending
                        ? textA.localeCompare(textB)
                        : textB.localeCompare(textA);
                }

                return 0; // Si alguna celda no es visible, no hacemos nada
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
