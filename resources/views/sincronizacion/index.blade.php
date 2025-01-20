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
    </style>
</head>
<body>

    <h1>Sincronización de Artículos</h1>

    @if(session('success'))
        <p class="success">{{ session('success') }}</p>
    @elseif(session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    <p>Seleccione una opción para sincronizar los artículos:</p>

    <a href="{{ route('sincronizacion.primera') }}">
        <button>Primera Sincronización</button>
    </a>
    <br><br>
    <a href="{{ route('sincronizacion.actualizar') }}">
        <button>Actualizar Artículos</button>
    </a>
@endsection
