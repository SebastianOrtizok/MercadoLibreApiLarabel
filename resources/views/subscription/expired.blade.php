@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5">
                <div class="card-header bg-danger text-white">
                    <h4>Suscripción Vencida</h4>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <p>Tu suscripción ha vencido. Por favor, renueva tu plan para seguir accediendo al dashboard.</p>
                    <div id="paypal-button-container"></div>
                    <a href="{{ route('plans') }}" class="btn btn-primary">Renovar plan</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script
    src="https://www.paypal.com/sdk/js?client-id=BAALYLUmqvjd-Wzz1IJHFFalfEM-MjIeCdSPEhNYTdQqKeiQF6JC4ml2XVNFFJDNFS-NvHTFQjyvkWTdN4&components=hosted-buttons&disable-funding=venmo¤cy=USD">
</script>
<script>
    paypal.HostedButtons({
        hostedButtonId: "TU_HOSTED_BUTTON_ID", // Reemplaza con el ID de tu botón de PayPal
    }).render("#paypal-button-container");
</script>
@endsection
