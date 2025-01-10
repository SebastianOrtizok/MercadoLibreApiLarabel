@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Publicaciones</h2>

    <!-- Tabla con clases de Bootstrap para un mejor diseño -->
    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Imagen</th>
                <th>Título</th>
                <th>Ventas en los últimos 30 días</th>
                <th>Stock Actual</th>
                <th>Stock Estimado</th>
                <th>Estado</th>
                <th>Última Venta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reporte as $item)
            <tr>
                <td><img src="{{ $item['imagen'] }}" alt="Imagen del producto" class="img-fluid" style="width: 100px;"></td>
                <td>
                    <strong>{{ $item['titulo'] }}</strong><br>
                    <!-- Mostrar el ID en un color diferente y el link a MercadoLibre -->
                    <span style="color: #007bff; font-weight: bold;">ID: {{ $item['id'] }}</span><br>
                    <a href="{{ $item['permalink'] }}" target="_blank" style="color: #007bff;">Ver en MercadoLibre</a>

                </td>
                <td>{{ $item['ventasDiarias'] }}</td>
                <td>{{ $item['stockActual'] }}</td>
                <td>{{ $item['stockEstimado'] }}</td>
                <td>{{ ucfirst($item['estado']) }}</td>
                <td>{{ $item['ultimaVenta'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
