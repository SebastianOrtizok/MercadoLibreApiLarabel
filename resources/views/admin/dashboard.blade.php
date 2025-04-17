@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Panel de Administración</h1>

    <!-- Mensajes de éxito o error -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <!-- Sección 1: Seleccionar usuario -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Seleccionar Usuario</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.select-user') }}" method="POST">
                @csrf
                <div class="form-group mb-0">
                    <label for="selected_user_id" class="font-weight-bold">Seleccionar usuario:</label>
                    <select name="selected_user_id" id="selected_user_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Seleccionar usuario --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ session('selected_user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} (ID: {{ $user->id }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Mensaje de usuario seleccionado -->
            @if (session('selected_user_id'))
                <div class="alert alert-info mt-3 mb-0">
                    Actuando como: <strong>{{ $users->find(session('selected_user_id'))->name }}</strong>
                    <a href="{{ route('admin.clear-selection') }}" class="btn btn-sm btn-warning ml-2">Dejar de impersonificar</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Sección 2: Crear usuario -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Crear Nuevo Usuario</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.store-user') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Nombre</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="font-weight-bold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="font-weight-bold">Contraseña</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation" class="font-weight-bold">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Crear Usuario</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>

    <!-- Sección 3: Agregar cuenta de MercadoLibre -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Agregar Cuenta de MercadoLibre</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.add-initial-token') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id" class="font-weight-bold">Usuario:</label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">-- Seleccionar usuario --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ml_account_id" class="font-weight-bold">ID de Cuenta ML:</label>
                            <input type="text" name="ml_account_id" id="ml_account_id" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="access_token" class="font-weight-bold">Access Token:</label>
                            <input type="text" name="access_token" id="access_token" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="refresh_token" class="font-weight-bold">Refresh Token:</label>
                            <input type="text" name="refresh_token" id="refresh_token" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="expires_in" class="font-weight-bold">Expira en (segundos, default 21600):</label>
                    <input type="number" name="expires_in" id="expires_in" class="form-control" value="21600">
                </div>
                <button type="submit" class="btn btn-info">Guardar Cuenta</button>
            </form>
        </div>
    </div>

    <!-- Sección 4: Lista de usuarios -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Lista de Usuarios</h5>
        </div>
        <div class="card-body">
            <!-- Contenedor para botones de restaurar columnas -->
            <div id="restore-columns-users" class="mt-3 d-flex flex-wrap gap-2"></div>
            <div class="table-responsive">
                <table id="usersTable" class="table table-hover modern-table">
                    <thead>
                        <tr>
                            <th data-column-name="ID" data-sortable="true" data-column="id"><span>ID</span><i class="fas fa-eye toggle-visibility"></i></th>
                            <th data-column-name="Nombre" data-sortable="true" data-column="name"><span>Nombre</span><i class="fas fa-eye toggle-visibility"></i></th>
                            <th data-column-name="Email" data-sortable="true" data-column="email"><span>Email</span><i class="fas fa-eye toggle-visibility"></i></th>
                            <th data-column-name="Cuentas ML" data-sortable="true" data-column="cuentas_ml"><span>Cuentas ML</span><i class="fas fa-eye toggle-visibility"></i></th>
                            <th data-column-name="Acciones"><span>Acciones</span><i class="fas fa-eye toggle-visibility"></i></th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        @forelse ($users as $user)
                            <tr>
                                <td data-column="id">{{ $user->id }}</td>
                                <td data-column="name">{{ $user->name }}</td>
                                <td data-column="email">{{ $user->email }}</td>
                                <td data-column="cuentas_ml">{{ $user->mercadolibreTokens->count() }}</td>
                                <td>
                                    <a href="{{ route('admin.user-details', $user->id) }}" class="btn btn-sm btn-info">Ver Detalles</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.4/js/dataTables.colReorder.min.js"></script>
    <script>
        jQuery(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().clear().destroy();
            }
            var table = $('#usersTable').DataTable({
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
                    { targets: [2], width: '20%' } // Columna Email
                ]
            });

            var restoreContainer = $('#restore-columns-users');

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
</div>
@endsection
