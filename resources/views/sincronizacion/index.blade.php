<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sincronización MercadoLibre</title>
</head>
<body>

    <h1>Sincronización de Artículos</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <p>Seleccione una opción para sincronizar los artículos:</p>

    <a href="{{ route('sincronizacion.primera') }}">
        <button>Primera Sincronización</button>
    </a>
    <br><br>
    <a href="{{ route('sincronizacion.actualizar') }}">
        <button>Actualizar Artículos</button>
    </a>

</body>
</html>
