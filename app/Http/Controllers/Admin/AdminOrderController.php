<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Voucher; 
class AdminOrderController extends Controller
{
    // Danh sách đơn hàng với tìm kiếm & lọc
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('phone_number', 'like', '%' . $search . '%')
                  ->orWhere('id', $search);
            });
        }

        $orders = $query->latest()->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    // Xem chi tiết đơn hàng
    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'user']);
        return view('admin.orders.show', compact('order'));
    }

public function cancel(Order $order)
{
    if (!in_array($order->status, ['pending', 'approved'])) {
        return back()->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại.');
    }

    DB::transaction(function() use ($order) {
        foreach ($order->orderItems as $item) {
            $variant = $item->variant;

            if (!$variant) {
                // Nếu không có quan hệ, tìm lại bằng product_id, size, color
                $variant = \App\Models\ProductVariant::where('product_id', $item->product_id)
                    ->where('size', $item->size)
                    ->where('color', $item->color)
                    ->first();

                if (!$variant) {
                    \Log::warning("Không tìm thấy variant cho OrderItem ID {$item->id} - product_id: {$item->product_id}, size: {$item->size}, color: {$item->color}");
                    continue;
                }
            }

            // Cộng lại tồn kho
            $variant->stock_quantity += $item->quantity;
            $variant->save();

            \Log::info("Tăng tồn kho: Variant ID {$variant->id}, Số lượng: {$item->quantity}");
        }

        $order->status = 'cancelled';
        $order->save();
    });

    return back()->with('success', 'Đơn hàng đã được hủy và tồn kho đã được cập nhật.');
}



    // Cập nhật trạng thái đơn hàng


public function updateStatus(Request $request, Order $order)
{
    $currentStatus = $order->status;
    $newStatus = $request->input('status');
\Log::info("Yêu cầu cập nhật đơn {$order->id} từ {$currentStatus} => {$newStatus}");

    // Các trạng thái được phép chuyển tiếp từ trạng thái hiện tại
    $allowedTransitions = [
        'pending'   => ['approved', 'shipping', 'delivered', 'cancelled'], // chờ duyệt có thể đi mọi hướng
        'approved'  => ['shipping', 'delivered'], // duyệt rồi thì có thể giao hoặc giao luôn
        'shipping'  => ['delivered'], // chỉ được giao xong
        'delivered' => [], // hoàn thành rồi không đổi nữa
        'cancelled' => [], // đã hủy cũng không đổi nữa
    ];

    // Kiểm tra trạng thái mới có hợp lệ với trạng thái hiện tại hay không
    if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
        return back()->with('error', 'Không thể chuyển trạng thái từ "' . $currentStatus . '" sang "' . $newStatus . '".');
    }

    // Nếu hợp lệ thì cập nhật
    $order->update(['status' => $newStatus]);

    return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
}
}