@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h1 class="mb-4">Promociones del Ítem</h1>

        {{-- Ver la estructura de los datos --}}
        {{-- @dd($itemPromotions) --}}

        @if (!empty($itemPromotions))
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
                                <th>Nombre</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Fin</th>
                                <th>Precio Original</th>
                                <th>Precio con Descuento</th>
                                <th>Beneficios</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($promotions as $promotion)
                                <tr>
                                    <td>{{ $promotion['type'] ?? 'Desconocido' }}</td>
                                    <td>{{ $promotion['status'] ?? 'Desconocido' }}</td>
                                    <td>{{ $promotion['name'] ?? 'Sin nombre' }}</td>
                                    <td>{{ $promotion['start_date'] ?? 'Fecha no disponible' }}</td>
                                    <td>{{ $promotion['finish_date'] ?? 'Fecha no disponible' }}</td>
                                    <td>
                                        {{ isset($promotion['original_price']) ? number_format($promotion['original_price'], 2) : 'No disponible' }}
                                    </td>
                                    <td>
                                        {{ isset($promotion['new_price']) ? number_format($promotion['new_price'], 2) : 'No disponible' }}
                                    </td>
                                    <td>
                                        @if (isset($promotion['benefits']) && !empty($promotion['benefits']))
                                            <ul>
                                                <li>Tipo de Beneficio: {{ $promotion['benefits']['type'] ?? 'Desconocido' }}</li>
                                                <li>Porcentaje Meli: {{ $promotion['benefits']['meli_percent'] ?? 'N/A' }}%</li>
                                                <li>Porcentaje Vendedor: {{ $promotion['benefits']['seller_percent'] ?? 'N/A' }}%</li>
                                            </ul>
                                        @else
                                            <p>No hay beneficios disponibles.</p>
                                        @endif
                                    </td>
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

       <!-- Controles de paginación -->

@endsection
