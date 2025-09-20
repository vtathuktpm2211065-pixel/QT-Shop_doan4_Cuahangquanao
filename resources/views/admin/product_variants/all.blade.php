@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <h4 class="mb-3">📦 Danh sách tất cả biến thể sản phẩm</h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>STT</th>
                <th>Sản phẩm</th>
                <th>Màu</th>
                <th>Size</th>
                <th>Giá</th>
                <th>Số lượng</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $index => $variant)
            <tr>
                <td>{{ $index + 1 }}</td>
               <td>
  <a href="{{ route('admin.san-pham.bien-the', $variant->product->id) }}">
    {{ $variant->product->name ?? '[Không có sản phẩm]' }}
  </a>
</td>
                <td>{{ $variant->color }}</td>
                <td>{{ $variant->size }}</td>
                <td>{{ number_format($variant->price, 0, ',', '.') }} VNĐ</td>
                <td>{{ $variant->stock_quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
