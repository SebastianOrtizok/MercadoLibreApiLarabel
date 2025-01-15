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
                <th>SKU</th> <!-- Columna para el SKU -->
                <th>Tipo de Publicación</th> <!-- Columna para el tipo de publicación -->
                <th>En Catálogo</th> <!-- Columna para saber si está en catálogo -->
            </tr>
        </thead>
        <tbody>
         @forelse($publications as $item)
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
                <td>{{ $item['sku'] ?? 'No disponible' }}</td> <!-- SKU -->
                <td>{{ $item['tipoPublicacion'] ?? 'Desconocido' }}</td> <!-- Tipo de publicación -->
                <td>{{ $item['enCatalogo'] ?? 'No disponible' }}</td> <!-- En catálogo -->
            </tr>
         @empty
            <tr>
                <td colspan="9" class="text-center">No se encontraron publicaciones.</td>
            </tr>
         @endforelse
        </tbody>
    </table>

    <!-- Controles de paginación -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                <a class="page-link" href="?page={{ $currentPage - 1 }}&limit={{ $limit }}" aria-label="Anterior">&laquo;</a>
            </li>

            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="?page={{ $i }}&limit={{ $limit }}">{{ $i }}</a>
                </li>
            @endfor

            <li class="page-item {{ $currentPage == $totalPages ? 'disabled' : '' }}">
                <a class="page-link" href="?page={{ $currentPage + 1 }}&limit={{ $limit }}" aria-label="Siguiente">&raquo;</a>
            </li>
        </ul>
    </nav>
</div>
@endsection
