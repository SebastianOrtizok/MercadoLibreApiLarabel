@extends('layouts.dashboard')
@section('content')
<body class="bg-light">
    <div class="container my-5">
        <h1>Productos de la Categoría: {{ $categoryId }}</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Título</th>
                    <th>Precio</th>
                    <th>Disponibles</th>
                    <th>Enlace</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items['items'] as $item)
                <tr>
                    <td><img src="{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}" class="img-thumbnail" style="width: 100px;"></td>
                    <td>{{ $item['title'] }}</td>
                    <td>${{ number_format($item['price'], 2, ',', '.') }}</td>
                    <td>{{ $item['available_quantity'] }}</td>
                    <td><a href="{{ $item['permalink'] }}" class="btn btn-primary btn-sm" target="_blank">Ver en MercadoLibre</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($items['total'] > count($items['items']))
        <div class="mt-3">
            <p>Mostrando {{ count($items['items']) }} de {{ $items['total'] }} productos.</p>
        </div>
        @else
        <div class="mt-3">
            <p>Mostrando todos los productos disponibles.</p>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection
