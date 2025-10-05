@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Quản lý khách hàng</h2>
   <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" 
                   placeholder="Tìm theo tên hoặc email..." 
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <input type="number" name="min_orders" class="form-control" 
                   placeholder="Tối thiểu đơn hàng" 
                   value="{{ request('min_orders') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">🔍 Lọc</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary w-100">❌ Reset</a>
        </div>
    </form>

    <table class="table table-bordered">
         <thead class="table-primary text-center">
            <tr>
                <th>Tên</th>
                <th>Email</th>
                <th>Số đơn hàng</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td class="text-center">{{ $customer->orders_count }}</td>
                    <td class="text-center">{{ $customer->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-info btn-sm">Xem</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $customers->links() }}
</div>
@endsection
