<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use App\Models\Order;

class OrderTrackingController extends Controller
{
    // Hiển thị form tra cứu
    public function form()
    {
        return view('orders.track_order_form');
    }

    // Xử lý tra cứu
    public function lookup(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => ['required', 'regex:/^[0-9]{10}$/'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ tên khách hàng.',
            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
            'phone_number.regex' => 'Số điện thoại phải gồm đúng 10 chữ số.',
        ]);

        $fullName = $request->input('full_name');
        $phone    = $request->input('phone_number');

        $orders = Order::where('full_name', 'like', '%' . $fullName . '%')
                    ->where('phone_number', $phone)
                    ->orderBy('order_date', 'desc')
                    ->get();

        if ($orders->isEmpty()) {
            return back()->withErrors(['Không tìm thấy đơn hàng khớp với thông tin đã nhập.']);
        }

        return view('orders.track_order_result', compact('orders'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $cancelableStatuses = ['pending', 'approved', 'processing', 'paid'];

        if (in_array($order->status, $cancelableStatuses)) {
            $order->update(['status' => 'cancelled']);
            return back()->with('success', 'Đơn hàng đã được hủy thành công.');
        }

        return back()->withErrors(['Không thể hủy đơn ở trạng thái hiện tại.']);
    }
}
