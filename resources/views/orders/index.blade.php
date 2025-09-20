@extends('app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Đơn hàng của bạn</h2>

    @if($orders->isEmpty())
        <p>Bạn chưa có đơn hàng nào.</p>
    @else
        @foreach ($orders as $order)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <strong>Đơn hàng #{{ $order->id }}</strong> <br>
                    Ngày đặt: {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}
                </div>
              @php
    $statusLabels = [
        'pending'   => 'Chờ duyệt',
        'approved'  => 'Đã duyệt',
        'shipping'  => 'Đang giao hàng',
        'delivered' => 'Giao hàng thành công',
        'cancelled' => 'Đã hủy',
    ];

    $statusClasses = [
        'pending'   => 'bg-secondary',
        'approved'  => 'bg-warning text-dark',
        'shipping'  => 'bg-primary',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger',
    ];

    $statusIcons = [
        'pending'   => 'bi-clock',
        'approved'  => 'bi-check-circle',
        'shipping'  => 'bi-truck',
        'delivered' => 'bi-check2-circle',
        'cancelled' => 'bi-x-circle',
    ];
@endphp

<div>
    <span class="badge {{ $statusClasses[$order->status] ?? 'bg-dark' }}">
        <i class="bi {{ $statusIcons[$order->status] ?? 'bi-question-circle' }}"></i>
        {{ $statusLabels[$order->status] ?? 'Không xác định' }}
    </span>
</div>


            </div>
            <div class="card-body">
                @foreach ($order->orderItems as $item)
                <div class="d-flex mb-3 border-bottom pb-3">
                    <div style="width: 80px;">
                        @if($item->product && $item->product->image_url)
                            <img src="{{ asset('images/' . $item->product->image_url) }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="img-fluid rounded" 
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white text-center rounded" style="width: 80px; height: 80px; line-height: 80px;">
                                Không có ảnh
                            </div>
                        @endif
                    </div>
                    <div class="ms-3 w-100">
                        <h6 class="mb-1">{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</h6>
                        <p class="mb-0">
                            Số lượng: {{ $item->quantity }} <br>
                           Đơn giá: {{ number_format($item->unit_price) }} VNĐ <br>
Thành tiền: <strong>{{ number_format($item->total_price) }} VNĐ</strong><br>


                        </p>
                    </div>
                </div>
                @endforeach

                <div class="d-flex justify-content-between">
    <span>Tạm tính:</span>
    <span>{{ number_format($order->total_amount + $order->discount_amount - $order->shipping_fee) }} VNĐ</span>
</div>
@if($order->discount_amount > 0)
<div class="d-flex justify-content-between">
    <span class="text-success">Giảm giá:</span>
    <span class="text-success">-{{ number_format($order->discount_amount) }} VNĐ</span>
</div>
@endif
<div class="d-flex justify-content-between">
    <span>Phí vận chuyển:</span>
    <span>{{ number_format($order->shipping_fee) }} VNĐ</span>
</div>
<hr>
<div class="d-flex justify-content-between align-items-center">
    <strong>Tổng cộng:</strong>
    <strong>{{ number_format($order->total_amount) }} VNĐ</strong>
</div>

@if(in_array($order->status, ['pending', 'approved']))
        <form method="POST" action="{{ route('admin.orders.cancel', $order->id) }}">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')">
                <i class="bi bi-x-circle"></i> Hủy đơn hàng
            </button>
        </form>
    @endif
                <div class="mt-3 text-end">
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection
