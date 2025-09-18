@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-primary fw-bold">Gestión de Competidores</h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Formulario para agregar competidor -->
    <div class="mb-4">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#formCollapse" aria-expanded="false" aria-controls="formCollapse">
            <i class="fas fa-plus me-2"></i> Agregar Nuevo Competidor
        </button>
        <div class="collapse mt-3" id="formCollapse">
            <div class="card shadow-sm p-4 bg-light rounded">
                <form action="{{ route('competidores.store') }}" method="POST" id="competidor-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="official_store_id">Official Store ID (opcional)</label>
                                <input type="number" name="official_store_id" id="official_store_id" class="form-control" value="{{ old('official_store_id') }}">
                            </div>
                            <label for="seller_id" class="form-label fw-semibold">Seller ID</label>
                            <input type="text" name="seller_id" id="seller_id" class="form-control" placeholder="Ej: 179571326" required readonly>
                            @error('seller_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nickname" class="form-label fw-semibold">Nickname</label>
                            <div class="input-group">
                                <input type="text" name="nickname" id="nickname" class="form-control" placeholder="Ej: TESTACCOUNT" required>
                                <button type="button" class="btn btn-outline-secondary" id="find-seller-id">
                                    <i class="fas fa-search me-2"></i> Buscar Seller ID
                                </button>
                            </div>
                            @error('nickname')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div id="seller-id-error" class="text-danger mt-1" style="display: none;"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="nombre" class="form-label fw-semibold">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Competidor de Prueba" required>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i> Guardar Competidor
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de Competidores -->
    <h3 class="mb-4 text-primary fw-bold">Lista de Competidores</h3>
    <div id="restore-columns-competidores" class="mt-3 d-flex flex-wrap gap-2"></div>
    <div class="table-responsive">
        <table id="competidoresTable" class="table table-hover modern-table shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th data-column-name="Nombre"><span>Nombre</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Seller ID"><span>Seller ID</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Nickname"><span>Nickname</span><i class="fas fa-eye toggle-visibility"></i></th>
                    <th data-column-name="Acción"><span>Acción</span><i class="fas fa-eye toggle-visibility"></i></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($competidores as $competidor)
                    <tr>
                        <td>{{ $competidor->nombre }}</td>
                        <td>{{ $competidor->seller_id }}</td>
                        <td>{{ $competidor->nickname }}</td>
                        <td>
                            <form action="{{ route('competidores.actualizar') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="competidor_id" value="{{ $competidor->id }}">
                                <select name="categoria" class="form-control form-control-sm mb-2" style="width: auto; display: inline-block;">
                                    <option value="">Seleccionar categoría a escrapear</option>
                                    <option value="vehiculos" {{ $competidor->categoria == 'vehiculos' ? 'selected' : '' }}>Vehículos</option>
                                    <option value="inmuebles" {{ $competidor->categoria == 'inmuebles' ? 'selected' : '' }}>Inmuebles</option>
                                    <option value="supermercado" {{ $competidor->categoria == 'supermercado' ? 'selected' : '' }}>Supermercado</option>
                                    <option value="tecnologia" {{ $competidor->categoria == 'tecnologia' ? 'selected' : '' }}>Tecnología</option>
                                    <option value="hogar-muebles-jardin" {{ $competidor->categoria == 'hogar-muebles-jardin' ? 'selected' : '' }}>Hogar, Muebles y Jardín</option>
                                    <option value="electrodomesticos-aires-ac" {{ $competidor->categoria == 'electrodomesticos-aires-ac' ? 'selected' : '' }}>Electrodomésticos y Aires Ac.</option>
                                    <option value="deportes-fitness" {{ $competidor->categoria == 'deportes-fitness' ? 'selected' : '' }}>Deportes y Fitness</option>
                                    <option value="belleza-cuidado-personal" {{ $competidor->categoria == 'belleza-cuidado-personal' ? 'selected' : '' }}>Belleza y Cuidado Personal</option>
                                    <option value="herramientas" {{ $competidor->categoria == 'herramientas' ? 'selected' : '' }}>Herramientas</option>
                                    <option value="construccion" {{ $competidor->categoria == 'construccion' ? 'selected' : '' }}>Construcción</option>
                                    <option value="industrias-oficinas" {{ $competidor->categoria == 'industrias-oficinas' ? 'selected' : '' }}>Industrias y Oficinas</option>
                                    <option value="accesorios-para-vehiculos" {{ $competidor->categoria == 'accesorios-para-vehiculos' ? 'selected' : '' }}>Accesorios para Vehículos</option>
                                    <option value="agro" {{ $competidor->categoria == 'agro' ? 'selected' : '' }}>Agro</option>
                                    <option value="animales-mascotas" {{ $competidor->categoria == 'animales-mascotas' ? 'selected' : '' }}>Animales y Mascotas</option>
                                    <option value="antiguedades-colecciones" {{ $competidor->categoria == 'antiguedades-colecciones' ? 'selected' : '' }}>Antigüedades y Colecciones</option>
                                    <option value="arte-libreria-merceria" {{ $competidor->categoria == 'arte-libreria-merceria' ? 'selected' : '' }}>Arte, Librería y Mercería</option>
                                    <option value="autos-motos-otros" {{ $competidor->categoria == 'autos-motos-otros' ? 'selected' : '' }}>Autos, Motos y Otros</option>
                                    <option value="bebes" {{ $competidor->categoria == 'bebes' ? 'selected' : '' }}>Bebés</option>
                                    <option value="camaras-accesorios" {{ $competidor->categoria == 'camaras-accesorios' ? 'selected' : '' }}>Cámaras y Accesorios</option>
                                    <option value="celulares-telefonos" {{ $competidor->categoria == 'celulares-telefonos' ? 'selected' : '' }}>Celulares y Teléfonos</option>
                                    <option value="coleccionables-hobbies" {{ $competidor->categoria == 'coleccionables-hobbies' ? 'selected' : '' }}>Coleccionables y Hobbies</option>
                                    <option value="consolas-videojuegos" {{ $competidor->categoria == 'consolas-videojuegos' ? 'selected' : '' }}>Consolas y Videojuegos</option>
                                    <option value="deportes-fitness" {{ $competidor->categoria == 'deportes-fitness' ? 'selected' : '' }}>Deportes y Fitness</option>
                                    <option value="electrodomesticos-aires-ac" {{ $competidor->categoria == 'electrodomesticos-aires-ac' ? 'selected' : '' }}>Electrodomésticos y Aires Ac.</option>
                                    <option value="electronica-audio-video" {{ $competidor->categoria == 'electronica-audio-video' ? 'selected' : '' }}>Electrónica, Audio y Video</option>
                                    <option value="hogar-muebles-jardin" {{ $competidor->categoria == 'hogar-muebles-jardin' ? 'selected' : '' }}>Hogar, Muebles y Jardín</option>
                                    <option value="industrias-oficinas" {{ $competidor->categoria == 'industrias-oficinas' ? 'selected' : '' }}>Industrias y Oficinas</option>
                                    <option value="inmuebles" {{ $competidor->categoria == 'inmuebles' ? 'selected' : '' }}>Inmuebles</option>
                                    <option value="instrumentos-musicales" {{ $competidor->categoria == 'instrumentos-musicales' ? 'selected' : '' }}>Instrumentos Musicales</option>
                                    <option value="joyas-relojes" {{ $competidor->categoria == 'joyas-relojes' ? 'selected' : '' }}>Joyas y Relojes</option>
                                    <option value="juegos-juguetes" {{ $competidor->categoria == 'juegos-juguetes' ? 'selected' : '' }}>Juegos y Juguetes</option>
                                    <option value="libros-revistas-comics" {{ $competidor->categoria == 'libros-revistas-comics' ? 'selected' : '' }}>Libros, Revistas y Comics</option>
                                    <option value="musica-peliculas-series">Música, Películas y Series</option>
                                    <option value="ropa-accesorios">Ropa y Accesorios</option>
                                    <option value="salud-equipamiento-medico">Salud y Equipamiento Médico</option>
                                    <option value="souvenirs-cotillon-fiestas">Souvenirs, Cotillón y Fiestas</option>
                                    <option value="otras-categorias">Otras Categorías</option>
                                </select>
                                <button type="submit" class="btn btn-outline-success btn-sm ms-2">
                                    <i class="fas fa-sync-alt me-2"></i> Actualizar
                                </button>
                            </form>
                            <form action="{{ route('competidores.destroy') }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que querés eliminar este competidor?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="competidor_id" value="{{ $competidor->id }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm ms-2">
                                    <i class="fas fa-trash-alt me-2"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="fas fa-exclamation-circle me-2"></i> No hay competidores registrados aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Tabla de Publicaciones Descargadas -->
    <h3 class="mb-4 text-primary fw-bold">Publicaciones Descargadas</h3>
    <div id="restore-columns-publicaciones" class="mt-3 d-flex flex-wrap gap-2"></div>
    <div class="mb-4 mt-5">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false" aria-controls="filtrosCollapse">
            <i class="fas fa-filter"></i> <span id="toggleText">Mostrar Filtros</span>
        </button>
        <div class="collapse" id="filtrosCollapse">
            <form method="GET" action="{{ route('competidores.articulos.index') }}" class="mt-3">
                <div class="filtros-container p-3 bg-light rounded shadow-sm">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label>Nickname</label>
                            <input type="text" name="nickname" class="form-control" placeholder="Buscar por nickname" value="{{ request('nickname') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Título</label>
                            <input type="text" name="titulo" class="form-control" placeholder="Buscar por título" value="{{ request('titulo') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Categorías</label>
                            <input type="text" name="categorias" class="form-control" placeholder="Buscar por categorías" value="{{ request('categorias') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label>Es Full</label>
                            <select name="es_full" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" {{ request('es_full') == '1' ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ request('es_full') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label>Following</label>
                            <select name="following" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" {{ request('following') == '1' ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ request('following') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label>Ordenar por</label>
                            <select name="order_by" class="form-control">
                                <option value="">Sin orden</option>
                                <option value="precio" {{ request('order_by') == 'precio' ? 'selected' : '' }}>Precio Original</option>
                                <option value="precio_descuento" {{ request('order_by') == 'precio_descuento' ? 'selected' : '' }}>Precio con Descuento</option>
                                <option value="ultima_actualizacion" {{ request('order_by') == 'ultima_actualizacion' ? 'selected' : '' }}>Última Actualización</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label>Dirección</label>
                            <select name="direction" class="form-control">
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('export.items-competidores') }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i> Exportar a Excel
            </a>
        </div>
        <form method="POST" action="{{ route('competidores.follow') }}" id="follow-form">
            @csrf
            <table id="publicacionesTable" class="table table-hover modern-table shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th data-column-name="Seguir"><span>Seguir</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Competidor"><span>Competidor</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Publicación"><span>Publicación</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Título"><span>Título</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Categorías"><span>Categorías</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Precio Original"><span>Precio Original</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Precio con Descuento"><span>Precio con Descuento</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Información de Cuotas"><span>Info. Cuotas</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Precio sin Impuestos"><span>Precio sin Imp.</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Cantidad Disponible"><span>Cant. Disponible</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Cantidad Vendida"><span>Cant. Vendida</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="URL"><span>URL</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Es Full"><span>Es Full</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Envío Gratis"><span>Envío Gratis</span><i class="fas fa-eye toggle-visibility"></i></th>
                        <th data-column-name="Última Actualización"><span>Última Actualización</span><i class="fas fa-eye toggle-visibility"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="{{ $item->following ? 'highlight-followed' : '' }}">
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>
                                <input type="hidden" name="follow[{{ $item->item_id }}]" value="no">
                                <input type="checkbox" name="follow[{{ $item->item_id }}]" value="yes" {{ $item->following ? 'checked' : '' }}>
                            </td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->competidor->nombre ?? 'N/A' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->item_id }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->titulo }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->categorias ?? '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>${{ number_format($item->precio, 2) }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>${{ $item->precio_descuento ? number_format($item->precio_descuento, 2) : '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->info_cuotas ?? '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>${{ $item->precio_sin_impuestos ? number_format($item->precio_sin_impuestos, 2) : '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->cantidad_disponible ?? '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->cantidad_vendida ?? '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif><a href="{{ $item->url }}" target="_blank">Publicación</a></td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->es_full ? 'Sí' : 'No' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->envio_gratis ? 'Sí' : 'No' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->ultima_actualizacion ? \Carbon\Carbon::parse($item->ultima_actualizacion)->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle me-2"></i> No hay publicaciones descargadas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary me-2" form="follow-form">Seguir Publicación Seleccionada</button>
            </div>
        </form>

        @include('layouts.pagination', [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'limit' => $limit
        ])
    </div>
