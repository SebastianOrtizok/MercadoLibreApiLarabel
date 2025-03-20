@extends('layouts.dashboard')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Promociones Sincronizadas</h2>

        <!-- Formulario de filtros colapsado -->
        <div class="mb-4">
            <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false" aria-controls="filtrosCollapse">
                <i class="fas fa-filter"></i> <span id="toggleText">Mostrar Filtros</span>
            </button>
            <div class="collapse" id="filtrosCollapse">
                <form method="GET" action="{{ route('dashboard.item_promotions') }}" class="mt-3">
                    <div class="filtros-container p-3 bg-light rounded shadow-sm">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label>Cuenta</label>
                                <select name="ml_account_id" class="form-control">
                                    <option value="">Todas las cuentas</option>
                                    @foreach (\DB::table('mercadolibre_tokens')->where('user_id', auth()->id())->select('ml_account_id', 'seller_name')->distinct()->get() as $account)
                                        <option value="{{ $account->ml_account_id }}" {{ request('ml_account_id') == $account->ml_account_id ? 'selected' : '' }}>
                                            {{ $account->seller_name ?? $account->ml_account_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label>Estado</label>
                                <select name="status" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="started" {{ request('status') == 'started' ? 'selected' : '' }}>Iniciada</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="candidate" {{ request('status') == 'candidate' ? 'selected' : '' }}>Candidata</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label>Tipo de Promoción</label>
                                <select name="type" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="SMART" {{ request('type') == 'SMART' ? 'selected' : '' }}>Smart</option>
                                    <option value="PRICE_DISCOUNT" {{ request('type') == 'PRICE_DISCOUNT' ? 'selected' : '' }}>Descuento en Precio</option>
                                    <option value="UNHEALTHY_STOCK" {{ request('type') == 'UNHEALTHY_STOCK' ? 'selected' : '' }}>Stock No Saludable</option>
                                    <option value="DEAL" {{ request('type') == 'DEAL' ? 'selected' : '' }}>Oferta Especial</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label>Buscar (ID/Título)</label>
                                <input type="text" name="search" class="form-control" placeholder="Buscar por ID o título" value="{{ request('search') }}">
                            </div>
                            <!-- Nuevo filtro para promociones -->
                            <div class="col-md-3 mb-2">
                                <label>Promociones</label>
                                <select name="promotion_filter" class="form-control">
                                    <option value="all" {{ request('promotion_filter', 'with_promotions') == 'all' ? 'selected' : '' }}>Todos</option>
                                    <option value="with_promotions" {{ request('promotion_filter', 'with_promotions') == 'with_promotions' ? 'selected' : '' }}>Con Promociones</option>
                                    <option value="without_promotions" {{ request('promotion_filter') == 'without_promotions' ? 'selected' : '' }}>Sin Promociones</option>
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

        <!-- Botones para restaurar columnas -->
        <div id="restore-columns-promotions" class="mt-3 d-flex flex-wrap gap-2"></div>

        <!-- Tabla de promociones -->
        <div class="table-responsive">
            <table id="promotionsTable" class="table table-hover modern-table">
                <thead class="sticky">
                    <tr>
                        <th data-column-name="ID Promoción" data-sortable="true" data-column="promotion_id">
                            <span>Imagen</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                        <th data-column-name="Cuenta">
                            <span>Cuenta</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                        <th data-column-name="Producto" data-sortable="true" data-column="ml_product_id">
                            <span>Producto</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                        <th data-column-name="Tipo" data-sortable="true" data-column="type">
                            <span>Tipo</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                        <th data-column-name="Estado" data-sortable="true" data-column="status">
                            <span>Estado</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                        <th data-column-name="Precio Original" data-sortable="true" data-column="original_price">
                            <span>Precio Original</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                        <th data-column-name="Precio Nuevo" data-sortable="true" data-column="new_price">
                            <span>Precio Nuevo</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                        <th data-column-name="Inicio" data-sortable="true" data-column="start_date">
                            <span>Inicio</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                        <th data-column-name="Fin" data-sortable="true" data-column="finish_date">
                            <span>Fin</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                        <th data-column-name="Días Restantes" data-sortable="true" data-column="days_remaining">
                            <span>Días Restantes</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                        <th data-column-name="Nombre" data-sortable="true" data-column="name">
                            <span>Nombre</span>
                            <i class="fas fa-eye toggle-visibility"></i>
                        </th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($promotions as $promo)
                        <tr>
                            <td><img src="{{ $promo->imagen ?? '' }}" alt="Producto" style="max-width: 50px;"></td>
                            <td data-column="Cuenta">{{ $promo->seller_name }}</td>
                            <td data-column="ml_product_id">{{ $promo->titulo }}
                                <span style="font-weight: bold;">{{ $promo->ml_product_id }}</span>
                                <a href="{{ $promo->permalink ?? '#' }}" target="_blank">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </td>
                            <td data-column="type">{{ $promo->type ?? 'N/A' }}</td>
                            <td data-column="status">{{ $promo->status ?? 'N/A' }}</td>
                            <td data-column="original_price">{{ $promo->original_price ?? 'N/A' }}</td>
                            <td data-column="new_price">{{ $promo->new_price ?? 'N/A' }}</td>
                            <td data-column="start_date">{{ $promo->start_date ?? 'N/A' }}</td>
                            <td data-column="finish_date">{{ $promo->finish_date ?? 'N/A' }}</td>
                            <td data-column="days_remaining" class="{{ $promo->days_remaining < 0 && $promo->days_remaining !== 'Sin Promoción' ? 'text-danger' : '' }}">
                                {{ $promo->days_remaining ?? 'N/A' }}
                            </td>
                            <td data-column="name">{{ $promo->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('layouts.pagination', [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'limit' => $limit
        ])
        <a href="{{ route('sincronizacion.index') }}" class="btn btn-primary mt-3">Volver a Sincronización</a>
    </div>
    @endsection
    <!-- Estilos personalizados -->
    <style>
        .no-promotion {
            background-color: #ffe6e6 !important;
        }
    </style>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        jQuery(document).ready(function () {
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#promotionsTable')) {
                    $('#promotionsTable').DataTable().clear().destroy();
                }

                const isMobile = window.matchMedia("(max-width: 768px)").matches;
                const config = {
                    paging: false,
                    searching: false,
                    info: true,
                    autoWidth: false,
                    responsive: false,
                    scrollX: true,
                    stateSave: true,
                    processing: false,
                    width: '95%',
                    ordering: true,
                    columnDefs: [
                        { targets: '_all', className: 'shrink-text dt-center' },
                        { targets: [1], width: '20%' } // Producto
                    ],
                    createdRow: function (row, data, dataIndex) {
                        // Si la columna "Precio Nuevo" (índice 6) es "Sin Promoción", aplicar la clase
                        if (data[6] === 'Sin Promoción') {
                            $(row).addClass('no-promotion');
                        }
                    }
                };

                config.colReorder = !isMobile;
                var table = $('#promotionsTable').DataTable(config);

                var restoreContainer = $('#restore-columns-promotions');

                $('th i.fas.fa-eye').click(function () {
                    var th = $(this).closest('th');
                    var columnName = th.data('column-name');
                    var column = table.column(th);
                    column.visible(false);
                    table.columns.adjust().draw(false);
                    addRestoreButton(th, columnName);
                });

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

            initDataTable();
        });
    </script>

    <!-- Script para el menú de filtros -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function isMobile() {
                return window.innerWidth <= 768; // Ajusta según sea necesario
            }

            document.querySelectorAll('td[data-column="ml_product_id"]').forEach(td => {
                let text = td.textContent.trim();
                if (isMobile() && text.length > 30) {
                    td.textContent = text.substring(0, 30) + '...';
                }
            });
        });
    </script>

