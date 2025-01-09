<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h1 class="text-center mb-4">Account Information</h1>

        @if (!empty($accounts))
            @foreach ($accounts as $account)
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h4>{{ $account['account_info']['nickname'] ?? 'Unknown User' }}</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Account ID:</strong> {{ $account['ml_account_id'] }}</p> <!-- Cambié 'account_id' por 'ml_account_id' -->
                        <p><strong>Full Name:</strong> {{ $account['account_info']['first_name'] ?? 'N/A' }} {{ $account['account_info']['last_name'] ?? '' }}</p>
                        <p><strong>Email:</strong> {{ $account['account_info']['email'] ?? 'N/A' }}</p>
                        <p><strong>Reputation:</strong> {{ $account['account_info']['seller_reputation']['level_id'] ?? 'N/A' }}</p>
                        <p><strong>Points:</strong> {{ $account['account_info']['points'] ?? 0 }}</p>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-warning text-center">
                No account information available.
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
