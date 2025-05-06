<!DOCTYPE html>
<html>
<head>
    <title>Listado de Ítems por Categoría</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Listado de Ítems por Categoría</h1>

        @if ($error)
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

        <form method="POST" action="{{ route('competidores.category.analyze') }}">
            @csrf
            <div class="form-group">
                <label for="category_id">Seleccionar Categoría:</label>
                <select name="category_id" id="category_id" class="form-control">
                    @foreach ($categories as $category)
                        <option value="{{ $category['id'] }}" {{ old('category_id', $categoryId) == $category['id'] ? 'selected' : '' }}>
                            {{ $category['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Listar Ítems</button>
        </form>

        @if (!empty($items))
            <h3>Ítems en la categoría {{ $categoryId }} (Total: {{ $total }})</h3>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Precio</th>
                        <th>Cantidad Disponible</th>
                        <th>Cantidad Vendida</th>
                        <th>Envío Gratis</th>
                        <th>Vendedor ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item['id'] }}</td>
                            <td>{{ $item['title'] }}</td>
                            <td>{{ $item['price'] }}</td>
                            <td>{{ $item['available_quantity'] }}</td>
                            <td>{{ $item['sold_quantity'] }}</td>
                            <td>{{ $item['shipping']['free_shipping'] ? 'Sí' : 'No' }}</td>
                            <td>{{ $item['seller']['id'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No se encontraron ítems para mostrar.</p>
        @endif
    </div>
</body>
</html>
