@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">👤 Chi tiết khách hàng</h2>

    <!-- Thông tin khách hàng -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h4 class="card-title">{{ $customer->name }}</h4>
            <br></br><p><i class="fas fa-envelope text-primary"></i> <strong>Email:</strong> {{ $customer->email }}</p>
            <p><i class="fas fa-phone text-success"></i> <strong>Số điện thoại:</strong> {{ $customer->phone ?? 'Chưa có' }}</p>
            <p><i class="fas fa-calendar text-warning"></i> <strong>Ngày tạo:</strong> {{ $customer->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Thống kê -->
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="text-muted">Tổng đơn</h5>
                    <h3 class="fw-bold">{{ $totalOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background:#d4edda;">
                <div class="card-body">
                    <h5 class="text-muted">Thành công</h5>
                    <h3 class="fw-bold text-success">{{ $successOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background:#fff3cd;">
                <div class="card-body">
                    <h5 class="text-muted">Đang xử lý</h5>
                    <h3 class="fw-bold text-warning">{{ $pendingOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background:#f8d7da;">
                <div class="card-body">
                    <h5 class="text-muted">Đã hủy</h5>
                    <h3 class="fw-bold text-danger">{{ $cancelOrders }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Giỏ hàng -->
   <div class="card mb-4 shadow-sm">
    <div class="card-header bg-info text-white">
        <i class="fas fa-shopping-cart"></i> Giỏ hàng hiện tại
    </div>
    <div class="card-body">
        @if($customer->carts->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Màu</th>
                            <th>Size</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer->carts as $cart)
                            @foreach($cart->cartItems as $item)
                                <tr>
                                   
                                     <td style="width: 100px">
                                <img src="{{ asset('images/' . $item->product->image_url) }}" alt="{{ $item->product->name }}" class="img-fluid rounded" style="max-height: 80px">
                            </td>

                                    <td class="fw-semibold">
                                        {{ $item->productVariant->product->name }}
                                    </td>
                                    <td>{{ $item->productVariant->color }}</td>
                                    <td>{{ $item->productVariant->size }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end text-danger fw-bold">
                                        {{ number_format($item->price) }} đ
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted">Khách hàng chưa có sản phẩm trong giỏ.</p>
        @endif
    </div>
</div>


    <!-- Lịch sử đơn hàng -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-box"></i> Lịch sử đơn hàng
        </div>
        <div class="card-body">
            @if($customer->orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Hình ảnh</th>
                                <th>Ngày đặt</th>
                                <th class="text-end">Tổng tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                     <td style="width: 100px">
                                <img src="{{ asset('images/' . $item->product->image_url) }}" alt="{{ $item->product->name }}" class="img-fluid rounded" style="max-height: 80px">
                            </td>
                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($order->total_amount) }} đ</td>
                                    <td>
                                        @if($order->status == 'success')
                                            <span class="badge bg-success">Thành công</span>
                                        @elseif($order->status == 'pending')
                                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                                        @elseif($order->status == 'cancel')
                                            <span class="badge bg-danger">Đã hủy</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Khách hàng chưa có đơn hàng nào.</p>
            @endif
        </div>
    </div>
</div>
@endsection
