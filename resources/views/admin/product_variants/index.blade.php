@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <h4 class="mb-3">🧬 Biến thể của sản phẩm: <strong>{{ $product->name }}</strong></h4>

    {{-- Form thêm biến thể --}}
    <div class="card mb-4">
        <div class="card-header">➕ Thêm biến thể mới</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.san-pham.bien-the.store', $product->id) }}">

                @csrf
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="color" class="form-control" placeholder="Màu sắc" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="size" class="form-control" placeholder="Size" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="price" class="form-control" placeholder="Giá" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="stock_quantity" class="form-control" placeholder="Số lượng" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-success btn-sm">Lưu biến thể</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Danh sách biến thể --}}
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                  <th><input type="checkbox" id="check-all"></th>
                <th>STT</th>
                <th>Màu sắc</th>
                <th>Size</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tác vụ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $index => $variant)
            <tr>
                 <td><input type="checkbox" name="variant_ids[]" value="{{ $variant->id }}"></td>
                <td>{{ $index + 1 }}</td>
                <td>{{ $variant->color }}</td>
                <td>{{ $variant->size }}</td>
                <td>{{ number_format($variant->price, 0, ',', '.') }} VNĐ</td>
                <td>{{ $variant->stock_quantity }}</td>
                <td>
                    <a href="{{ route('admin.bien-the.edit', $variant->id) }}" class="btn btn-sm btn-warning">✏️ Sửa</a>


                    {{-- Xoá --}}
                    <form action="{{ route('admin.bien-the.destroy', $variant->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Xoá biến thể này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">🗑️ Xoá</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<a href="{{ route('admin.product_variants.index') }}" class="btn btn-sm btn-secondary mb-3">⬅️ Quay về danh sách tất cả biến thể</a>
@endsection
