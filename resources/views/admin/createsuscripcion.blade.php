@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Gestión de Suscripciones</h1>

    <!-- Mensajes de éxito o error -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <!-- Sección 1: Crear/Editar Suscripción -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">{{ isset($suscripcion) ? 'Editar Suscripción' : 'Crear Nueva Suscripción' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($suscripcion) ? route('admin.suscripciones.update', $suscripcion->id) : route('admin.suscripciones.store') }}" method="POST">
                @csrf
                @if (isset($suscripcion))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuario_id" class="font-weight-bold">Usuario</label>
                            <select name="usuario_id" id="usuario_id" class="form-control @error('usuario_id') is-invalid @enderror" required>
                                <option value="">-- Seleccionar usuario --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ (isset($suscripcion) && $suscripcion->usuario_id == $user->id) || old('usuario_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} (ID: {{ $user->id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('usuario_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="plan" class="font-weight-bold">Plan</label>
                            <select name="plan" id="plan" class="form-control @error('plan') is-invalid @enderror" required>
                                <option value="">-- Seleccionar plan --</option>
                                <option value="test" {{ (isset($suscripcion) && $suscripcion->plan == 'test') || old('plan') == 'test' ? 'selected' : '' }}>Test</option>
                                <option value="prueba_gratuita" {{ (isset($suscripcion) && $suscripcion->plan == 'prueba_gratuita') || old('plan') == 'prueba_gratuita' ? 'selected' : '' }}>Prueba Gratuita</option>
                                <option value="mensual" {{ (isset($suscripcion) && $suscripcion->plan == 'mensual') || old('plan') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                                <option value="trimestral" {{ (isset($suscripcion) && $suscripcion->plan == 'trimestral') || old('plan') == 'trimestral' ? 'selected' : '' }}>Trimestral</option>
                                <option value="anual" {{ (isset($suscripcion) && $suscripcion->plan == 'anual') || old('plan') == 'anual' ? 'selected' : '' }}>Anual</option>
                            </select>
                            @error('plan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="monto" class="font-weight-bold">Monto (ARS)</label>
                            <input type="number" step="0.01" name="monto" id="monto" class="form-control @error('monto') is-invalid @enderror" value="{{ isset($suscripcion) ? $suscripcion->monto : old('monto', 0) }}" required>
                            @error('monto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado" class="font-weight-bold">Estado</label>
                            <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                                <option value="activo" {{ (isset($suscripcion) && $suscripcion->estado == 'activo') || old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="vencido" {{ (isset($suscripcion) && $suscripcion->estado == 'vencido') || old('estado') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                                <option value="cancelado" {{ (isset($suscripcion) && $suscripcion->estado == 'cancelado') || old('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_inicio" class="font-weight-bold">Fecha de Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ isset($suscripcion) ? $suscripcion->fecha_inicio->format('Y-m-d') : old('fecha_inicio', now()->format('Y-m-d')) }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_fin" class="font-weight-bold">Fecha de Fin (opcional)</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" value="{{ isset($suscripcion) && $suscripcion->fecha_fin ? $suscripcion->fecha_fin->format('Y-m-d') : old('fecha_fin') }}">
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning">{{ isset($suscripcion) ? 'Actualizar Suscripción' : 'Crear Suscripción' }}</button>
                <a href="{{ route('admin.suscripciones.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>

    <!-- Sección 2: Lista de Suscripciones -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Lista de Suscripciones</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="suscripcionesTable" class="table table-hover modern-table">
                    <thead>
                        <tr>
                            <th data-column-name="ID" data-sortable="true">ID</th>
                            <th data-column-name="Usuario" data-sortable="true">Usuario</th>
                            <th data-column-name="Plan" data-sortable="true">Plan</th>
                            <th data-column-name="Monto" data-sortable="true">Monto (ARS)</th>
                            <th data-column-name="Fecha Inicio" data-sortable="true">Fecha Inicio</th>
                            <th data-column-name="Fecha Fin" data-sortable="true">Fecha Fin</th>
                            <th data-column-name="Estado" data-sortable="true">Estado</th>
                            <th data-column-name="Acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suscripciones as $suscripcion)
                            <tr>
                                <td>{{ $suscripcion->id }}</td>
                                <td>{{ $suscripcion->usuario->name }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $suscripcion->plan)) }}</td>
                                <td>{{ number_format($suscripcion->monto, 2) }}</td>
                                <td>{{ $suscripcion->fecha_inicio->format('d/m/Y') }}</td>
                                <td>{{ $suscripcion->fecha_fin ? $suscripcion->fecha_fin->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ ucfirst($suscripcion->estado) }}</td>
                                <td>
                                    <a href="{{ route('admin.suscripciones.edit', $suscripcion->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay suscripciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        jQuery(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#suscripcionesTable')) {
                $('#suscripcionesTable').DataTable().clear().destroy();
            }
            $('#suscripcionesTable').DataTable({
                paging: true,
                searching: true,
                info: true,
                autoWidth: false,
                responsive: true,
                scrollX: true,
                stateSave: false,
                processing: true,
                columnDefs: [
                    { targets: '_all', className: 'dt-center' },
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });
        });
    </script>
</div>
@endsection
