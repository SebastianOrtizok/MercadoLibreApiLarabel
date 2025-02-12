<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h1 class="text-center mb-4">Mercado Libre Inventory</h1>

        @if (!empty($inventory) && is_iterable($inventory))
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Available Quantity</th>
                    <th>Condition</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventory as $item)
                    <tr>
                        <td>{{ $item['title'] ?? 'N/A' }}</td>
                        <td>${{ isset($item['price']) ? number_format($item['price'], 2) : '0.00' }}</td>
                        <td>{{ $item['available_quantity'] ?? '0' }}</td>
                        <td>{{ ucfirst($item['condition'] ?? 'unknown') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-warning text-center">
        No inventory items found.
    </div>
@endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
