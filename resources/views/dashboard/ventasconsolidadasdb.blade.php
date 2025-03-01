@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Ventas Consolidadas DB</h2>

<!-- Formulario de filtros colapsado -->
<div class="mb-4">
    <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false" aria-controls="filtrosCollapse">
        <i class="fas fa-filter"></i> <span id="toggleText">Mostrar Filtros</span>
    </button>
    <div class="collapse" id="filtrosCollapse">
        <form method="GET" action="{{ route('dashboard.ventasconsolidadasdb') }}" class="mt-3">
            <div class="filtros-container p-3 bg-light rounded shadow-sm">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio', now()->subDays(30)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin', now()->format('Y-m-d')) }}">
                    </div>
                        <div class="col-md-3 mb-2">
                        <label>Buscar (SKU/Título)</label>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por SKU o título" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Cuenta</label>
                        <select name="ml_account_id" class="form-control">
                            <option value="">Todas las cuentas</option>
                            @foreach (\DB::table('mercadolibre_tokens')->where('user_id', auth()->id())->pluck('ml_account_id')->unique() as $account)
                                <option value="{{ $account }}" {{ request('ml_account_id') == $account ? 'selected' : '' }}>{{ $account }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Tipo de Publicación</label>
                        <select name="tipo_publicacion" class="form-control">
                            <option value="">Todos los tipos</option>
                            <option value="classic" {{ request('tipo_publicacion') == 'classic' ? 'selected' : '' }}>Clásica</option>
                            <option value="premium" {{ request('tipo_publicacion') == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="gold_special" {{ request('tipo_publicacion') == 'gold_special' ? 'selected' : '' }}>Gold Special</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Estado de la Orden</label>
                        <select name="order_status" class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="paid" {{ request('order_status') == 'paid' ? 'selected' : '' }}>Pagada</option>
                            <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>Enviada</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Estado de la Publicación</label>
                        <select name="estado_publicacion" class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="active" {{ request('estado_publicacion') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="paused" {{ request('estado_publicacion') == 'paused' ? 'selected' : '' }}>Pausado</option>
                            <option value="under_review" {{ request('estado_publicacion') == 'under_review' ? 'selected' : '' }}>En revisión</option>
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


<!-- Sección de resumen por cuenta simplificada -->
<div class="card modern-card mb-4">
    <div class="card-header">
        <h4 class="mb-0">
            Resumen de Ventas ({{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }})
            <span class="days-range">{{ $diasDeRango }} días</span>
        </h4>
        <a href="{{ route('exportar.ventas') }}" class="btn btn-outline-primary btn-sm export-btn">
            <i class="fas fa-file-excel"></i> Exportar a Excel
        </a>
    </div>
    <div class="card-body">
        @if(isset($resumenPorCuenta) && !empty($resumenPorCuenta))
            <div class="d-flex flex-wrap gap-3">
                @foreach($resumenPorCuenta as $cuenta => $total)
                    <div class="progress-container">
                        <div class="progress-label">{{ $cuenta }}</div>
                        <div class="progress modern-progress">
                            <div class="progress-bar"
                                 role="progressbar"
                                 style="width: {{ min(($total / $maxVentasTotal * 100), 100) }}%;"
                                 aria-valuenow="{{ $total }}"
                                 aria-valuemin="0"
                                 aria-valuemax="{{ $maxVentasTotal }}">
                                {{ $total }} ventas
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted text-center">No hay datos de ventas para mostrar en este período.</p>
        @endif
        <div id="restore-columns-order" class="mt-3 d-flex flex-wrap gap-2"></div>
    </div>
</div>


    <!-- Tabla de resultados -->
    <div class="table-responsive">
    <table id="orderTable" class="table table-hover modern-table">
        <thead>
            <tr>
                <th data-column-name="Cuenta">
                    <span>Cuenta</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="Imagen">
                    <span>Imagen</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="Producto" data-sortable="true" data-column="producto">
                    <span>Producto</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="SKU" data-sortable="true" data-column="sku">
                    <span>SKU</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="Título" data-sortable="true" data-column="titulo">
                    <span>Título</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="Ventas" data-sortable="true" data-column="ventas_diarias">
                    <span>Ventas</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="Publicación">
                    <span>Publicación</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="Stock" data-sortable="true" data-column="stock">
                    <span>Stock</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="ImDías de Stock" data-sortable="true" data-column="dias_stock">
                    <span>Días de Stock</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="Estado de la Orden">
                    <span>Estado Orden</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="Estado de la Publicación">
                    <span>Estado Pub.</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
                <th data-column-name="Fecha de Última Venta">
                    <span>Última Venta</span>
                    <i class="fas fa-eye toggle-visibility"></i>
                </th>
            </tr>
        </thead>
        <tbody id="table-body">
            @forelse ($ventas as $venta)
                <tr>
                    <td data-column="Cuenta">{{ $venta['ml_account_id'] ?? 'N/A' }}</td>
                    <td>
                        <img src="{{ $venta['imagen'] ?? asset('images/default.png') }}"
                             alt="{{ $venta['titulo'] ?? 'Sin título' }}"
                             class="table-img">
                    </td>
                    <td data-column="producto">
                        <a href="{{ route('dashboard.ventaid', ['item_id' => $venta['producto'], 'fecha_inicio' => request('fecha_inicio'), 'fecha_fin' => request('fecha_fin')]) }}"
                           class="table-link">
                            {{ $venta['producto'] }}
                        </a>
                        <a href="{{ $venta['url'] }}" target="_blank" class="table-icon-link">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </td>
                    <td data-column="sku">{{ $venta['sku'] ?? 'N/A' }}</td>
                    <td data-column="titulo">{{ $venta['titulo'] }}</td>
                    <td data-column="ventas_diarias">{{ $venta['cantidad_vendida'] }}</td>
                    <td>{{ $venta['tipo_publicacion'] }}</td>
                    <td data-column="stock">{{ $venta['stock'] ?? 'Sin stock' }}</td>
                    <td data-column="dias_stock" class="highlight">{{ $venta['dias_stock'] ?? 'N/A' }}</td>
                    <td>{{ $venta['order_status'] }}</td>
                    <td>{{ $venta['estado'] ?? 'Desconocido' }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta['fecha_ultima_venta'])->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center text-muted">No hay ventas para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


</div>
<!-- Controles de paginación -->
   <!-- Controles de paginación -->
   @include('layouts.pagination', [
    'currentPage' => $currentPage,
    'totalPages' => $totalPages,
    'limit' => $limit
])

@endsection

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- script para ordenar las columnas y ocultar -->
<script>
jQuery(document).ready(function () {
    // Función para inicializar o reinicializar la tabla
    function initDataTable() {
        // Verificar si ya existe una instancia y destruirla
        if ($.fn.DataTable.isDataTable('#orderTable')) {
            $('#orderTable').DataTable().clear().destroy();
        }

        // Media query para detectar móviles (menor a 768px)
        const isMobile = window.matchMedia("(max-width: 768px)").matches;

        // Configuración base de DataTables
        const config = {
            paging: false,
            searching: false,
            info: true,
            autoWidth: false,
            responsive: false,
            scrollX: true,
            stateSave: false,
            processing: false,
            width: '95%',
            ordering: true,
            columnDefs: [
                { targets: '_all', className: 'shrink-text dt-center' },
                { targets: [4], width: '20%' }
            ]
        };

        // Habilitar colReorder solo en escritorio
        config.colReorder = !isMobile;

        // Inicializar la tabla
        var table = $('#orderTable').DataTable(config);

        var restoreContainer = $('#restore-columns-order');

        // Ocultar columna al hacer clic en el ícono del ojo
        $('th i.fas.fa-eye').click(function () {
            var th = $(this).closest('th');
            var columnName = th.data('column-name');
            var column = table.column(th);
            column.visible(false);
            table.columns.adjust().draw(false);
            addRestoreButton(th, columnName);
        });

        // Función para agregar el botón de restauración
        function addRestoreButton(th, columnName) {
            var button = $(`<button class="btn btn-outline-secondary btn-sm">${columnName} <i class="fas fa-eye"></i></button>`);
            button.on('click', function () {
                table.column(th).visible(true);
                table.columns.adjust().draw(false);
                $(this).remove();
            });
            restoreContainer.append(button);
        }
    }

    // Inicializar la tabla al cargar la página
    initDataTable();


});

</script>




<!-- Script para el menu de los filtros -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.querySelector('[data-bs-target="#filtrosCollapse"]');
        const toggleText = toggleBtn ? toggleBtn.querySelector('#toggleText') : null;
        const collapseElement = document.getElementById('filtrosCollapse');

        if (toggleBtn && toggleText && collapseElement) {
            toggleText.textContent = collapseElement.classList.contains('show') ? 'Ocultar Filtros' : 'Mostrar Filtros';

            collapseElement.addEventListener('shown.bs.collapse', function () {
                console.log("Filtros mostrados");
                toggleText.textContent = 'Ocultar Filtros';
            });
            collapseElement.addEventListener('hidden.bs.collapse', function () {
                console.log("Filtros ocultados");
                toggleText.textContent = 'Mostrar Filtros';
            });
        } else {
            console.error('No se encontraron uno o más elementos necesarios');
        }
    });
</script>
<!-- script para calcular las fechas del filtro de desplazamiento -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const diasRange = document.getElementById('diasRange');
    const diasValue = document.getElementById('diasValue');
    const fechaInicioCalculada = document.getElementById('fechaInicioCalculada');

    if (!diasRange || !diasValue || !fechaInicioCalculada) {
        console.error('Elementos no encontrados:', { diasRange, diasValue, fechaInicioCalculada });
        return;
    }

    function updateFechaInicio() {
        const dias = parseInt(diasRange.value);
        diasValue.innerText = dias; // Usar innerText en lugar de textContent
        console.log('Días seleccionados:', dias);

        const hoy = new Date();
        const fechaInicio = new Date(hoy);
        fechaInicio.setDate(hoy.getDate() - dias);

        const year = fechaInicio.getFullYear();
        const month = String(fechaInicio.getMonth() + 1).padStart(2, '0');
        const day = String(fechaInicio.getDate()).padStart(2, '0');
        const fechaFormateada = `${year}-${month}-${day}`;

        fechaInicioCalculada.value = fechaFormateada;
    }

    updateFechaInicio();
    diasRange.addEventListener('input', function () {
        updateFechaInicio();
        console.log('Deslizador movido');
    });
});
</script>
<!-- script para truncar el titulo en mobiles -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('td[data-column="titulo"]').forEach(td => {
        let text = td.textContent.trim();
        if (text.length > 30) {
            td.textContent = text.substring(0, 30) + '...';
        }
    });
});
</script>
@endpush
