@extends('app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">🧾 Chi tiết đơn hàng #{{ $order->id }}</h2>

    @if (session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger text-center">{{ $errors->first() }}</div>
    @endif

    {{-- Thẻ thông tin đơn hàng --}}
    <div class="card shadow rounded-3 border-0">
        <div class="card-header bg-light text-dark fw-bold">
            <i class="bi bi-clipboard-check"></i> Thông tin đơn hàng & sản phẩm
        </div>

        <div class="card-body px-4">
            {{-- Thông tin giao hàng --}}
            <div class="mb-4">
                <p><strong>👤 Người nhận:</strong> {{ $order->full_name }}</p>
                <p><strong>📞 Số điện thoại:</strong> {{ $order->phone_number }}</p>
                <p><strong>📍 Địa chỉ:</strong>
                    {{ $detail ? $detail . ', ' : '' }}
                    {{ $wardName ? $wardName . ', ' : '' }}
                    {{ $districtName ? $districtName . ', ' : '' }}
                    {{ $provinceName }}
                </p>
                <p><strong>💳 Phương thức thanh toán:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                
                {{-- Hiển thị trạng thái thanh toán --}}
                <p><strong>💰 Trạng thái thanh toán:</strong>
                    @if($order->status == 'paid')
                        <span class="badge bg-success">✅ Đã thanh toán</span>
                    @elseif($order->status == 'pending')
                        <span class="badge bg-warning">⏳ Chờ thanh toán</span>
                    @elseif($order->status == 'failed')
                        <span class="badge bg-danger">❌ Thanh toán thất bại</span>
                    @else
                        <span class="badge bg-secondary">💳 Chưa thanh toán</span>
                    @endif
                </p>

                @if($order->transaction_id)
                    <p><strong>🔢 Mã giao dịch:</strong> {{ $order->transaction_id }}</p>
                @endif

                <p><strong>🏷️ Mã giảm giá:</strong> {{ $order->voucher_code ?? 'Không sử dụng' }}</p>

                @php
                    $itemsTotal = 0;
    foreach ($order->orderItems as $item) {
        $itemsTotal += ($item->unit_price * $item->quantity);
    }
    
    $shipping = $order->shipping_fee ?? 0;
    $discount = $order->discount_amount ?? 0;
    $finalTotal = $itemsTotal + $shipping - $discount;
                   
                    $statusLabels = [
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'shipping' => 'Đang giao hàng',
                        'delivered' => 'Giao hàng thành công',
                        'cancelled' => 'Đã hủy',
                       
                    ];

                    $badgeClasses = [
                        'pending' => 'bg-secondary',
                        'approved' => 'bg-warning',
                        'shipping' => 'bg-primary',
                        'delivered' => 'bg-success',
                        'cancelled' => 'bg-danger',
                    
                    ];
                @endphp

                @if($order->discount_amount > 0)
                <div class="d-flex justify-content-between">
                    <span class="text-success">Giảm giá:</span>
                    <span class="text-success">-{{ number_format($order->discount_amount) }} VNĐ</span>
                </div>
                @endif

                <p><strong>💰 Tổng tiền sản phẩm:</strong>
                    <span class="text-danger fw-bold">{{ number_format($itemsTotal, 0, ',', '.') }} VNĐ</span>
                </p>
                <p><strong>🚚 Phí vận chuyển:</strong>
                    <span class="text-danger fw-bold">{{ number_format($shipping, 0, ',', '.') }} VNĐ</span>
                </p>
                @if($discount > 0)
                    <p><strong class="text-success">🔻 Giảm giá:</strong>
                        <span class="text-success">-{{ number_format($discount, 0, ',', '.') }} VNĐ</span>
                    </p>
                @endif

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <p><strong>📦 Trạng thái đơn hàng:</strong>
                        <span class="badge {{ $badgeClasses[$order->status] ?? 'bg-dark' }}">
                            {{ $statusLabels[$order->status] ?? 'Không xác định' }}
                        </span>
                    </p>
                    
                    {{-- Hiển thị tổng tiền cuối cùng --}}
                    <p class="fs-5 fw-bold text-primary">
                        💵 Tổng thanh toán: {{ number_format($finalTotal, 0, ',', '.') }} VNĐ
                    </p>
                </div>
            </div>

            {{-- Danh sách sản phẩm --}}
            <hr>
            <h5 class="mb-3"><i class="bi bi-box-seam"></i> Sản phẩm trong đơn hàng</h5>

            @foreach ($order->orderItems as $item)
                <div class="row align-items-center border-bottom py-3">
                    <div class="col-md-2 col-4 text-center">
                        @if($item->product && $item->product->image_url)
                            <img src="{{ asset('images/' . $item->product->image_url) }}"
                                 alt="{{ $item->product->name }}"
                                 class="img-fluid rounded border"
                                 style="max-height: 80px;">
                        @else
                            <span class="text-muted small">Không có ảnh</span>
                        @endif
                    </div>

                    <div class="col-md-10 col-8">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <div class="fw-bold">{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</div>
                                <div class="small text-muted">
                                    Size: {{ $item->size ?? 'Không chọn' }} |
                                    Màu: {{ ucfirst($item->color ?? 'Không chọn') }} |
                                    SL: {{ $item->quantity }}
                                </div>
                                <div class="mt-1">
                                    <span>Đơn giá:</span>
                                    <span class="text-dark">{{ number_format($item->unit_price, 0, ',', '.') }}₫</span> |
                                    <span class="text-danger fw-bold">Thành tiền: {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }} VNĐ</span>
                                </div>
                            </div>

                            <div class="text-end">
                                @if($item->product)
                                    <a href="{{ route('chi_tiet', $item->product->slug) }}" class="btn btn-sm btn-outline-primary mb-2">
                                        Xem sản phẩm
                                    </a>
                                @endif

                                @if($order->status == 'delivered' && $item->product)
                                    @php
                                        $alreadyReviewed = $item->product->reviews()
                                            ->where('user_id', Auth::id())
                                            ->where('order_id', $order->id)
                                            ->exists();
                                    @endphp

                                    @if(!$alreadyReviewed)
                                        <button class="btn btn-outline-primary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reviewModal-{{ $item->id }}">
                                            Đánh giá
                                        </button>
                                    @else
                                        <a href="{{ route('products.showReviews', $item->product->id) }}"
                                           class="btn btn-outline-success btn-sm">
                                            Xem đánh giá của bạn
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Nút hành động --}}
            <div class="text-end mt-4 d-flex gap-2 justify-content-end">
                <form method="POST" action="{{ route('orders.reorder', $order->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-cart-plus"></i> Mua lại đơn hàng
                    </button>
                </form>

                {{-- Chỉ cho phép hủy đơn hàng nếu chưa thanh toán và ở trạng thái pending/approved --}}
                @if(in_array($order->status, ['pending', 'approved']) && $order->status != 'paid')
                    <form method="POST" action="{{ route('admin.orders.cancel', $order->id) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')">
                            <i class="bi bi-x-circle"></i> Hủy đơn hàng
                        </button>
                    </form>
                @endif

                {{-- Nút thanh toán lại nếu đơn hàng thất bại --}}
                @if($order->status == 'failed')
                    <a href="{{ route('checkout') }}" class="btn btn-primary">
                        <i class="bi bi-credit-card"></i> Thanh toán lại
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Modal đánh giá --}}
@foreach($order->orderItems as $item)
    @if($order->status == 'delivered' && $item->product)
        <div class="modal fade" id="reviewModal-{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data" class="modal-content">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Đánh giá: {{ $item->product->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="product_id" value="{{ $item->product->id }}">

                        <label>⭐ Số sao:</label>
                        <select name="rating" class="form-select mb-3" required>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} sao</option>
                            @endfor
                        </select>

                        <label>📝 Bình luận:</label>
                        <textarea name="comment" class="form-control mb-3"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Gửi đánh giá</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endforeach