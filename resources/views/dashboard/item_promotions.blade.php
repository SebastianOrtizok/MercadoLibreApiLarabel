@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h1 class="mb-4">Promociones del Ítem</h1>

        @if (count($itemPromotions) > 0)
            @php
                $groupedPromotions = collect($itemPromotions)->groupBy('itemId');
            @endphp
            @foreach ($groupedPromotions as $itemId => $promotions)
                <div class="mb-3">
                    <h2 class="bg-success text-white p-2">{{ $itemId ?? 'Desconocido' }}</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Precio</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Fin</th>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($promotions as $promotion)
                                <tr>
                                    <td>{{ $promotion['type'] ?? 'Desconocido' }}</td>
                                    <td>{{ $promotion['status'] ?? 'Desconocido' }}</td>
                                    <td>{{ $promotion['price'] ?? 'No disponible' }}</td>
                                    <td>{{ $promotion['start_date'] ?? 'Fecha no disponible' }}</td>
                                    <td>{{ $promotion['finish_date'] ?? 'Fecha no disponible' }}</td>
                                    <td>{{ $promotion['name'] ?? 'Sin nombre' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <p>No hay promociones disponibles.</p>
        @endif
    </div>
@endsection
