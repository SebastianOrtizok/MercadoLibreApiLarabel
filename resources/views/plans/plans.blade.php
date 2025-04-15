<form action="{{ route('payment.create') }}" method="POST">
    @csrf
    <input type="hidden" name="plan" value="{{ $name }}">
    <button type="submit" class="btn btn-primary w-100">
        <i class="fas fa-shopping-cart me-2"></i> Suscribirme
    </button>
</form>
