@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-dark">
            <h4 class="mb-0"><i class="fas fa-edit"></i> Sửa danh mục</h4>
        </div>
        <div class="card-body">

            {{-- Hiển thị lỗi --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf 
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" 
                           class="form-control" 
                           value="{{ old('name', $category->name) }}" required placeholder="Nhập tên danh mục">
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug (đường dẫn)</label>
                    <input type="text" name="slug" id="slug" 
                           class="form-control" 
                           value="{{ old('slug', $category->slug) }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea name="description" id="description" class="form-control" rows="4" placeholder="Mô tả danh mục">{{ old('description', $category->description) }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('name').addEventListener('input', function() {
    let slug = this.value.toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
    document.getElementById('slug').value = slug;
});
</script>
@endsection
