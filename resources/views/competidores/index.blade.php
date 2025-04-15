@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-primary fw-bold">Gestión de Competidores</h2>

    <!-- Mensaje de éxito -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Formulario para agregar competidor -->
    <div class="mb-4">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#formCollapse" aria-expanded="false" aria-controls="formCollapse">
            <i class="fas fa-plus me-2"></i> Agregar Nuevo Competidor
        </button>
        <div class="collapse mt-3" id="formCollapse">
            <div class="card shadow-sm p-4 bg-light rounded">
                <form action="{{ route('competidores.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="seller_id" class="form-label fw-semibold">Seller ID</label>
                            <input type="text" name="seller_id" id="seller_id" class="form-control" placeholder="Ej: 179571326" required>
                            @error('seller_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nickname" class="form-label fw-semibold">Nickname</label>
                            <input type="text" name="nickname" id="nickname" class="form-control" placeholder="Ej: TESTACCOUNT" required>
                            @error('nickname')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nombre" class="form-label fw-semibold">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Competidor de Prueba" required>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i> Guardar Competidor
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de competidores -->
    <div class="table-responsive mb-5">
        <table class="table table-hover modern-table shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Seller ID</th>
                    <th scope="col">Nickname</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($competidores as $competidor)
                    <tr>
                        <td>{{ $competidor->nombre }}</td>
                        <td>{{ $competidor->seller_id }}</td>
                        <td>{{ $competidor->nickname }}</td>
                        <td>
                            <form action="{{ route('competidores.actualizar') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="competidor_id" value="{{ $competidor->id }}">
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-sync-alt me-2"></i> Actualizar
                                </button>
                            </form>
                            <form action="{{ route('competidores.destroy') }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que querés eliminar este competidor?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="competidor_id" value="{{ $competidor->id }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash-alt me-2"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="fas fa-exclamation-circle me-2"></i> No hay competidores registrados aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Tabla de ítems descargados -->
    <h3 class="mb-4 text-primary fw-bold">Publicaciones Descargadas</h3>
    <div class="table-responsive">
        <table class="table table-hover modern-table shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th scope="col">Competidor</th>
                    <th scope="col">Publicación</th>
                    <th scope="col">Título</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Stock</th>
                    <th scope="col">Ventas</th>
                    <th scope="col">Envío Gratis</th>
                    <th scope="col">Última Actualización</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->competidor->nombre ?? 'N/A' }}</td>
                        <td>{{ $item->item_id }}</td>
                        <td>{{ $item->titulo }}</td>
                        <td>${{ number_format($item->precio, 2) }}</td>
                        <td>{{ $item->cantidad_disponible }}</td>
                        <td>{{ $item->cantidad_vendida }}</td>
                        <td>
                            <span class="badge {{ $item->envio_gratis ? 'bg-success' : 'bg-secondary' }}">
                                {{ $item->envio_gratis ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td>{{ $item->ultima_actualizacion->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-2"></i> No hay publicaciones descargadas aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
