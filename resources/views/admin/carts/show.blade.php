@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">🧾 Chi tiết giỏ hàng #GH{{ str_pad($cart->id, 5, '0', STR_PAD_LEFT) }}</h4>

    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            🧑 Thông tin khách hàng
        </div>
        <div class="card-body">
          <p><strong>Họ tên:</strong> {{ $cart->user?->name ?? 'Không xác định' }}</p>
<p><strong>Email:</strong> {{ $cart->user?->email ?? 'Không xác định' }}</p>

            <p><strong>Trạng thái:</strong>
                @if ($cart->status === 'active')
                    <span class="badge bg-warning">Đang chờ</span>
                @elseif ($cart->status === 'completed')
                    <span class="badge bg-success">Đã thanh toán</span>
                @else
                    <span class="badge bg-danger">Bị hủy</span>
                @endif
            </p>
            <p><strong>Ngày tạo:</strong> {{ $cart->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            📦 Sản phẩm trong giỏ hàng
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered m-0 align-middle">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Biến thể</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tạm tính</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cart->items as $item)
                        <tr class="text-center">
                            <td style="width: 100px">
                                <img src="{{ asset('images/' . $item->product->image_url) }}" alt="{{ $item->product->name }}" class="img-fluid rounded" style="max-height: 80px">
                            </td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->variant->color ?? '-' }} / {{ $item->variant->size ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price ?? $item->product->price, 0, '.', ',') }}₫</td>
                            <td>{{ number_format(($item->price ?? $item->product->price) * $item->quantity, 0, '.', ',') }}₫</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Không có sản phẩm nào trong giỏ hàng.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-end mt-3">
        <h5><strong>Tổng cộng:</strong></h5>
        <p>
            <span>Tổng sản phẩm: </span>
            <span>{{ number_format($cart->total_amount, 0, ',', '.') }}₫</span>
        </p>
        <p>
            <span>Phí vận chuyển: </span>
            <span>{{ number_format($cart->shipping_fee ?? 0, 0, ',', '.') }}₫</span>
        </p>
        <h5>
            <strong>Tổng thanh toán: </strong>
            <span class="text-danger">
                {{ number_format($cart->total_amount + ($cart->shipping_fee ?? 0), 0, ',', '.') }}₫
            </span>
        </h5>
    </div>

    <a href="{{ route('admin.carts.index') }}" class="btn btn-outline-secondary float-end">← Quay lại danh sách</a>
</div>
@endsection
