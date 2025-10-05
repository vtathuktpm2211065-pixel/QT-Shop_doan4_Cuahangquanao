<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    
public function index(Request $request)
{
    $query = User::query()->withCount('orders');

    // Chỉ lấy khách hàng (không lấy admin, staff)
    $query->whereDoesntHave('roles', function($q) {
        $q->whereIn('name', ['admin', 'staff']);
    });

    // Tìm theo tên hoặc email
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
        });
    }

    // Lọc theo số đơn hàng tối thiểu
    if ($request->filled('min_orders')) {
        $query->has('orders', '>=', (int) $request->min_orders);
    }

    $customers = $query->orderBy('created_at','desc')->paginate(10);

    return view('admin.customers.index', compact('customers'));
}

    public function show($id)
    {
        $customer = User::with([
            'orders',
            'carts.cartItems.productVariant.product'
        ])->findOrFail($id);

        
        $totalOrders     = $customer->orders->count();
        $successOrders   = $customer->orders->where('status', 'success')->count();
        $cancelOrders    = $customer->orders->where('status', 'cancel')->count();
        $pendingOrders   = $customer->orders->where('status', 'pending')->count();

        return view('admin.customers.show', compact(
            'customer',
            'totalOrders',
            'successOrders',
            'cancelOrders',
            'pendingOrders'
        ));
    }
}
