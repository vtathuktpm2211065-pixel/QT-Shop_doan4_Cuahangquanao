@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">✏️ Sửa biến thể sản phẩm: <strong>{{ $variant->product->name ?? '' }}</strong></h4>

    {{-- Hiện thông báo lỗi nếu có --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.bien-the.update', $variant->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="color" class="form-label">Màu sắc</label>
            <input type="text" id="color" name="color" class="form-control" value="{{ old('color', $variant->color) }}" required>
        </div>

        <div class="mb-3">
            <label for="size" class="form-label">Size</label>
            <input type="text" id="size" name="size" class="form-control" value="{{ old('size', $variant->size) }}" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Giá (VNĐ)</label>
            <input type="number" id="price" name="price" class="form-control" value="{{ old('price', $variant->price) }}" >
        </div>

        <div class="mb-3">
            <label for="stock_quantity" class="form-label">Số lượng</label>
            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $variant->stock_quantity) }}" required min="0">
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật biến thể</button>
        <a href="{{ route('admin.san-pham.bien-the', $variant->product_id) }}" class="btn btn-secondary">⬅️ Quay lại danh sách biến thể</a>
    </form>
</div>
@endsection
