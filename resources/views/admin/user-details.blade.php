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
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Cuenta ML</th>
                            <th>Nombre del Vendedor</th>
                            <th>Access Token</th>
                            <th>Refresh Token</th>
                            <th>Expira en</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user->mercadolibreTokens as $token)
                            <tr>
                                <td>{{ $token->ml_account_id }}</td>
                                <td>{{ $token->seller_name ?? 'No disponible' }}</td>
                                <td>{{ Str::limit($token->access_token, 20) }}</td>
                                <td>{{ Str::limit($token->refresh_token, 20) }}</td>
                                <td>{{ $token->expires_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection
