@extends('layouts.dashboard')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Publicaciones</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                 <th>Título</th>
                <th>Vistas</th>
                <th>Vendidos</th>
            </tr>
        </thead>
        <tbody>
        @foreach($publications['items'] as $publication)
                    <tr>

                    <td>{{ $publication['body']['title'] }}</td>
                    <td>{{ $publication['visits'] ?? 'No disponible' }}</td>
                    <td>{{ $publication['body']['sold_quantity'] }}</td>
                    </tr>
                @endforeach

        </tbody>
    </table>
</div>
@endsection
