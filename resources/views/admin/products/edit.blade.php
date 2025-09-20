@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        
            <h4> Chỉnh sửa sản phẩm</h4>
        
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.san-pham.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Hình ảnh hiện tại --}}
                <div class="mb-3 text-center">
                    <label class="form-label fw-bold">Ảnh hiện tại</label><br>
                    @if ($product->image_url)
                        <img src="{{ asset('images/' . $product->image_url) }}" alt="Ảnh hiện tại" class="preview-image" id="current-preview">
                    @else
                        <p><em>Không có hình</em></p>
                    @endif
                </div>

                {{-- Chọn ảnh mới --}}
                <div class="mb-3">
                    <label for="image" class="form-label">Chọn ảnh mới (nếu muốn thay)</label>
                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tên sản phẩm --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="{{ old('name', $product->name) }}" required>
                </div>

                {{-- Slug --}}
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" name="slug" id="slug" class="form-control"
                        value="{{ old('slug', $product->slug) }}" required>
                </div>

                {{-- Mô tả --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea name="description" id="description" class="form-control"
                        rows="3" required>{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="mb-3">
    <label for="price" class="form-label">Giá (VNĐ)</label>
    <input type="number" name="price" id="price" class="form-control"
        value="{{ old('price', $product->price) }}" required>
</div>
               <div class="row mb-3">
    {{-- Giới tính --}}
    <div class="col-md-6">
        <label for="gioi_tinh" class="form-label">Giới tính</label>
        <select name="gioi_tinh" id="gioi_tinh" class="form-select" required>
            <option value="Nam" {{ old('gioi_tinh', $product->gioi_tinh) == 'Nam' ? 'selected' : '' }}>Nam</option>
            <option value="Nữ" {{ old('gioi_tinh', $product->gioi_tinh) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
            <option value="Unisex" {{ old('gioi_tinh', $product->gioi_tinh) == 'Unisex' ? 'selected' : '' }}>Unisex</option>
        </select>
    </div>

    {{-- Danh mục --}}
    <div class="col-md-6">
        <label for="category_id" class="form-label">Danh mục</label>
        <select name="category_id" id="category_id" class="form-select" required>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>


                {{-- Checkbox nổi bật và phổ biến --}}
                <div class="mb-3">
    <label class="form-label fw-bold">Hiển thị sản phẩm</label>
    <div class="row">
        <div class="col-md-6">
            <div class="form-check form-switch">
                <input type="checkbox" name="noi_bat" id="noi_bat" class="form-check-input"
                    {{ old('noi_bat', $product->noi_bat) ? 'checked' : '' }}>
                <label for="noi_bat" class="form-check-label"> Nổi bật</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-check form-switch">
                <input type="checkbox" name="pho_bien" id="pho_bien" class="form-check-input"
                    {{ old('pho_bien', $product->pho_bien) ? 'checked' : '' }}>
                <label for="pho_bien" class="form-check-label">Phổ biến</label>
            </div>
        </div>
    </div>
</div>

                {{-- Nút --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success">💾 Lưu</button>
                    <a href="{{ route('admin.san-pham.index') }}" class="btn btn-secondary">❌ Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tự động tạo slug từ tên
    document.getElementById('name').addEventListener('input', function () {
        const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    });

    // Preview ảnh
    document.getElementById('image').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                let img = document.getElementById('current-preview');
                if (!img) {
                    img = document.createElement('img');
                    img.className = 'preview-image mt-2';
                    img.id = 'current-preview';
                    document.querySelector('#image').parentNode.appendChild(img);
                }
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

@endsection
<style>
    .preview-image {
        max-width: 150px;
        max-height: 150px;
        object-fit: cover;
        margin-top: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }
</style>
