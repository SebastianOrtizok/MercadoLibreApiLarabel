@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Publicaciones</h2>
    <div class="filtros-container mb-4 p-3 bg-light rounded shadow-sm">
        <!-- Campo de búsqueda y filtro por estado -->
        <form method="GET" action="{{ route('dashboard.publications') }}" class="mb-0 w-100 d-flex align-items-center gap-3">
            @csrf
            <div class="input-group w-50">
                <input type="text" name="search" class="form-control" placeholder="Buscar por título..." value="{{ request('search') }}">
            </div>

            <!-- Select para el estado de las publicaciones -->
            <div class="w-25">
                <select name="status" class="form-select">
                    <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Activas</option>
                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Pausadas</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>En Revisión</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Cerradas</option>
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Todas</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
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
                        <td  class="text-center">{{ $item['stockActual'] }}
                        <br>
                        <span style="color: green; font-weight: bold;">
                            {{ $item['logistic_type'] ?? 'No disponible' }}
                        </span>
                        <br>
                        @if(!empty($item['user_product_id']))
                            <a href="#" class="spanid"
                            data-user_product_id="{{ $item['user_product_id'] }}"
                            data-ml_account_id="{{ $item['ml_account_id'] }}"
                            onclick="loadInventoryData(this)">
                                <span>{{ $item['user_product_id'] }}</span>
                            </a>
                        @else
                            <span style="color: red; font-weight: bold;">Sin inventario</span>
                        @endif

             <!-- Modal de Inventario -->
                    <div class="modal fade" id="inventoryModal" tabindex="-1" aria-labelledby="inventoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="inventoryModalLabel">Detalles del Producto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Cantidad</th>
                                                    <th>Disponibilidad</th>
                                                </tr>
                                            </thead>
                                            <tbody id="inventoryData">
                                                <tr>
                                                    <td colspan="3" class="text-center">Cargando datos...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                   <!-- Fin Modal -->

                        </td>
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
   @include('layouts.pagination', [
    'currentPage' => $currentPage,
    'totalPages' => $totalPages,
    'limit' => $limit
])



@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- script para obtener datos del modal -->

<script>
   function loadInventoryData(element) {
    let userProductId = element?.getAttribute("data-user_product_id");
    let mlAccountId = element?.getAttribute("data-ml_account_id");

    if (!userProductId || !mlAccountId) {
        console.error("Faltan atributos en el elemento.");
        return;
    }

    console.log("loadInventoryData ha sido llamada con:", { userProductId, mlAccountId });

    fetch(`/dashboard/inventory?user_product_id=${userProductId}&ml_account_id=${mlAccountId}`)
        .then(response => response.json())
        .then(data => {
            console.log("Datos recibidos:", data);

            let inventoryTable = document.getElementById("inventoryData");

            if (!data.id || !data.locations) {
                inventoryTable.innerHTML = `<tr><td colspan="3" class="text-center text-danger">Datos no disponibles</td></tr>`;
                return;
            }

            let tableRows = data.locations.map(loc => `
                <tr>
                    <td>${loc.type || 'N/A'}</td>
                    <td class="text-center">${loc.quantity || '0'}</td>
                    <td>${loc.availability_type ? loc.availability_type.replace('_', ' ') : 'Desconocido'}</td>
                </tr>
            `).join('');

            inventoryTable.innerHTML = tableRows;

            let modalElement = document.getElementById('inventoryModal');
            if (modalElement) {
                let modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log("Modal abierto correctamente.");
            } else {
                console.error("No se encontró el modal en el DOM.");
            }
        })
        .catch(error => {
            console.error("Error al obtener los datos:", error);
            document.getElementById("inventoryData").innerHTML = `<tr><td colspan="3" class="text-center text-danger">Error al cargar los datos.</td></tr>`;
        });
}

</script>


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
