@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Publicaciones</h2>

<!-- Formulario de filtros colapsado -->
<div class="mb-4">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false" aria-controls="filtrosCollapse">
            <i class="fas fa-filter"></i> <span id="toggleText">Mostrar Filtros</span>
        </button>
        <div class="collapse" id="filtrosCollapse">
            <form method="GET" action="{{ route('dashboard.publications') }}" class="mt-3">
                <div class="filtros-container p-3 bg-light rounded shadow-sm">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label>Buscar (Título/SKU)</label>
                            <input type="text" name="search" class="form-control" placeholder="Buscar por título o SKU" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>ID MercadoLibre (MLA)</label>
                            <input type="text" name="mla_id" class="form-control" placeholder="Ej: MLA123456789" value="{{ request('mla_id') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Estado de la Publicación</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Activas</option>
                                <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Pausadas</option>
                                <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>En Revisión</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Cerradas</option>
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Todas</option>
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

    <!-- Contenedor para columnas ocultas -->
    <div id="restore-columns" class="mb-3 d-flex flex-wrap gap-2"></div>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table id="publicationsTable" class="table table-hover modern-table">
            <thead>
                <tr>
                    <th data-column-name="Usuario"><span>Usuario</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Imagen"><span>Imagen</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Título" data-sortable="true" data-column="titulo"><span>Título</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Precio" data-sortable="true" data-column="precio"><span>Precio</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Condición"><span>Condición</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Stock Actual" data-sortable="true" data-column="stockActual"><span>Stock Actual</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Estado" data-sortable="true" data-column="estado"><span>Estado</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="SKU" data-sortable="true" data-column="sku"><span>SKU</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Tipo de Publicación" data-sortable="true" data-column="tipoPublicacion"><span>Tipo Pub.</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Catálogo"><span>Catálogo</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Categoría"><span>Categoría</span><i class="fas fa-eye toggle-visibility"></i></th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($publications as $item)
                    <tr>
                        <td data-column="Usuario">{{ $item['ml_account_id'] }}</td>
                        <td>
                            <img src="{{ $item['imagen'] ?? asset('images/default.png') }}" alt="{{ $item['titulo'] ?? 'Sin título' }}" class="table-img">
                        </td>
                        <td data-column="titulo">
                            {{ $item['titulo'] }}
                            <br>
                            <small class="text-muted">{{ $item['id'] }}</small>
                            <a href="{{ $item['permalink'] }}" target="_blank" class="table-icon-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </td>
                        <td data-column="precio">${{ number_format($item['precio'], 2, ',', '.') }}</td>
                        <td data-column="condicion">{{ ucfirst($item['condicion']) }}</td>
                        <td data-column="stockActual">
                            {{ $item['stockActual'] }}
                            <br>
                            <span style="color: green; font-weight: bold;">
                                {{ $item['logistic_type'] ?? 'No disponible' }}
                            </span>
                            <br>
                            @if(!empty($item['user_product_id']))
                                <a href="#" class="table-link"
                                   data-user_product_id="{{ $item['user_product_id'] }}"
                                   data-ml_account_id="{{ $item['ml_account_id'] }}"
                                   onclick="loadInventoryData(this)">
                                    {{ $item['user_product_id'] }}
                                </a>
                            @else
                                <span style="color: red; font-weight: bold;">Sin inventario</span>
                            @endif
                        </td>
                        <td data-column="estado">{{ ucfirst($item['estado']) }}</td>
                        <td data-column="sku">{{ $item['sku'] ?? 'N/A' }}</td>
                        <td data-column="tipoPublicacion">{{ $item['tipoPublicacion'] ?? 'Desconocido' }}</td>
                        <td data-column="catalogo">
                            @if($item['enCatalogo'] === true)
                                <span style="color: green; font-weight: bold;">En catálogo</span>
                            @else
                                <span style="color: red;">No</span>
                            @endif
                        </td>
                        <td data-column="categoria">
                            <form method="POST" action="{{ route('dashboard.category.items', ['categoryId' => $item['categoryid']]) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Ver Categoría</button>
                            </form>
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>

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

    <!-- Paginación -->
    @include('layouts.pagination', [
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'limit' => $limit
    ])
</div>
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


    fetch(`/dashboard/inventory?user_product_id=${userProductId}&ml_account_id=${mlAccountId}`)
        .then(response => response.json())
        .then(data => {
           // console.log("Datos recibidos:", data);

            let inventoryTable = document.getElementById("inventoryData");

            if (!data.id || !data.locations) {
                inventoryTable.innerHTML = `<tr><td colspan="3" class="text-center text-danger">Datos no disponibles</td></tr>`;
                return;
            }

            let typeMapping = {
            'selling_address': 'Depósito del Vendedor',       // Depósito de tu tienda
            'meli_facility': 'Depósito de Mercado Libre',     // Depósito de Mercado Libre
            'warehouse': 'Almacén',                           // Otro tipo de almacén
            'default': 'Depósito Predeterminado',             // Depósito predeterminado
            'distribution_center': 'Centro de Distribución',  // Centro de distribución de Mercado Libre
            // agregar más tipos aquí si es necesario
        };

        // Crear las filas de la tabla
        let tableRows = data.locations.map(loc => `
            <tr>
                <td>${typeMapping[loc.type] || loc.type || 'N/A'}</td>  <!-- Mapeo del tipo de depósito -->
                <td class="text-center">${loc.quantity || '0'}</td>  <!-- Cantidad disponible -->
                <td>${loc.availability_type ? loc.availability_type.replace('_', ' ') : 'Desconocido'}</td>  <!-- Disponibilidad -->
            </tr>
        `).join('');


            inventoryTable.innerHTML = tableRows;

            let modalElement = document.getElementById('inventoryModal');
            if (modalElement) {
                let modal = new bootstrap.Modal(modalElement);
                modal.show();
               // console.log("Modal abierto correctamente.");
            } else {
              //  console.error("No se encontró el modal en el DOM.");
            }
        })
        .catch(error => {
           // console.error("Error al obtener los datos:", error);
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
    scrollX: true,
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
