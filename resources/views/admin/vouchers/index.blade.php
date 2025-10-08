@extends('layouts.admin')

@section('content')

<div class="container">
    <h4 class="mb-3">🎟️ Danh sách Voucher</h4>

    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-success mb-3">➕ Thêm Voucher</a>
<form method="GET" action="{{ route('admin.vouchers.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="code" class="form-control" placeholder="Tìm theo mã voucher" value="{{ request('code') }}">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary w-100">Xóa lọc</a>
    </div>
</form>

    <table class="table table-striped table-hover table-bordered align-middle text-center">
   <thead class="table-primary text-center">
        <tr>
            <th>Mã</th>
            <th>Giảm (VNĐ)</th>
            <th>Loại giảm</th>
            <th>Đơn tối thiểu</th>
            <th>Đơn tối đa</th>
            <th>Thời gian</th>
            <th>Số lần dùng</th>
            <th>Hiệu lực</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vouchers as $voucher)
        <tr>
            <td class="fw-bold text-primary">{{ $voucher->code }}</td>
            <td>{{ number_format($voucher->discount_value) }}</td>
            <td>{{ $voucher->discount_type === 'percent' ? 'Phần trăm (%)' : 'Tiền mặt (VNĐ)' }}</td>
            <td>{{ number_format($voucher->min_order_amount) }}</td>
            <td>{{ number_format($voucher->order_amount) }}</td>
            <td>
                {{ $voucher->start_date ? \Carbon\Carbon::parse($voucher->start_date)->format('d/m/Y') : 'Không rõ' }}
                -
                {{ $voucher->expires_at ? \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y') : 'Không hết hạn' }}
            </td>
            <td>{{ $voucher->used_orders_count }} / {{ $voucher->usage_limit }}</td>
            <td class="{{ $voucher->isActive() ? 'text-success' : 'text-danger' }}">
                {{ $voucher->isActive() ? '✅' : '❌' }}
            </td>
            <td>
                <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-sm btn-warning">Sửa</a>
                <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa voucher này?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Xóa</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center">Không có voucher nào.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $vouchers->withQueryString()->links() }}
</div>

@endsection
