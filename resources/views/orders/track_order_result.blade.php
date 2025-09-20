@extends('app')

@section('title', 'Kết quả tra cứu đơn hàng')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">📦 Kết quả tra cứu đơn hàng</h3>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @if($orders->isEmpty())
        <div class="alert alert-warning">Không tìm thấy đơn hàng nào khớp với thông tin.</div>
        <a href="{{ route('guest.track_order_form') }}" class="btn btn-secondary">← Tra cứu khác</a>
    @else
        @foreach($orders as $order)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Đơn hàng #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                    @php
                        $labels = [
                            'pending'   => 'Chờ duyệt',
                            'approved'  => 'Đã duyệt',
                            'shipping'  => 'Đang giao hàng',
                            'delivered' => 'Giao hàng thành công',
                            'cancelled' => 'Đã hủy',
                        ];
                    @endphp
                    <span class="badge bg-info text-dark">
                        {{ $labels[$order->status] ?? ucfirst($order->status) }}
                    </span>
                </div>

                <div class="card-body">
                    <p><strong>Họ tên:</strong> {{ $order->full_name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $order->phone_number }}</p>
                    <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
                    <p><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</p>
                    <p><strong>Phương thức thanh toán:</strong> {{ strtoupper($order->payment_method) }}</p>
                    <p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount) }}₫</p>

                    <h5 class="mt-4">📦 Sản phẩm trong đơn</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Size</th>
                                <th>Màu</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? '—' }}</td>
                                    <td>{{ $item->size }}</td>
                                    <td>{{ $item->color }}</td>
                                    <td>{{ number_format($item->unit_price) }}₫</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->total_price) }}₫</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if(in_array($order->status, ['pending', 'approved']))
                        <form method="POST" action="{{ route('guest.cancel_order', $order->id) }}" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                            @csrf
                            <button type="submit" class="btn btn-danger mt-3">Hủy đơn hàng</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach

        <a href="{{ route('guest.track_order_form') }}" class="btn btn-secondary">← Tra cứu khác</a>
    @endif
</div>
@endsection
