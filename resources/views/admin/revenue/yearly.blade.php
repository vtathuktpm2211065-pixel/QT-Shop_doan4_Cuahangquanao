@extends('layouts.admin')

@section('content')
<div class="container-fluid pb-5">
  <h3 class="mb-4">📊 Báo cáo doanh thu năm {{ $year }}</h3>

  <form method="GET" class="mb-4">
    <label for="year">Chọn năm:</label>
    <input type="number" name="year" id="year" value="{{ $year }}" class="form-control d-inline-block w-auto">
    <button class="btn btn-primary ms-2">Xem báo cáo</button>
  </form>

  <div class="alert alert-info">
    <strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 3, ',', '.') }} đ
  </div>

  <!-- Biểu đồ dây doanh thu theo tháng -->
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title">📈 Biểu đồ doanh thu theo từng tháng</h5>
    </div>
    <div class="card-body">
      <canvas id="yearlyLineChart" height="100"></canvas>
    </div>
  </div>

<div class="row mb-4">
  <!-- Biểu đồ số lượng bán -->
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title">Số lượng sản phẩm bán ra</h5>
      </div>
      <div class="card-body">
        <canvas id="productQuantityChart" height="200"></canvas>
      </div>
    </div>
  </div>

  <!-- Biểu đồ doanh thu theo sản phẩm -->
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title">Doanh thu theo sản phẩm</h5>
      </div>
      <div class="card-body">
        <canvas id="productRevenueChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>


  <!-- Bảng tổng hợp sản phẩm bán ra -->
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title">🗂 Tổng hợp sản phẩm đã bán trong năm</h5>
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
            <td>{{ number_format($product['price'], 3, ',', '.') }} đ</td>
            <td>{{ $product['sold'] }}</td>
            <td>{{ number_format($product['revenue'], 3, ',', '.') }} đ</td>
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
  // Biểu đồ dây doanh thu theo từng tháng
  const lineCtx = document.getElementById('yearlyLineChart').getContext('2d');
  new Chart(lineCtx, {
    type: 'line',
    data: {
      labels: @json($chartLabels), // ["Tháng 1", "Tháng 2", ...]
      datasets: [{
        label: 'Doanh thu theo tháng (VNĐ)',
        data: @json($chartRevenue), // [1000000, 2000000, ...]
        borderColor: '#0d6efd',
        backgroundColor: 'rgba(13,110,253,0.1)',
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
            callback: value => value.toLocaleString() + ' đ'
          }
        }
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: ctx => `${ctx.parsed.y.toLocaleString()} đ`
          }
        }
      }
    }
  });

 // Biểu đồ số lượng sản phẩm bán ra
const quantityCtx = document.getElementById('productQuantityChart').getContext('2d');
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
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});

// Biểu đồ doanh thu theo sản phẩm
const revenueCtx = document.getElementById('productRevenueChart').getContext('2d');
new Chart(revenueCtx, {
  type: 'bar',
  data: {
    labels: @json($quantityChartLabels), // cùng nhãn
    datasets: [{
      label: 'Doanh thu (VNĐ)',
      data: @json($productRevenueData), // bạn cần truyền biến này từ Controller
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
          label: ctx => ctx.parsed.y.toLocaleString() + ' đ'
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: value => value.toLocaleString() + ' đ'
        }
      }
    }
  }
});

</script>
@endsection
