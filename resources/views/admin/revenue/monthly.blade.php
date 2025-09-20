@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h3 class="mb-4">📈 Báo cáo doanh thu tháng {{ \Carbon\Carbon::parse($month)->format('m/Y') }}</h3>

  <form method="GET" class="mb-4">
    <label for="month">Chọn tháng:</label>
    <input type="month" name="month" id="month" value="{{ $month }}" class="form-control d-inline-block w-auto">
    <button class="btn btn-primary ms-2">Xem báo cáo</button>
  </form>

  <div class="alert alert-info">
    <strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 0, ',', '.') }} đ
  </div>

  <div class="row mb-4">
  <!-- Biểu đồ doanh thu -->
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title">Tỷ trọng doanh thu theo sản phẩm</h5>
      </div>
      <div class="card-body">
        <canvas id="monthlyChart" height="200"></canvas>
      </div>
    </div>
  </div>

  <!-- Biểu đồ số lượng sản phẩm -->
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title">Biểu đồ số lượng sản phẩm bán ra</h5>
      </div>
      <div class="card-body">
        <canvas id="quantityChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>
<!-- Biểu đồ doanh thu theo ngày -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="card-title">📊 Doanh thu theo từng ngày trong tháng</h5>
  </div>
  <div class="card-body">
    <canvas id="dailyChart" height="120"></canvas>
  </div>
</div>

<!-- Bảng doanh thu theo ngày -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="card-title">📅 Bảng thống kê doanh thu từng ngày</h5>
  </div>
  <div class="card-body p-0">
    <table class="table table-bordered mb-0">
      <thead class="table-light">
        <tr>
          <th>Ngày</th>
          <th>Doanh thu</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($dailyRevenueData as $item)
        <tr>
          <td>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
          <td>{{ number_format($item['total'], 0, ',', '.') }} đ</td>
        </tr>
        @empty
        <tr><td colspan="2" class="text-center">Không có dữ liệu.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>


  <!-- Bảng danh sách sản phẩm -->
  <div class="card">
    <div class="card-header">
      <h5 class="card-title">Danh sách sản phẩm bán chạy</h5>
    </div>
    <div class="card-body p-0">
      <table class="table table-bordered mb-0">
        <thead class="table-dark">
          <tr>
            <th>Sản phẩm</th>
            <th>Giá</th>
            <th>Đã bán</th>
            <th>Doanh thu</th>
            <th>Tỷ lệ</th>
            <th>Tồn kho</th>
            <th>Xem</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($topProducts as $product)
          <tr>
            <td>
              <div class="d-flex align-items-center">
                @if ($product['image'])
                  <img src="{{ asset('storage/' . $product['image']) }}" width="40" class="me-2 rounded-circle" alt="product">
                @endif
                {{ $product['name'] }}
              </div>
            </td>
            <td>{{ number_format($product['price'], 0, ',', '.') }} đ</td>
            <td>{{ $product['sold'] }}</td>
            <td>{{ number_format($product['revenue'], 0, ',', '.') }} đ</td>
            <td>{{ $product['percent'] }}%</td>
            <td>{{ $product['stock'] ?? 'Không rõ' }}</td>
            <td>
              <a href="{{ route('chi_tiet', $product['slug']) }}" class="text-primary">
                <i class="fas fa-search"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center">Không có sản phẩm nào.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Biểu đồ doanh thu
  const ctx = document.getElementById('monthlyChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: @json($chartLabels),
      datasets: [{
        label: 'Doanh thu (VNĐ)',
        data: @json($chartRevenue),
        backgroundColor: '#0d6efd',
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(ctx) {
              const index = ctx.dataIndex;
              const sold = @json($chartSold)[index];
              return `Doanh thu: ${ctx.parsed.y.toLocaleString()} đ (x${sold} sản phẩm)`;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return value.toLocaleString() + ' đ';
            }
          }
        }
      }
    }
  });
// Biểu đồ doanh thu theo ngày
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
new Chart(dailyCtx, {
  type: 'line',
  data: {
    labels: @json($dailyLabels),
    datasets: [{
      label: 'Doanh thu',
      data: @json($dailyValues),
      borderColor: '#198754',
      backgroundColor: 'rgba(25, 135, 84, 0.2)',
      fill: true,
      tension: 0.4,
      pointRadius: 4
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function(value) {
            return value.toLocaleString() + ' đ';
          }
        }
      }
    },
    plugins: {
      tooltip: {
        callbacks: {
          label: function(ctx) {
            return `${ctx.parsed.y.toLocaleString()} đ`;
          }
        }
      }
    }
  }
});

  // Biểu đồ số lượng sản phẩm
  const quantityCtx = document.getElementById('quantityChart').getContext('2d');
  new Chart(quantityCtx, {
    type: 'bar',
    data: {
      labels: @json($quantityChartLabels),
      datasets: [{
        label: 'Số lượng bán',
        data: @json($quantityChartData),
        backgroundColor: '#ffc107',
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
@endsection
