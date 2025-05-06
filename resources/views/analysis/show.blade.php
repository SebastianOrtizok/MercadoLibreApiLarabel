<!DOCTYPE html>
<html>
<head>
    <title>Análisis</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Detalles del Análisis: {{ $analysis->name }}</h1>

    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <h2>Estadísticas</h2>
    <p>Ventas Totales: {{ $analysis->controller->sales }}</p>
    <p>Ingresos Totales: {{ $analysis->controller->revenue }}</p>

    <h3>Vendedores</h3>
    <table>
        <thead>
            <tr>
                <th>ID Vendedor</th>
                <th>Ventas</th>
                <th>Ingresos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($analysis->controller->sellers as $sellerId => $data)
                <tr>
                    <td>{{ $sellerId }}</td>
                    <td>{{ $data['sales'] }}</td>
                    <td>{{ $data['revenue'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Competidores Seguidos</h3>
    @if (!empty($competitorData))
        <table>
            <thead>
                <tr>
                    <th>ID Ítem</th>
                    <th>Título</th>
                    <th>Precio</th>
                    <th>Vendedor</th>
                    <th>Cantidad Vendida</th>
                    <th>Cantidad Disponible</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($competitorData as $item)
                    <tr>
                        <td>{{ $item['item_id'] }}</td>
                        <td>{{ $item['title'] }}</td>
                        <td>{{ $item['price'] }}</td>
                        <td>{{ $item['seller'] }}</td>
                        <td>{{ $item['sold_quantity'] }}</td>
                        <td>{{ $item['available_quantity'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay competidores seguidos.</p>
    @endif

    <a href="{{ route('analysis.index') }}">Volver</a>
</body>
</html>
