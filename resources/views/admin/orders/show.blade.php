@extends('layouts.admin')

@section('content')
<div class="container">
    <h4 class="mb-3">📄 Chi tiết đơn hàng #{{ $order->id }}</h4>

    <p><strong>Khách hàng:</strong> {{ $order->full_name }}</p>
    <p><strong>SĐT:</strong> {{ $order->phone_number }}</p>
    <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
    <p><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</p>
    <p><strong>Trạng thái:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Phương thức thanh toán:</strong> {{ strtoupper($order->payment_method) }}</p>

  @php
    // Tổng trước khi giảm: total_amount (đã trừ) + giảm giá => để hiển thị
    $totalBeforeDiscount = $order->total_amount + ($order->discount_amount ?? 0);

    // Tổng thanh toán thực sự: đã bao gồm phí ship, đã trừ voucher
    $totalAfterDiscount = $totalBeforeDiscount - ($order->discount_amount ?? 0) + ($order->shipping_fee ?? 0);
@endphp

<p><strong>💵 Tổng tiền sản phẩm:</strong> {{ number_format($totalBeforeDiscount, 0, ',', '.') }} VNĐ</p>

@if ($order->voucher_code)
    <p><strong>🎟️ Voucher:</strong> {{ $order->voucher_code }}</p>
    <p><strong>🧾 Giảm giá:</strong> -{{ number_format($order->discount_amount, 0, ',', '.') }} VNĐ</p>
@endif

<p><strong>🚚 Phí vận chuyển:</strong> {{ number_format($order->shipping_fee, 0, ',', '.') }} VNĐ</p>

<p><strong>🧮 Tổng thanh toán:</strong> 
    <span class="text-danger fw-bold">
        {{ number_format($totalAfterDiscount, 0, ',', '.') }} VNĐ
    </span>
</p>

    <h5 class="mt-4">📦 Sản phẩm trong đơn</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên SP</th>
                <th>Size</th>
                <th>Màu</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</td>
                    <td>{{ $item->size }}</td>
                    <td>{{ $item->color }}</td>
                    <td>{{ number_format($item->unit_price, 0, ',', '.') }} VNĐ</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->total_price, 0, ',', '.') }} VNĐ</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">⬅ Quay lại</a>
</div>
@endsection
