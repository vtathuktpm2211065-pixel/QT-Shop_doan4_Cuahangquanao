@extends('layouts.admin')

@section('content')
<div class="container">
    <h4 class="mb-3">🎟️ Danh sách Voucher</h4>

    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-success mb-3">➕ Thêm Voucher</a>

    <table class="table table-bordered">
        <thead>
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
    @foreach($vouchers as $voucher)
    <tr>
        <td>{{ $voucher->code }}</td>
        <td>{{ number_format($voucher->discount_value) }}</td>
        <td>
            {{ $voucher->discount_type === 'percent' ? 'Phần trăm (%)' : 'Tiền mặt (VNĐ)' }}
        </td>
        <td>{{ number_format($voucher->min_order_amount) }}</td>
        <td>{{ number_format($voucher->order_amount) }}</td>
     

        <td>
            {{ $voucher->start_date ? \Carbon\Carbon::parse($voucher->start_date)->format('d/m/Y') : 'Không rõ' }}
            -
            {{ $voucher->expires_at ? \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y') : 'Không hết hạn' }}
        </td>
        <td>{{ $voucher->used_orders_count }} / {{ $voucher->usage_limit }}</td>

        <td>{{ $voucher->isActive() ? '✅' : '❌' }}</td>
        <td>
            <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-sm btn-warning">Sửa</a>
            <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa voucher này?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Xóa</button>
            </form>
        </td>
    </tr>
    @endforeach
</tbody>

    </table>

    {{ $vouchers->links() }}
</div>
@endsection
