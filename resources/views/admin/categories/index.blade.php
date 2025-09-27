@extends('layouts.admin')
@php
    function highlight($text, $search) {
        if (!$search) return $text;
        return preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark>$1</mark>', $text);
    }
@endphp

@section('content')
<div class="container">
    <h2 class="mb-4">Quản lý danh mục</h2>

    <a href="{{ route('categories.create') }}" class="btn bg-success mb-3">
        <i class="fas fa-plus"></i> Thêm danh mục
    </a>
<form action="{{ route('categories.index') }}" method="GET" class="mb-3" style="max-width: 400px;">
    <div class="input-group">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="🔍 Tìm theo tên hoặc slug...">
        <button class="btn btn-primary" type="submit">
            <i class="fas fa-search"></i>
        </button>
    </div>
</form>



    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover">
        <thead class="table-primary text-center align-middle">
    <tr>
        <th class="text-center">#</th>
<th>Tên danh mục</th>
<th>Slug</th>
<th>Mô tả</th>
<th class="text-center">Ngày tạo</th>
<th class="text-center">Ngày cập nhật</th>
<th class="text-center">Số sản phẩm</th>
<th class="text-center">Hành động</th>

    </tr>
</thead>

        <tbody>
            @forelse($categories as $index => $category)
            <tr>
                <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                <td>{{ $category->description ?? '-' }}</td>
                <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                <td>{{ $category->products_count ?? 0 }}</td>
              <td>
    <div class="d-flex justify-content-center gap-1">
        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">
            <i class="fas fa-edit"></i> Sửa
        </a>
        <form action="{{ route('categories.destroy', $category) }}"
              method="POST"
              onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này không?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">
                <i class="fas fa-trash"></i> Xóa
            </button>
        </form>
    </div>
</td>

            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Chưa có danh mục nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $categories->links() }}
    </div>
</div>
@endsection
