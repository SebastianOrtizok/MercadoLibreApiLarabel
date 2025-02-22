@extends('layouts.dashboard')

@section('content')
    <div class="container my-5">
        <h1 class="text-center mb-4">Account Information</h1>

        @if (!empty($accounts))
            <div class="row">
                @foreach ($accounts as $account)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">{{ $account['account_info']['nickname'] ?? 'Unknown User' }}</h4>
                            </div>
                            <div class="card-body">
                                <p><strong>Account ID:</strong> {{ $account['ml_account_id'] }}</p>
                                <p><strong>Full Name:</strong> {{ $account['account_info']['first_name'] ?? 'N/A' }} {{ $account['account_info']['last_name'] ?? '' }}</p>
                                <p><strong>Email:</strong> {{ $account['account_info']['email'] ?? 'N/A' }}</p>
                                <p><strong>Reputation Level:</strong> {{ $account['account_info']['seller_reputation']['level_id'] ?? 'N/A' }}</p>
                                <p><strong>Points:</strong> {{ $account['account_info']['points'] ?? 0 }}</p>

                                <!-- Gráfico: Ventas Históricas -->
                                <div class="mt-4">
                                    <h5>Sales Data (Historical)</h5>
                                    <canvas id="salesChart-{{ $loop->index }}"></canvas>
                                </div>

                                <!-- Gráfico: Ratings Históricos -->
                                <div class="mt-4">
                                    <h5>Ratings Distribution</h5>
                                    <canvas id="ratingsChart-{{ $loop->index }}"></canvas>
                                </div>

                                <!-- Gráfico: Métricas Últimos 60 Días -->
                                <div class="mt-4">
                                    <h5>Performance Metrics (Last 60 Days)</h5>
                                    <canvas id="metricsChart-{{ $loop->index }}"></canvas>
                                </div>

                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            // Datos históricos de ventas
                            const ctxSales = document.getElementById("salesChart-{{ $loop->index }}").getContext("2d");
                            new Chart(ctxSales, {
                                type: "bar",
                                data: {
                                    labels: ["Completed", "Canceled", "Total"],
                                    datasets: [{
                                        label: "Sales (All Time)",
                                        data: [
                                            {{ $account['account_info']['seller_reputation']['transactions']['completed'] ?? 0 }},
                                            {{ $account['account_info']['seller_reputation']['transactions']['canceled'] ?? 0 }},
                                            {{ $account['account_info']['seller_reputation']['transactions']['total'] ?? 0 }}
                                        ],
                                        backgroundColor: ["#28a745", "#dc3545", "#007bff"]
                                    }]
                                },
                                options: { responsive: true }
                            });

                            // Datos históricos de ratings
                            const ctxRatings = document.getElementById("ratingsChart-{{ $loop->index }}").getContext("2d");
                            new Chart(ctxRatings, {
                                type: "pie",
                                data: {
                                    labels: ["Positive", "Neutral", "Negative"],
                                    datasets: [{
                                        data: [
                                            {{ $account['account_info']['seller_reputation']['transactions']['ratings']['positive'] ?? 0 }},
                                            {{ $account['account_info']['seller_reputation']['transactions']['ratings']['neutral'] ?? 0 }},
                                            {{ $account['account_info']['seller_reputation']['transactions']['ratings']['negative'] ?? 0 }}
                                        ],
                                        backgroundColor: ["#28a745", "#ffc107", "#dc3545"]
                                    }]
                                },
                                options: { responsive: true }
                            });

                            // Métricas de los últimos 60 días
                            const ctxMetrics = document.getElementById("metricsChart-{{ $loop->index }}").getContext("2d");
                            new Chart(ctxMetrics, {
                                type: "bar",
                                data: {
                                    labels: ["Sales (60d)", "Claims (60d)", "Delayed Handling (60d)", "Cancellations (60d)"],
                                    datasets: [{
                                        label: "Last 60 Days Metrics",
                                        data: [
                                            {{ $account['account_info']['seller_reputation']['metrics']['sales']['completed'] ?? 0 }},
                                            {{ $account['account_info']['seller_reputation']['metrics']['claims']['value'] ?? 0 }},
                                            {{ $account['account_info']['seller_reputation']['metrics']['delayed_handling_time']['value'] ?? 0 }},
                                            {{ $account['account_info']['seller_reputation']['metrics']['cancellations']['value'] ?? 0 }}
                                        ],
                                        backgroundColor: ["#007bff", "#dc3545", "#ffc107", "#17a2b8"]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        });
                    </script>

                @endforeach
            </div>
        @else
            <div class="alert alert-warning text-center">
                No account information available.
            </div>
        @endif
    </div>
@endsection
