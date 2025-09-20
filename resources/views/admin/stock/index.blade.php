@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3>📦 Quản lý kho</h3>
<div class="btn-group-actions" style="display:flex; gap:10px; justify-content: flex-end;gap: 10px;margin-bottom: 20px;">
    <a href="{{ route('admin.stock.import') }}" class="btn btn-primary"> Nhập kho</a>
    <a href="{{ route('admin.stock.export') }}" class="btn btn-secondary"> Xuất kho</a>
    <a href="{{ route('admin.stock.sync') }}" class="btn btn-success"> Đồng bộ kho</a>
</div>

    {{-- Form lọc --}}
    <form method="GET" action="{{ route('admin.stock.index') }}" class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
            <label class="form-label">Tìm kiếm theo tên, màu, size</label>
            <input type="text" name="keyword" class="form-control form-control-sm" placeholder="VD: Bộ đồ bà ba/đen.m" value="{{ request('keyword') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Danh mục</label>
            <select name="category_id" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

       <div class="col-md-3 d-flex justify-content-between">
    <button type="submit" class="btn btn-outline-primary btn-sm mb-1">🔍 Tìm kiếm</button>
    <a href="{{ route('admin.stock.export') }}" class="btn btn-outline-secondary btn-sm mb-1">❌ Xóa bộ lọc</a>
</div>

    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Phân loại (Màu / Size)</th>
                <th>Tồn kho</th>
                <th>Cập nhật gần nhất</th>
            </tr>
        </thead>
        <tbody>
            @forelse($variants as $variant)
            <tr>
                <td>{{ $variant->product->name }}</td>
                <td>{{ $variant->color }} / {{ $variant->size }}</td>
                <td>{{ $variant->stock_quantity }}</td>
                <td>{{ $variant->updated_at ? $variant->updated_at->format('d/m/Y H:i') : 'Chưa có' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Không có dữ liệu phù hợp</td>
            </tr>
            @endforelse
        </tbody>
    </table>


</div>
@endsection
