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
                <th>Precio</th> <!-- Nueva columna para el precio -->
                <th>Condición</th> <!-- Nueva columna para la condición -->
                <th>Stock Actual</th>
                <th>Estado</th>
                <th>Última Venta</th>
                <th>Acciones</th> <!-- Opcional: columna para acciones futuras -->
            </tr>
        </thead>
        <tbody>
        <!-- Eliminar el dd($publications) para continuar con la vista -->
        @foreach($publications['items'] as $item) <!-- Cambiar para recorrer $publications['items'] -->
            <tr>
                <td>
                    @if(isset($item['imagen']) && $item['imagen'])
                        <img src="{{ $item['imagen'] }}" alt="Imagen del producto" class="img-fluid" style="width: 100px;">
                    @else
                        <span>No disponible</span>
                    @endif
                </td>
                <td>
                    <strong>{{ $item['titulo'] }}</strong><br>
                    <span style="color: #007bff; font-weight: bold;">ID: {{ $item['id'] }}</span><br>
                    <a href="{{ $item['permalink'] }}" target="_blank" style="color: #007bff;">Ver en MercadoLibre</a>
                </td>
                <td>${{ number_format($item['precio'], 2, ',', '.') }}</td> <!-- Formato precio -->
                <td>{{ ucfirst($item['condicion']) }}</td>
                <td>{{ $item['stockActual'] }}</td>
                <td>{{ ucfirst($item['estado']) }}</td>
                <td>{{ $item['ultimaVenta'] ?? 'No registrada' }}</td>
                <td>
                    <!-- Opcional: agregar acciones como editar o eliminar -->
                    <a href="#" class="btn btn-sm btn-primary">Editar</a>
                    <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