</div>

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.4/js/dataTables.colReorder.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {
            // Debugging: Verificar si jQuery y DataTables están cargados
            console.log('jQuery version:', $.fn.jquery);
            console.log('DataTables version:', $.fn.DataTable.version);

            // Inicializar DataTable para Competidores
            if ($.fn.DataTable.isDataTable('#competidoresTable')) {
                $('#competidoresTable').DataTable().clear().destroy();
            }
            var competidoresTable = $('#competidoresTable').DataTable({
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
                    { targets: [0], width: '20%' } // Nombre
                ]
            });

            // Botones de visibilidad para Competidores
            $('#competidoresTable th i.fas.fa-eye.toggle-visibility').each(function () {
                console.log('Botón fa-eye encontrado en competidoresTable:', $(this).parent().text());
                $(this).on('click', function () {
                    console.log('Clic en fa-eye para competidoresTable');
                    var th = $(this).closest('th');
                    var columnName = th.data('column-name');
                    var column = competidoresTable.column(th);
                    console.log('Ocultando columna:', columnName);
                    column.visible(false);
                    competidoresTable.columns.adjust().draw(false);
                    addRestoreButton(th, columnName, competidoresTable, $('#restore-columns-competidores'));
                });
            });

            // Inicializar DataTable para Publicaciones
            if ($.fn.DataTable.isDataTable('#publicacionesTable')) {
                $('#publicacionesTable').DataTable().clear().destroy();
            }
            var publicacionesTable = $('#publicacionesTable').DataTable({
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
                    { targets: [3], width: '20%' }, // Título
                    { targets: [11], width: '15%' } // URL
                ]
            });

            // Botones de visibilidad para Publicaciones
            $('#publicacionesTable th i.fas.fa-eye.toggle-visibility').each(function () {
                console.log('Botón fa-eye encontrado en publicacionesTable:', $(this).parent().text());
                $(this).on('click', function () {
                    console.log('Clic en fa-eye para publicacionesTable');
                    var th = $(this).closest('th');
                    var columnName = th.data('column-name');
                    var column = publicacionesTable.column(th);
                    console.log('Ocultando columna:', columnName);
                    column.visible(false);
                    publicacionesTable.columns.adjust().draw(false);
                    addRestoreButton(th, columnName, publicacionesTable, $('#restore-columns-publicaciones'));
                });
            });

            // Función para agregar botones de restauración
            function addRestoreButton(th, columnName, table, container) {
                console.log('Agregando botón de restauración para:', columnName);
                var button = $(`<button class="btn btn-outline-secondary btn-sm">${columnName} <i class="fas fa-eye"></i></button>`);
                button.on('click', function () {
                    console.log('Restaurando columna:', columnName);
                    table.column(th).visible(true);
                    table.columns.adjust().draw(false);
                    $(this).remove();
                });
                container.append(button);
            }

            // Script para el menú de filtros
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

            // Script para buscar Seller ID
            const findSellerIdButton = document.getElementById('find-seller-id');
            if (findSellerIdButton) {
                findSellerIdButton.addEventListener('click', function() {
                    console.log('Clic en Buscar Seller ID');
                    const nicknameInput = document.getElementById('nickname');
                    const sellerIdInput = document.getElementById('seller_id');
                    const errorDiv = document.getElementById('seller-id-error');
                    const nickname = nicknameInput.value.trim();

                    if (!nickname) {
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = 'Por favor, ingresá un nickname válido.';
                        return;
                    }

                    errorDiv.style.display = 'none';
                    errorDiv.textContent = '';

                    fetch('{{ route("seller-id.find") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ nickname: nickname }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            sellerIdInput.value = data.seller_id;
                            console.log('Seller ID encontrado:', data.seller_id);
                        } else {
                            errorDiv.style.display = 'block';
                            errorDiv.textContent = data.message || 'Error al buscar el Seller ID.';
                            sellerIdInput.value = '';
                        }
                    })
                    .catch(error => {
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = 'Error al buscar el Seller ID. Por favor, intenta de nuevo.';
                        sellerIdInput.value = '';
                        console.error('Error en fetch:', error);
                    });
                });
            }
        });
    </script>
@endsection
