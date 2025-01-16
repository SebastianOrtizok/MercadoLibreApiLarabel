@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Ventas</h2>

    <!-- Formulario para seleccionar el rango de días -->
    <form method="GET" action="{{ route('dashboard.ventas') }}" class="mb-4">
    <div class="form-row align-items-end">
        <div class="col-md-3">
            <label for="dias">Seleccionar rango de días</label>
            <select name="dias" id="dias" class="form-control">
                @for($i = 1; $i <= 360; $i++)
                    <option value="{{ $i }}" {{ request('dias', 360) == $i ? 'selected' : '' }}>
                        {{ $i }} {{ $i > 1 ? 'días' : 'día' }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </div>
</form>


    <!-- Mostrar las fechas seleccionadas -->
    <p>Rango de fechas: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}  - {{ $diasDeRango -1}} días</p>

    <!-- Tabla de resultados -->
    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Título del Artículo</th>
                <th>Ventas Diarias</th>
                <th>Publicacion</th>
                <th>Fecha de Última Venta</th>
            </tr>
        </thead>
        <tbody>
    @forelse($ventas['ventas'] as $venta)
        <tr>
            <!-- Mostrar título del producto -->
            <td>{{ $venta['titulo'] }}</td>

            <!-- Mostrar ventas diarias -->
            <td>{{ $venta['ventas_diarias'] }}</td>
            <!-- Mostrar el tipo de publicación (gold, classic, etc.) -->
            <td>{{ $venta['tipo_publicacion'] }}</td>
            <!-- Mostrar fecha de la última venta con el formato adecuado -->
            <td>{{ \Carbon\Carbon::parse($venta['fecha_ultima_venta'])->format('d/m/Y H:i') }}</td>

        </tr>
    @empty
        <tr>
            <td colspan="4">No hay ventas para este rango de fechas.</td>
        </tr>
    @endforelse
        </tbody>
    </table>
</div>
@endsection
