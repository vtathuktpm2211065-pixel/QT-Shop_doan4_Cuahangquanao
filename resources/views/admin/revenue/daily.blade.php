@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h3 class="mb-4">📊 Báo cáo doanh thu ngày {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>

  <!-- Form chọn ngày -->
  <form method="GET" class="mb-4">
    <label for="date">Chọn ngày:</label>
    <input type="date" name="date" id="date" value="{{ $date }}" class="form-control d-inline-block w-auto">
    <button class="btn btn-primary ms-2">Xem báo cáo</button>
  </form>

  <!-- Tổng doanh thu -->
  <div class="alert alert-info">
    <strong>Tổng doanh thu:</strong> {{ number_format($revenue, 0, ',', '.') }} đ
  </div>

  <!-- Biểu đồ sản phẩm bán chạy -->
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title">Biểu đồ top 5 sản phẩm bán chạy</h5>
    </div>
    <div class="card-body">
      <canvas id="productChart" height="120"></canvas>
    </div>
  </div>

  <!-- Bảng sản phẩm bán chạy -->
  <div class="card">
    <div class="card-header">
      <h5 class="card-title">Top sản phẩm bán chạy trong ngày</h5>
    </div>
    <div class="card-body p-0">
      <table class="table table-bordered table-hover mb-0">
        <thead class="table-dark">
          <tr>
            <th>Sản phẩm</th>
            <th>Giá</th>
            <th>Đã bán</th>
            <th>Biến động</th>
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
            <td>{{ number_format($product['sold']) }}</td>
            <td>
              @if ($product['change_percent'] > 0)
                <span class="text-success">⬆ {{ $product['change_percent'] }}%</span>
              @elseif ($product['change_percent'] < 0)
                <span class="text-danger">⬇ {{ abs($product['change_percent']) }}%</span>
              @else
                <span class="text-secondary">0%</span>
              @endif
            </td>
            <td>
              <a href="{{ route('chi_tiet', $product['slug']) }}" class="text-primary">

                <i class="fas fa-search"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center">Không có sản phẩm nào bán trong ngày này.</td>
          </tr>
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
  const ctx = document.getElementById('productChart').getContext('2d');
  const productChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: @json($chartLabels),
      datasets: [{
        label: 'Số lượng bán',
        data: @json($chartData),
        backgroundColor: '#0d6efd',
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => `Đã bán: ${ctx.parsed.y} sản phẩm`
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 }
        }
      }
    }
  });
</script>
@endsection
