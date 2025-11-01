@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">📦 Quản lý đơn hàng</h4>

   <div class="card mb-3 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-center filter-bar">
            <!-- Ô tìm kiếm -->
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" 
                       placeholder="🔍 Tìm theo tên, SĐT hoặc mã đơn..." 
                       value="{{ request('search') }}">
            </div>

            <!-- Chọn trạng thái -->
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Giao hàng thành công</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>

            <!-- Nút lọc -->
            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
            </div>
        </form>
    </div>
</div>



    <!-- Bảng danh sách đơn hàng -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-primary text-center align-middle">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>SĐT</th>
                        <th>Phí ship</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @php
                            $statusLabels = [
    'pending'   => ['Chờ duyệt', 'warning'],
    'approved'  => ['Đã duyệt', 'info'],
    'shipping'  => ['Đang giao hàng', 'primary'],
    'delivered' => ['Giao hàng thành công', 'success'],
    'cancelled' => ['Đã hủy', 'danger'],
    'completed' => ['Hoàn thành', 'success'],
   
];

                            $currentStatus = $statusLabels[$order->status] ?? ['Không xác định', 'secondary'];
                        @endphp

                        <tr>
                            <td class="text-center fw-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $order->full_name }}</td>
                            <td>{{ $order->phone_number }}</td>
                            <td class="text-end">{{ number_format($order->shipping_fee) }}₫</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($order->total_amount) }}₫</td>
                            <td class="text-center">{{ strtoupper($order->payment_method) }}</td>

                            {{-- Trạng thái --}}
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                                    @csrf
                                    @method('PATCH')

                                    @php
                                        $statusTransitions = [
                                            'pending'   => ['approved', 'shipping', 'delivered', 'cancelled'],
                                            'approved'  => ['shipping', 'delivered'],
                                            'shipping'  => ['delivered'],
                                            'delivered' => [],
                                            'cancelled' => [],
                                        ];
                                        $allowedNext = $statusTransitions[$order->status] ?? [];
                                    @endphp

                                    @if(count($allowedNext))
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option selected disabled>{{ $currentStatus[0] }}</option>
                                            @foreach($allowedNext as $next)
                                                <option value="{{ $next }}">{{ $statusLabels[$next][0] }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="badge bg-{{ $currentStatus[1] }}">
                                            {{ $currentStatus[0] }}
                                        </span>
                                    @endif
                                </form>
                            </td>

                            <td class="text-center">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
<style>
    .filter-bar .form-control,
    .filter-bar .form-select {
        height: calc(2.5rem + 2px); 
        font-size: 1rem;
    }
</style>
