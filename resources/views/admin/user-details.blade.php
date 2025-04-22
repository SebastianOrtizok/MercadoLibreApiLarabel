@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Detalles del Usuario: {{ $user->name }}</h1>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Información del Usuario</h5>
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> {{ $user->email }}</p>
        </div>
    </div>

    <h3 class="mb-3">Cuentas de MercadoLibre</h3>
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Cuentas Asociadas</h5>
        </div>
        <div class="card-body">
            @if ($user->mercadolibreTokens->isEmpty())
                <p>No hay cuentas asociadas.</p>
            @else
                <!-- Contenedor para botones de restaurar columnas -->
                <div id="restore-columns-ml-accounts" class="mt-3 d-flex flex-wrap gap-2"></div>
                <div class="table-responsive">
                    <table id="mlAccountsTable" class="table table-hover modern-table">
                        <thead>
                            <tr>
                                <th data-column-name="ID Cuenta ML" data-sortable="true" data-column="ml_account_id"><span>ID Cuenta ML</span><i class="fas fa-eye toggle-visibility"></i></th>
                                <th data-column-name="Nombre del Vendedor" data-sortable="true" data-column="seller_name"><span>Nombre del Vendedor</span><i class="fas fa-eye toggle-visibility"></i></th>
                                <th data-column-name="Access Token" data-sortable="true" data-column="access_token"><span>Access Token</span><i class="fas fa-eye toggle-visibility"></i></th>
                                <th data-column-name="Refresh Token" data-sortable="true" data-column="refresh_token"><span>Refresh Token</span><i class="fas fa-eye toggle-visibility"></i></th>
                                <th data-column-name="Expira en" data-sortable="true" data-column="expires_at"><span>Expira en</span><i class="fas fa-eye toggle-visibility"></i></th>
                                <th data-column-name="Acciones" data-sortable="false">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @foreach ($user->mercadolibreTokens as $token)
                                <tr>
                                    <td data-column="ml_account_id">{{ $token->ml_account_id }}</td>
                                    <td data-column="seller_name">{{ $token->seller_name ?? 'No disponible' }}</td>
                                    <td data-column="access_token">{{ Str::limit($token->access_token, 20) }}</td>
                                    <td data-column="refresh_token">{{ Str::limit($token->refresh_token, 20) }}</td>
                                    <td data-column="expires_at">{{ $token->expires_at ? $token->expires_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.mercadolibre-tokens.edit', ['user' => $user->id, 'token' => $token->id]) }}" class="btn btn-sm btn-warning">Editar</a>
                                        <form action="{{ route('admin.mercadolibre-tokens.destroy', ['user' => $user->id, 'token' => $token->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este token?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-3">Volver</a>

    <!-- Scripts -->
    @if (!$user->mercadolibreTokens->isEmpty())
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/colreorder/1.5.4/js/dataTables.colReorder.min.js"></script>
        <script>
            jQuery(document).ready(function () {
                if ($.fn.DataTable.isDataTable('#mlAccountsTable')) {
                    $('#mlAccountsTable').DataTable().clear().destroy();
                }
                var table = $('#mlAccountsTable').DataTable({
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
                        { targets: [2], width: '20%' }, // Columna Access Token
                        { targets: -1, orderable: false } // Columna Acciones
                    ]
                });

                var restoreContainer = $('#restore-columns-ml-accounts');

                $('th i.fas.fa-eye.toggle-visibility').click(function () {
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
            });
        </script>
    @endif
</div>
@endsection
