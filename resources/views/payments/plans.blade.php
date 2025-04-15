@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-primary fw-bold">Elige tu Plan</h2>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm text-center">
                <div class="card-header bg-primary text-white">
                    <h3>Mensual</h3>
                </div>
                <div class="card-body">
                    <h4>$20</h4>
                    <p>Por mes</p>
                    <div id="paypal-container-MENSUAL"></div>
                </div>
            </div>
        </div>
        <!-- Espacio para trimestral y anual -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm text-center">
                <div class="card-header bg-primary text-white">
                    <h3>Trimestral</h3>
                </div>
                <div class="card-body">
                    <h4>$54</h4>
                    <p>Cada 3 meses</p>
                    <p>(Generá otro botón en PayPal para probar)</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm text-center">
                <div class="card-header bg-primary text-white">
                    <h3>Anual</h3>
                </div>
                <div class="card-body">
                    <h4>$192</h4>
                    <p>Por año</p>
                    <p>(Generá otro botón en PayPal para probar)</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script de PayPal -->
<script src="https://www.paypal.com/sdk/js?client-id=BAALYLUmqvjd-Wzz1IJHFFalfEM-MjIeCdSPEhNYTdQqKeiQF6JC4ml2XVNFFJDNFS-NvHTFQjyvkWTdN4&components=hosted-buttons&disable-funding=venmo¤cy=USD"></script>
<script>
    paypal.HostedButtons({
        hostedButtonId: "SENP7WVTGT344",
        returnUrl: "{{ route('payment.success') }}?user_id={{ auth()->id() }}&plan=mensual" // Redirección con datos
    }).render("#paypal-container-MENSUAL");
</script>
@endsection
