<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis de Competidores por Categoría</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        select, button {
            padding: 8px;
            font-size: 16px;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .error {
            color: red;
            margin: 10px 0;
        }
        .results {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse | collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h2>Análisis de Competidores por Categoría</h2>

    @if ($error)
        <p class="error">{{ $error }}</p>
    @endif

    <form method="POST" action="{{ route('competidores.category.analyze') }}">
        @csrf
        <div class="controls">
            <select name="category_id" required>
                <option value="">Selecciona una categoría</option>
                @foreach ($categories as $category)
                    <option value="{{ $category['id'] }}" {{ old('category_id') == $category['id'] ? 'selected' : '' }}>
                        {{ $category['name'] }}
                    </option>
                @endforeach
            </select>
            <button type="submit">Iniciar Análisis</button>
        </div>
    </form>

    @if ($analysis)
        <div class="results">
            <h3>Resultados para categoría {{ $analysis['category_id'] }}</h3>
            <p>Ítems analizados: {{ $analysis['total_items'] }}</p>
            <h4>Palabras clave principales:</h4>
            <ul>
                @foreach ($analysis['top_keywords'] as $word => $count)
                    <li>{{ $word }}: {{ $count }} veces</li>
                @endforeach
            </ul>
            <h4>Competidores:</h4>
            <table>
                <thead>
                    <tr>
                        <th>Vendedor</th>
                        <th>Ítems</th>
                        <th>Precio Promedio</th>
                        <th>Envío Gratis (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($analysis['competitors'] as $competitor)
                        <tr>
                            <td>{{ $competitor['seller_id'] }}</td>
                            <td>{{ $competitor['item_count'] }}</td>
                            <td>${{ number_format($competitor['average_price'], 2) }}</td>
                            <td>{{ number_format($competitor['free_shipping_percentage'], 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>
