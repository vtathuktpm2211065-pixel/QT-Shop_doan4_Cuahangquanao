<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CartReminderNotification;
class AdminCartController extends Controller
{
    // Danh sách giỏ hàng có tìm kiếm và lọc
   public function index(Request $request)
{
    $query = Cart::with(['user', 'items.product', 'items.variant', 'order']);


    // Điều kiện lọc...
    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    } else {
        $query->where('status', 'active');
    }

    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
        });
    }

    $carts = $query->latest()->paginate(10);

    // 👉 Thêm thống kê để dùng trong view
    $totalCarts = Cart::count();
    $active = Cart::where('status', 'active')->count();

   $avgValue = $totalCarts > 0
    ? round(Cart::with('items')->get()->sum(function ($cart) {
        return $cart->items->sum(fn($item) => $item->price * $item->quantity);
    }) / $totalCarts)
    : 0;

    return view('admin.carts.index', compact('carts', 'totalCarts', 'active', 'avgValue'));
}

    // Xem chi tiết giỏ hàng
    public function show($id)
    {
        $cart = Cart::with(['user', 'items.product','items.variant'])->findOrFail($id);
        return view('admin.carts.show', compact('cart'));
    }

 

    // Trang thống kê giỏ hàng
   public function statistics()
{
    $totalCarts = Cart::count();
    $completed  = Cart::where('status', 'completed')->count();
    $shipping   = Cart::where('status', 'shipping')->count(); // ✅ Thêm dòng này
    $active     = Cart::where('status', 'active')->count();
    $cancelled  = Cart::where('status', 'cancelled')->count();

    $totalValue = Cart::with('items')->get()->sum(function ($cart) {
        return $cart->items->sum(fn($item) => $item->price * $item->quantity);
    });

    $avgValue = $totalCarts > 0 ? round($totalValue / $totalCarts) : 0;
    $conversionRate = $totalCarts > 0 ? round($completed / $totalCarts * 100, 2) : 0;

    return view('admin.carts.index', compact(
        'totalCarts', 'completed', 'shipping', 'active', 'cancelled', 'avgValue', 'conversionRate'
    ));
}
}