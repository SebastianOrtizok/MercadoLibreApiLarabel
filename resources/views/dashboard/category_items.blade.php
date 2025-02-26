@extends('layouts.dashboard')
@section('content')
    <div class="container mt-5">
        <h1>Productos de la Categoría: {{ $categoryId }}</h1>
        <!-- Botón para volver a la página anterior -->
        <div class="table-responsive">
            <a href="javascript:history.back()" class="btn btn-primary mb-3">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <div id="restore-columns-category" class="mb-3 d-flex flex-wrap gap-2"></div>
            <div class="filtros-container mb-4 p-3 bg-light rounded shadow-sm">
                 <div class="table-responsive">
                      <table id= "categoryTable" class="table table-striped table-sm table-bordered table-hover">
                <thead class="thead-dark sticky-top">
                    <tr>
                        <th class="text-center" data-column-name="Imagen" data-sortable="false"><i class="fas fa-eye" ></i><br>Imagen</th>
                        <th class="text-center" data-column-name="Título" data-sortable="true"><i class="fas fa-eye"></i><br>Título</th>
                        <th class="text-center" data-column-name="Precio" data-sortable="true"><i class="fas fa-eye"></i><br>Precio</th>
                        <th class="text-center" data-column-name="Disponibles" data-sortable="true"><i class="fas fa-eye"></i><br>Disponibles</th>
                        <th class="text-center" data-column-name="Tipo de Listado" data-sortable="true"><i class="fas fa-eye"></i><br>Tipo de Listado</th>
                        <th class="text-center" data-column-name="Vendedor" data-sortable="true"><i class="fas fa-eye" ></i><br>Vendedor</th>
                        <th class="text-center" data-column-name="Item Ganador" data-sortable="true"><i class="fas fa-eye" ></i><br>Item Ganador</th>
                        <th class="text-center" data-column-name="Catalog Listing" data-sortable="true"><i class="fas fa-eye" ><br></i>Catalog Listing</th>
                    </tr>
                </thead>
                <tbody id="table-body-category">
                @foreach($items as $item)
        <tr>
            <td>
                <img src="{{ $item['thumbnail'] ?? '' }}"
                     alt="{{ $item['title'] ?? 'Sin título' }}"
                     class="img-thumbnail"
                     style="width: 100px;">
            </td>
            <td>{{ $item['title'] ?? 'Sin título' }}</td>
            <td>${{ number_format($item['price'] ?? 0, 2, ',', '.') }}</td>
            <td>{{ $item['available_quantity'] ?? 'N/A' }}</td>
            <td>{{ $item['listing_type_id'] ?? 'N/A' }}</td>
            <td>{{ $item['seller']['nickname'] ?? 'N/A' }}</td>
            <td>{{ $item['winner_item_id'] ?? 'N/A' }}</td>
            <td>{{ isset($item['catalog_listing']) && $item['catalog_listing'] ? 'Sí' : 'No' }}</td>
        </tr>
    @endforeach

              </tbody>
         </table>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



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
