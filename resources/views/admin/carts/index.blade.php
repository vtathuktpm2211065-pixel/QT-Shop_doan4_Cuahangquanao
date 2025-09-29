@extends('layouts.admin')  

@section('content') 
<div class="container-fluid">     
    <h4 class="mb-4">🛒 Quản lý giỏ hàng của khách hàng</h4>

    <!-- Thanh tìm kiếm và lọc -->     
    <form method="GET" class="d-flex mb-4">         
        <input type="text" name="search" class="form-control me-2" placeholder="Tìm theo tên, Email hoặc mã giỏ hàng..." value="{{ request('search') }}">         
        <button class="btn btn-primary">Tìm</button>     
    </form>

    <!-- Thống kê -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title">🛒 Tổng số giỏ hàng</h5>
                <p class="display-6 fw-bold">{{ $totalCarts }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card bg-warning text-dark shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title">⏳ Đang hoạt động</h5>
                <p class="display-6 fw-bold">{{ $active }}</p>
            </div>
        </div>
    </div>
   
    <div class="col-md-4 mb-3">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title">💰 Giá trị TB giỏ hàng</h5>
                <p class="display-6 fw-bold">{{ number_format($avgValue * 1, 0, ',', '.') }}₫</p>
            </div>
        </div>
    </div>
</div>

<div class="card-header text-center fw-bold" style="font-size: 2rem; color: #000; background-color: #f7f8f7ff;">
    📋 Danh sách giỏ hàng
</div>


    <!-- Bảng danh sách giỏ hàng -->     
   <div class="card shadow-sm">
    
        <div class="card-body p-0">
            <table class="table table-bordered table-hover m-0">
                <thead class="table-primary text-center align-middle">
                    <tr class="text-center">
                        <th>Mã giỏ hàng</th>                 
                        <th>Khách hàng</th>  
                        <th>SĐT</th>               
                        <th>Email</th>                 
                        <th>Tổng tiền</th>                 
                        <th>Sản phẩm</th>                 
                        <th>Ngày tạo</th>                 
                        <th>Hành động</th> 
                    </tr>
                </thead>
                <tbody>
                    @forelse ($carts as $cart)
                        <tr class="text-center align-middle">
                            <td>GH{{ str_pad($cart->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $cart->order->full_name ?? $cart->user->name ?? 'Không rõ' }}</td>
                          <td>{{ optional($cart->order)->phone_number ?? optional($cart->user)->phone ?? 'Không rõ' }}</td>



                            <td>{{ $cart->user->email ?? 'Chưa có' }}</td>
                            <td class="text-end">
                                {{ number_format($cart->items->sum(function($item) {         
                                    $price = $item->price ?? $item->variant->price ?? 0;         
                                    return $price * $item->quantity * 1;     
                                }), 0, ',', '.') }}₫
                            </td>
                            <td>{{ $cart->items->count() }}</td>
                            <td>{{ $cart->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.carts.show', $cart->id) }}" class="btn btn-info btn-sm">Xem</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Không có dữ liệu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>
</div> 
@endsection
