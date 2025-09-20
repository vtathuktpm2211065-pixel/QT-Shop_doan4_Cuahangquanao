@extends('admin.layouts.app')

@section('title', 'Doanh thu xuất kho theo ngày')

@section('content')
<div class="container">
    <h1>Doanh thu xuất kho ngày {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h1>

    <form method="GET" class="mb-3">
        <label for="date">Chọn ngày:</label>
        <input type="date" id="date" name="date" value="{{ $date }}">
        <button type="submit" class="btn btn-primary btn-sm">Xem</button>
    </form>

    <h3>Tổng doanh thu: <strong>{{ number_format($totalRevenue) }} VNĐ</strong></h3>

    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Sản phẩm</th>
                <th>Màu</th>
                <th>Size</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th>Loại</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($revenues as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                    <td>{{ $item->variant->product->name ?? 'N/A' }}</td>
                    <td>{{ $item->variant->color ?? 'N/A' }}</td>
                    <td>{{ $item->variant->size ?? 'N/A' }}</td>
                    <td>{{ number_format($item->price) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->total) }}</td>
                    <td>{{ ucfirst($item->type) }}</td>
                    <td>{{ $item->note ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Không có dữ liệu doanh thu.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
