@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Competencia del Artículo: {{ $articulo->titulo ?? 'N/A' }}</h1>

    @if($articulo)
        <div class="card mb-4">
            <div class="card-header">
                <h5>Detalles del Artículo</h5>
            </div>
            <div class="card-body">
                <p><strong>ID Producto:</strong> {{ $articulo->ml_product_id }}</p>
                <p><strong>Precio:</strong> ${{ number_format($articulo->precio, 2, ',', '.') }}</p>
                <p><strong>Stock:</strong> {{ $articulo->stock_actual }}</p>
                <p><strong>Tipo Publicación:</strong> {{ $articulo->tipo_publicacion ?? 'N/A' }}</p>
                <p><strong>Cuenta ML:</strong> {{ $articulo->cuenta_ml ?? 'Sin cuenta' }}</p>
                <p><strong>Enlace:</strong> <a href="{{ $articulo->permalink }}" target="_blank">{{ $articulo->permalink }}</a></p>
                @if($articulo->imagen)
                    <p><strong>Imagen:</strong></p>
                    <img src="{{ $articulo->imagen }}" alt="{{ $articulo->titulo }}" style="max-width: 200px;">
                @endif
            </div>
        </div>

        @if($competencia)
            <div class="card">
                <div class="card-header">
                    <h5>Datos de Competencia</h5>
                </div>
                <div class="card-body">
                    <p><strong>Estado:</strong>
                        <span class="badge {{ $competencia['status'] == 'winning' ? 'bg-success' : ($competencia['status'] == 'not_listed' ? 'bg-danger' : 'bg-warning') }}">
                            {{ $competencia['status'] ?? 'N/A' }}
                        </span>
                    </p>
                    <p><strong>Precio Actual:</strong>
                        ${{ $competencia['current_price'] ? number_format($competencia['current_price'], 2, ',', '.') : 'N/A' }}
                    </p>
                    <p><strong>Precio para Ganar:</strong>
                        ${{ $competencia['price_to_win'] ? number_format($competencia['price_to_win'], 2, ',', '.') : 'N/A' }}
                    </p>
                    <p><strong>Mejoras (Boosts):</strong>
                        @if(is_array($competencia['boosts']) && !empty($competencia['boosts']))
                            <ul>
                                @foreach($competencia['boosts'] as $boost)
                                    <li>{{ $boost['description'] }} ({{ $boost['status'] }})</li>
                                @endforeach
                            </ul>
                        @else
                            N/A
                        @endif
                    </p>
                    <p><strong>Razón (si no gana):</strong>
                        @if(is_array($competencia['reason']) && !empty($competencia['reason']))
                            <ul>
                                @foreach($competencia['reason'] as $razon)
                                    <li>{{ $razon == 'item_not_opted_in' ? 'El artículo no está participando en competencia' : $razon }}</li>
                                @endforeach
                            </ul>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                No se pudieron obtener los datos de competencia para este artículo.
            </div>
        @endif
    @else
        <div class="alert alert-danger">
            {{ $error ?? 'Artículo no encontrado.' }}
        </div>
    @endif

    <a href="{{ route('dashboard.catalogo') }}" class="btn btn-primary mt-3">Volver al Catálogo</a>
</div>
@endsection
