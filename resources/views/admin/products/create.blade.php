@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Thêm sản phẩm mới</h3>

    <form action="{{ route('admin.san-pham.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Tên sản phẩm -->
        <div class="form-group">
            <label for="name">Tên sản phẩm:</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <!-- Mô tả -->
        <div class="form-group">
            <label for="description">Mô tả:</label>
            <textarea class="form-control" name="description" rows="4"></textarea>
        </div>

        <!-- Giá -->
        <div class="form-group">
            <label for="price">Giá bán (VNĐ):</label>
            <input type="number" class="form-control" name="price" min="0" required>
        </div>

        <!-- Danh mục -->
        <div class="form-group">
            <label for="category_id">Danh mục:</label>
            <select class="form-control" name="category_id" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Ảnh đại diện -->
        <div class="form-group">
            <label for="image">Ảnh đại diện:</label>
            <input type="file" class="form-control-file" name="image">
        </div>

        <!-- Hiển thị & Tiêu biểu -->
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" name="pho_bien" value="1">
            <label class="form-check-label">Hiển thị sản phẩm phổ biến</label>
        </div>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" name="noi_bat" value="1">
            <label class="form-check-label">Sản phẩm nổi bật</label>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
        <a href="{{ route('admin.san-pham.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
