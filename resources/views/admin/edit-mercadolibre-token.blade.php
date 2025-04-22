@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Editar Token de MercadoLibre para {{ $user->name }}</h1>

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

    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">Formulario de Edición</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.mercadolibre-tokens.update', ['user' => $user->id, 'token' => $token->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="ml_account_id" class="font-weight-bold">ID Cuenta ML</label>
                    <input type="text" name="ml_account_id" id="ml_account_id" class="form-control @error('ml_account_id') is-invalid @enderror" value="{{ old('ml_account_id', $token->ml_account_id) }}">
                    @error('ml_account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="seller_name" class="font-weight-bold">Nombre del Vendedor</label>
                    <input type="text" name="seller_name" id="seller_name" class="form-control @error('seller_name') is-invalid @enderror" value="{{ old('seller_name', $token->seller_name) }}">
                    @error('seller_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="access_token" class="font-weight-bold">Access Token</label>
                    <input type="text" name="access_token" id="access_token" class="form-control @error('access_token') is-invalid @enderror" value="{{ old('access_token', $token->access_token) }}" required>
                    @error('access_token')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="refresh_token" class="font-weight-bold">Refresh Token</label>
                    <input type="text" name="refresh_token" id="refresh_token" class="form-control @error('refresh_token') is-invalid @enderror" value="{{ old('refresh_token', $token->refresh_token) }}" required>
                    @error('refresh_token')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="expires_in" class="font-weight-bold">Expires In (segundos, predeterminado: 21600)</label>
                    <input type="number" name="expires_in" id="expires_in" class="form-control @error('expires_in') is-invalid @enderror" value="{{ old('expires_in', 21600) }}" placeholder="21600">
                    @error('expires_in')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-warning">Actualizar Token</button>
                <a href="{{ route('admin.user-details', $user->id) }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
