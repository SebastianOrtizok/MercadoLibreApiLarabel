@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-primary fw-bold">Elige tu Plan</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm text-center">
                <div class="card-header bg-primary text-white">
                    <h3>Mensual</h3>
                </div>
                <div class="card-body">
                    <h4>$10000 ARS</h4>
                    <p>Por mes</p>
                    <form action="{{ route('payment.create') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" value="mensual">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-cart me-2"></i> Suscribirme
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm text-center">
                <div class="card-header bg-primary text-white">
                    <h3>Trimestral</h3>
                </div>
                <div class="card-body">
                    <h4>$27000 ARS</h4>
                    <p>Cada 3 meses</p>
                    <form action="{{ route('payment.create') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" value="trimestral">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-cart me-2"></i> Suscribirme
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm text-center">
                <div class="card-header bg-primary text-white">
                    <h3>Anual</h3>
                </div>
                <div class="card-body">
                    <h4>$96000 ARS</h4>
                    <p>Por año</p>
                    <form action="{{ route('payment.create') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" value="anual">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-cart me-2"></i> Suscribirme
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
