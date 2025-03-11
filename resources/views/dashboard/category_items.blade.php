@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Productos de la Categoría: {{ $categoryId }}</h1>

    <!-- Botón para volver -->
    <div class="mb-4">
        <a href="javascript:history.back()" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Espacio para filtros colapsables (opcional, por si querés agregar después) -->
    
    <!-- Contenedor para columnas ocultas -->
    <div id="restore-columns-category" class="mb-3 d-flex flex-wrap gap-2"></div>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table id="categoryTable" class="table table-hover modern-table">
            <thead>
                <tr>
                    <th data-column-name="Imagen"><span>Imagen</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Título" data-sortable="true" data-column="title"><span>Título</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Precio" data-sortable="true" data-column="price"><span>Precio</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Disponibles" data-sortable="true" data-column="available_quantity"><span>Disponibles</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Tipo de Listado" data-sortable="true" data-column="listing_type_id"><span>Tipo de Listado</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Vendedor" data-sortable="true" data-column="seller"><span>Vendedor</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Item Ganador" data-sortable="true" data-column="winner_item_id"><span>Item Ganador</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Catalog Listing" data-sortable="true" data-column="catalog_listing"><span>Catalog Listing</span><i class="fas fa-eye toggle-visibility"></i></th>
                </tr>
            </thead>
            <tbody id="table-body-category">
                @forelse($items as $item)
                    <tr>
                        <td>
                            <img src="{{ $item['thumbnail'] ?? asset('images/default.png') }}"
                                 alt="{{ $item['title'] ?? 'Sin título' }}"
                                 class="table-img">
                        </td>
                        <td data-column="title">{{ $item['title'] ?? 'Sin título' }}</td>
                        <td data-column="price">${{ number_format($item['price'] ?? 0, 2, ',', '.') }}</td>
                        <td data-column="available_quantity">{{ $item['available_quantity'] ?? 'N/A' }}</td>
                        <td data-column="listing_type_id">{{ $item['listing_type_id'] ?? 'N/A' }}</td>
                        <td data-column="seller">{{ $item['seller']['nickname'] ?? 'N/A' }}</td>
                        <td data-column="winner_item_id">{{ $item['winner_item_id'] ?? 'N/A' }}</td>
                        <td data-column="catalog_listing">
                            {{ isset($item['catalog_listing']) && $item['catalog_listing'] ? 'Sí' : 'No' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No hay productos para esta categoría.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.querySelectorAll('.toggle-visibility').forEach(icon => {
        icon.addEventListener('click', function() {
            const columnName = this.parentElement.getAttribute('data-column-name');
            const cells = document.querySelectorAll(`td[data-column="${columnName.toLowerCase()}"], th[data-column-name="${columnName}"]`);
            cells.forEach(cell => {
                cell.style.display = cell.style.display === 'none' ? '' : 'none';
            });
            const restoreBtn = document.createElement('button');
            restoreBtn.className = 'btn btn-sm btn-outline-secondary';
            restoreBtn.textContent = `Mostrar ${columnName}`;
            restoreBtn.onclick = () => {
                cells.forEach(cell => cell.style.display = '');
                restoreBtn.remove();
            };
            document.getElementById('restore-columns-category').appendChild(restoreBtn);
        });
    });
</script>
@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- script para ordenar las columnas y ocultar -->
<script>
jQuery(document).ready(function () {
    var table = $('#categoryTable').DataTable({
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

    var restoreContainer = $('#restore-columns-category');

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
    const headers = document.querySelectorAll('th[data-sortable="true"]');
    const tableBody = document.getElementById('table-body-category');

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
