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
</head>
<body>

    <div class="content">
    <h1>Sincronización de Artículos</h1>

    @if(session('success'))
        <p class="success">{{ session('success') }}</p>
    @elseif(session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif
    <p>Seleccione una opción para sincronizar los artículos:</p>

    <a href="{{ route('sincronizacion.primera') }}">
        <button class="btn btn-danger">Primera Sincronización</button>
    </a>
    <br><br>
    <a href="{{ route('sincronizacion.actualizar') }}">
        <button class="btn btn-warning">Actualizar Artículos</button>
    </a>
</div>
@endsection
