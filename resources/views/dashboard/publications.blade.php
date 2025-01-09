<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Mercado Libre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h1 class="mb-4">Gestión de Publicaciones</h1>

        @if($publications['total'] > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>ID</th>
                            <th>Precio</th>
                            <th>Cantidad Disponible</th>
                            <th>Vendidos</th>
                            <th>Estado</th>
                            <th>En Catálogo</th>
                            <th>Última Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($publications['items'] as $publication)
                        <tr>
                            <td>
                                <img src="{{ $publication['body']['thumbnail'] }}" alt="{{ $publication['body']['title'] }}" class="img-thumbnail" style="max-width: 100px;">
                            </td>
                            <td>{{ $publication['body']['title'] }}</td>
                            <td>{{ $publication['body']['id'] }}</td>
                            <td>${{ number_format($publication['body']['price'], 2, ',', '.') }}</td>
                            <td>{{ $publication['body']['available_quantity'] }}</td>
                            <td>{{ $publication['body']['sold_quantity'] }}</td>
                            <td>
                                {{ ucfirst($publication['body']['condition']) }}
                            </td>
                            <td>
                                @if($publication['body']['catalog_listing'])
                                    <span class="badge bg-success">Sí</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($publication['body']['last_updated'])->format('d/m/Y H:i') }}</td>
<td>
    <a href="{{ $publication['body']['permalink'] }}" class="btn btn-primary btn-sm" target="_blank">Ver Publicación</a>
    <a href="{{ url('/dashboard/category', ['categoryId' => $publication['body']['category_id']]) }}"
       class="btn btn-warning btn-sm">Ver Competencia</a>
</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-warning" role="alert">
                No hay publicaciones para mostrar.
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
