@extends('layouts.admin')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="container">
    <h4 class="mb-4">✏️ Cập nhật mã giảm giá</h4>

    <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Mã voucher</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $voucher->code) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description', $voucher->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Loại giảm giá</label>
            <select name="discount_type" class="form-select" required>
                <option value="percent" {{ $voucher->discount_type == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                <option value="fixed" {{ $voucher->discount_type == 'fixed' ? 'selected' : '' }}>Tiền cố định (VNĐ)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá trị giảm</label>
            <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value', $voucher->discount_value) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Giá trị đơn tối thiểu (VNĐ)</label>
            <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $voucher->min_order_amount) }}">
        </div>
<div class="form-group">
    <label for="order_amount">Tổng tiền đơn hàng đã áp dụng (order_amount)</label>
    <input type="number" step="0.01" class="form-control" name="order_amount" value="{{ old('order_amount', $voucher->order_amount ?? 0) }}">
</div>

        <div class="mb-3">
            <label class="form-label">Số lượng sử dụng tối đa</label>
            <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $voucher->usage_limit) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Chỉ áp dụng cho khách hàng mới?</label>
            <select name="only_new_users" class="form-select">
                <option value="0" {{ $voucher->only_new_users == 0 ? 'selected' : '' }}>❌ Không</option>
                <option value="1" {{ $voucher->only_new_users == 1 ? 'selected' : '' }}>✅ Có</option>
            </select>
        </div>

       <div class="form-group">
    <label for="start_date">Ngày bắt đầu</label>
<input type="date" class="form-control" name="start_date" id="start_date" 
    value="{{ old('start_date', $voucher->start_date ? \Carbon\Carbon::parse($voucher->start_date)->format('Y-m-d') : '') }}">

</div>


        <div class="mb-3">
            <label class="form-label">Thời gian hết hạn</label>
            <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at', \Carbon\Carbon::parse($voucher->expires_at)->format('Y-m-d')) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select" required>
                <option value="1" {{ $voucher->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ $voucher->status == 0 ? 'selected' : '' }}>Không hoạt động</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">🔙 Quay lại</a>
    </form>
</div>
@endsection
