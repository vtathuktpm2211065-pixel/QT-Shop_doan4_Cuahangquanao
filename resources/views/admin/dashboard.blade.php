@extends('layouts.appp')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Dashboard quản trị</h3>

    <!-- Thống kê nhanh -->
    <div class="row text-white mb-4">
        <div class="col-md-2">
            <a href="{{ route('admin.san-pham.index') }}" class="text-white text-decoration-none">
                <div class="p-3 bg-danger rounded">🛍️ {{ $products }}<br><small>Sản phẩm</small></div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.stock.index') }}" class="text-white text-decoration-none">
                <div class="p-3 bg-warning rounded"><i class="fas fa-box"></i> {{$stocks }}<br><small> Kho</small></div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.san-pham.index') }}" class="text-white text-decoration-none">
                <div class="p-3 bg-primary rounded">🎁 {{ $vouchers }}<br><small>Voucher</small></div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.carts.index') }}" class="text-white text-decoration-none">
                <div class="p-3 bg-info rounded">🛒 {{ $carts }}<br><small>Giỏ hàng</small></div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.orders.index') }}" class="text-white text-decoration-none">
                <div class="p-3 bg-success rounded">📦 {{ $orders }}<br><small>Đơn hàng</small></div>
            </a>
        </div>
    </div>

    <div class="card mt-4 mb-4">
        <div class="card-header">
            Top 5 sản phẩm có doanh thu cao nhất tháng {{ \Carbon\Carbon::now()->format('m/Y') }}
        </div>
        <div class="card-body">
            <canvas id="topProductsChart" height="120"></canvas>
        </div>
    </div>

    <!-- Biểu đồ doanh thu theo tháng trong năm -->
    <div class="card">
        <div class="card-header">Biểu đồ doanh thu theo tháng</div>
        <div class="card-body">
            <canvas id="yearlyChart" height="120"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const topProductsCanvas = document.getElementById('topProductsChart');
    if (topProductsCanvas) {
        const topProductsCtx = topProductsCanvas.getContext('2d');
        new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels, JSON_UNESCAPED_UNICODE) !!},
                datasets: [{
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' VND';
                            }
                        }
                    }
                }
            }
        });
    }

    const yearlyCanvas = document.getElementById('yearlyChart');
    if (yearlyCanvas) {
        const yearlyCtx = yearlyCanvas.getContext('2d');
        new Chart(yearlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_map(function($m) { return 'Tháng ' . $m; }, range(1, 12)), JSON_UNESCAPED_UNICODE) !!},
                datasets: [{
                    data: {!! json_encode(array_values($yearlySales)) !!},
                    borderColor: '#28a745',
                    fill: false
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' VND';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Dashboard quản trị</h3>

    <!-- Thống kê nhanh -->
    <div class="row text-white mb-4">
    <div class="col-md-2">
        <a href="{{ route('admin.san-pham.index') }}" class="text-white text-decoration-none">
            <div class="p-3 bg-danger rounded">🛍️ {{ $products }}<br><small>Sản phẩm</small></div>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.stock.index') }}" class="text-white text-decoration-none">
            <div class="p-3 bg-warning rounded"><i class="fas fa-box"></i> {{$stocks }}<br><small> Kho</small>
</div>

        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.san-pham.index') }}" class="text-white text-decoration-none">
            <div class="p-3 bg-primary rounded">🎁 {{ $vouchers }}<br><small>Voucher</small></div>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.carts.index') }}" class="text-white text-decoration-none">
            <div class="p-3 bg-info rounded">🛒 {{ $carts }}<br><small>Giỏ hàng</small></div>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.orders.index') }}" class="text-white text-decoration-none">
            <div class="p-3 bg-success rounded">📦 {{ $orders }}<br><small>Đơn hàng</small></div>
        </a>
    </div>
</div>


    <div class="card mt-4 mb-4">
    <div class="card-header">
        Top 5 sản phẩm có doanh thu cao nhất tháng {{ \Carbon\Carbon::now()->format('m/Y') }}
    </div>
    <div class="card-body">
        <canvas id="topProductsChart" height="120"></canvas>
    </div>
</div>

    <!-- Biểu đồ doanh thu theo tháng trong năm -->
    <div class="card">
        <div class="card-header">Biểu đồ doanh thu theo tháng</div>
        <div class="card-body">
            <canvas id="yearlyChart" height="120"></canvas>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const topProductsCanvas = document.getElementById('topProductsChart');
    if (topProductsCanvas) {
        const topProductsCtx = topProductsCanvas.getContext('2d');
        new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels, JSON_UNESCAPED_UNICODE) !!},
                datasets: [{
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
    callback: function(value) {
        return value.toLocaleString() + ' VND';
    }
}

                    }
                }
            }
        });
    }

    const yearlyCanvas = document.getElementById('yearlyChart');
    if (yearlyCanvas) {
        const yearlyCtx = yearlyCanvas.getContext('2d');
       new Chart(yearlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_map(function($m) { return 'Tháng ' . $m; }, range(1, 12)), JSON_UNESCAPED_UNICODE) !!},
        datasets: [{
            data: {!! json_encode(array_values($yearlySales)) !!},
            borderColor: '#28a745',
            fill: false
        }]
    },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                       ticks: {
    callback: function(value) {
        return value.toLocaleString() + ' VND';
    }
}

                    }
                }
            }
        });
    }
});
</script>
@endpush