@extends('layouts.dashboard')

@section('content')
    <title>Sincronización MercadoLibre</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .content {
            padding: 20px;
        }
    </style>

    <div class="content">
        <h1>Sincronización de Artículos y Órdenes</h1>

        @if(session('success'))
            <p class="success">{{ session('success') }}</p>
        @elseif(session('error'))
            <p class="error">{{ session('error') }}</p>
        @endif

        <p>Seleccione una cuenta para sincronizar los artículos:</p>

        @php
            $userId = auth()->id(); // Obtener el ID del usuario autenticado
            $cuentas = \App\Models\MercadoLibreToken::where('user_id', $userId)->get();
        @endphp

        @foreach($cuentas as $cuenta)
            <a href="{{ route('sincronizacion.primera', ['user_id' => $cuenta->ml_account_id]) }}">
                <button class="btn btn-danger">
                    Sincronizar cuenta: {{ $cuenta->ml_account_id }}
                </button>
            </a>
            <br><br>
        @endforeach

        <a href="{{ route('sincronizacion.actualizar') }}">
            <button class="btn btn-warning">Actualizar Artículos</button>
        </a>
        <br><br>

        <form method="GET" action="{{ route('sync.orders.db') }}" class="mb-4">
        <div class="form-row d-flex align-items-center gap-3">
            <div class="col-md-3 mb-2">
                <label for="date_from">Desde:</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFromDefault }}">
            </div>
            <div class="col-md-3 mb-2">
                <label for="date_to">Hasta:</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateToDefault }}">
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-primary w-100">Descargar Órdenes</button>
            </div>
        </div>
    </form>
    </div>
@endsection
