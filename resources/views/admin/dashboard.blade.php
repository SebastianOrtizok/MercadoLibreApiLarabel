@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Panel de Administración</h1>

    <!-- Mensajes de éxito o error -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
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
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Cuentas ML</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mercadolibreTokens->count() }}</td>
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

@endsection
