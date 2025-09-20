@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">📦 Quản lý đơn hàng</h4>

    <!-- Thanh tìm kiếm và lọc -->
    <form method="GET" class="d-flex mb-3">
        <input type="text" name="search" class="form-control me-2" placeholder="Tìm theo tên, SĐT hoặc mã đơn..." value="{{ request('search') }}">
        <select name="status" class="form-select me-2">
            <option value="">-- Trạng thái --</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
            <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Giao hàng thành công</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
        </select>
        <button class="btn btn-primary">Lọc</button>
    </form>

    <!-- Bảng danh sách đơn hàng -->
   <table class="table table-bordered table-hover">
    <thead>
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
            <tr>
                <td>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $order->full_name }}</td>
                <td>{{ $order->phone_number }}</td>
                <td>{{ number_format($order->shipping_fee) }}₫</td>
                <td>{{ number_format($order->total_amount) }}₫</td>
                
                <td>{{ strtoupper($order->payment_method) }}</td>

                {{-- Cột trạng thái --}}
                <td>
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

                            $statusLabels = [
                                'pending'   => 'Chờ duyệt',
                                'approved'  => 'Đã duyệt',
                                'shipping'  => 'Đang giao hàng',
                                'delivered' => 'Giao hàng thành công',
                                'cancelled' => 'Đã hủy',
                            ];

                            $allowedNext = $statusTransitions[$order->status] ?? [];
                        @endphp

                        @if(count($allowedNext))
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option selected disabled>{{ $statusLabels[$order->status] ?? 'Không xác định' }}</option>
                                @foreach($allowedNext as $next)
                                    <option value="{{ $next }}">{{ $statusLabels[$next] }}</option>
                                @endforeach
                            </select>
                        @else
                            <span class="badge bg-secondary">{{ $statusLabels[$order->status] ?? 'Không xác định' }}</span>
                        @endif
                    </form>
                </td>
<td>{{ $order->created_at->format('d/m/Y') }}</td>

                {{-- Cột hành động --}}
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">Xem</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

   
</div>
@endsection
