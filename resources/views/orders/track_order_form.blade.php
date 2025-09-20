@extends('app')

@section('title', 'Tra cứu đơn hàng')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">🔍 Tra cứu đơn hàng</h3>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('guest.track_order') }}">
    @csrf

    <div class="mb-3">
        <label>Họ tên khách hàng <span class="text-danger">*</span></label>
        <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
    </div>

    <div class="mb-3">
        <label>Số điện thoại <span class="text-danger">*</span></label>
        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}" required>
    </div>

    <small class="text-muted">* Nhập chính xác họ tên và số điện thoại để tra cứu đơn hàng.</small>
    <br><br>
    <button class="btn btn-primary">Tra cứu</button>
</form>

</div>
@endsection
