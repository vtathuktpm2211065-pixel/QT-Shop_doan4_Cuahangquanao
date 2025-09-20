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
    <h4 class="mb-4">🎟️ Tạo mã giảm giá</h4>

    <form action="{{ route('admin.vouchers.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Mã voucher</label>
            <input type="text" name="code" class="form-control" placeholder="Ví dụ: NEWUSER10" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" placeholder="Mô tả về voucher" rows="2"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Loại giảm giá</label>
            <select name="discount_type" class="form-select" required>
                <option value="percent">Phần trăm (%)</option>
                <option value="fixed">Tiền cố định (VNĐ)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá trị giảm</label>
            <input type="number" name="discount_value" class="form-control" >
        </div>

        <div class="mb-3">
            <label class="form-label">Giá trị đơn tối thiểu (VNĐ)</label>
            <input type="number" name="min_order_amount" class="form-control" >
        </div>
<div class="form-group">
    <label for="order_amount">Tổng tiền đơn hàng đã áp dụng (order_amount)</label>
   <input type="number" step="0.01" class="form-control" name="order_amount" value="{{ old('order_amount', 0) }}">

</div>

        <div class="mb-3">
            <label class="form-label">Số lượng sử dụng tối đa</label>
            <input type="number" name="usage_limit" class="form-control" >
        </div>

        <div class="mb-3">
            <label class="form-label">Chỉ áp dụng cho khách hàng mới?</label>
            <select name="only_new_users" class="form-select">
                <option value="0">❌ Không</option>
                <option value="1">✅ Có</option>
            </select>
        </div>
<div class="form-group">
    <label for="start_date">Ngày bắt đầu</label>
    <input type="date" class="form-control" name="start_date" id="start_date" value="{{ old('start_date') }}">
</div>

        <div class="mb-3">
            <label class="form-label">Thời gian hết hạn</label>
            <input type="date" name="expires_at" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select" required>
                <option value="1" selected>Hoạt động</option>
                <option value="0">Không hoạt động</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">💾 Tạo voucher</button>
        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">🔙 Quay lại</a>
    </form>
</div>
@endsection
