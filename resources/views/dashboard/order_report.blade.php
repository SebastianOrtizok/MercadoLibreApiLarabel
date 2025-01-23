@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Ventas</h2>

    <!-- Formulario para seleccionar el rango de días -->
    <form method="GET" action="{{ route('dashboard.ventas') }}" class="mb-4">
        <div class="form-row d-flex align-items-center">
            <!-- Columna para el campo de días -->
            <div>
                <select name="dias" id="dias" class="form-control custom-select">
                    <option value="" disabled selected>Cantidad de días</option> <!-- Opción por defecto -->
                    @for($i = 1; $i <= 30; $i++)
                        <option value="{{ $i }}" {{ request('dias') == $i ? 'selected' : '' }}>
                            {{ $i }} {{ $i > 1 ? 'días' : 'día' }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Columna para el botón con el ícono de lupa alineado a la derecha -->
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn custom-btn">
                    <i class="fas fa-search"></i> <!-- Lupa de color azul -->
                </button>
            </div>
        </div>
    </form>



    <!-- Mostrar las fechas seleccionadas -->
    <p><strong>Rango de fechas:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }} - {{ $diasDeRango - 1 }} días</p>

    <!-- Tabla de resultados -->
    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Imagen</th>
                <th data-sortable="true" data-column="producto">Producto</th>
                <th data-sortable="true" data-column="sku">SKU</th>
                <th data-sortable="true" data-column="titulo">Título</th>
                <th data-sortable="true" data-column="ventas_diarias">Ventas Diarias</th>
                <th>Publicación</th>
                <th data-sortable="true" data-column="stock">Stock</th>
                <th data-sortable="true" data-column="dias_stock">Días de Stock</th>
                <th>Estado</th>
                <th>Fecha de Última Venta</th>
            </tr>
        </thead>
        <tbody id="table-body">
        @forelse($ventas['ventas'] as $venta)
            <tr>
                <!-- Mostrar imagen del producto -->
                <td>
                    <div class="img-container">
                        <img src="{{ $venta['imagen'] }}" alt="Imagen de {{ $venta['titulo'] }}" class="img-fluid">
                    </div>
                </td>
                <!-- Producto -->
                <td data-column="producto">{{ $venta['producto'] }}</td>
                <!-- Mostrar SKU -->
                <td data-column="sku">{{ $venta['sku'] }}</td>
                <!-- Mostrar título del producto -->
                <td data-column="titulo">{{ $venta['titulo'] }}</td>
                <!-- Mostrar ventas diarias -->
                <td data-column="ventas_diarias">{{ $venta['ventas_diarias'] }}</td>
                <!-- Mostrar el tipo de publicación -->
                <td>{{ $venta['tipo_publicacion'] }}</td>
                <!-- Mostrar stock -->
                <td data-column="stock">{{ $venta['stock'] }}</td>
                <!-- Mostrar días de stock -->
                <td data-column="dias_stock">{{ $venta['dias_stock'] }}</td>
                <!-- Estado -->
                <td>{{ $venta['estado'] }}</td>
                <!-- Mostrar fecha de la última venta -->
                <td>{{ \Carbon\Carbon::parse($venta['fecha_ultima_venta'])->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-danger text-center">No hay ventas para este rango de fechas.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection



<script>
    document.addEventListener('DOMContentLoaded', () => {
        const headers = document.querySelectorAll('th[data-sortable="true"]');
        const tableBody = document.getElementById('table-body');

        headers.forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-column');
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                const isAscending = header.classList.contains('ascending');

                // Ordenar las filas
                rows.sort((rowA, rowB) => {
                    const cellA = rowA.querySelector(`[data-column="${column}"]`).textContent.trim().toLowerCase();
                    const cellB = rowB.querySelector(`[data-column="${column}"]`).textContent.trim().toLowerCase();

                    if (!isNaN(parseFloat(cellA)) && !isNaN(parseFloat(cellB))) {
                        return isAscending ? parseFloat(cellA) - parseFloat(cellB) : parseFloat(cellB) - parseFloat(cellA);
                    }

                    return isAscending
                        ? cellA.localeCompare(cellB)
                        : cellB.localeCompare(cellA);
                });

                // Actualizar las clases de orden en los encabezados
                headers.forEach(h => h.classList.remove('ascending', 'descending'));
                header.classList.add(isAscending ? 'descending' : 'ascending');

                // Renderizar las filas ordenadas
                rows.forEach(row => tableBody.appendChild(row));
            });
        });
    });
</script>
