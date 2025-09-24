<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;

use App\Models\Voucher;
use App\Models\Stock;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    
  public function reorder($id)
{
    $order = Order::with('orderItems')->findOrFail($id);
    $user = Auth::user();

    // Bảo mật quyền xem lại đơn
    if ($user) {
        if ($order->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền đặt lại đơn hàng này.');
        }
    } else {
        $guestOrderIds = session('guest_order_ids', []);
        if (!in_array($order->id, $guestOrderIds)) {
            abort(403, 'Bạn không có quyền đặt lại đơn hàng này.');
        }
    }

    foreach ($order->orderItems as $item) {
        CartItem::updateOrCreate(
            [
                'user_id' => $user?->id ?? session()->getId(),
                'product_id' => $item->product_id,
                'size' => $item->size,
                'color' => $item->color,
            ],
            [
                'quantity' => DB::raw("quantity + {$item->quantity}"),
                'price' => $item->unit_price,
            ]
        );
    }

    return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
}

public function index()
{
    $user = Auth::user();

    if ($user) {
        // Nếu đã đăng nhập, lấy đơn của user
        $orders = Order::where('user_id', $user->id)
            ->with('orderItems.product')
            ->orderByDesc('order_date')
            ->get();
    } else {
        // Nếu là khách, lấy đơn theo session lưu ID
        $guestOrderIds = session('guest_order_ids', []);
        $orders = Order::whereIn('id', $guestOrderIds)
            ->with('orderItems.product')
            ->orderByDesc('order_date')
            ->get();
    }

    return view('orders.index', compact('orders'));
}

public function show($id)
{
    $order = Order::with('orderItems.product')->findOrFail($id);
    $user = Auth::user();

    // Nếu chưa đăng nhập → phải là đơn hàng được tạo bởi guest
    if (!$user) {
        $guestOrderIds = session('guest_order_ids', []);
        if (!in_array($order->id, $guestOrderIds)) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }
    }

    // Nếu đăng nhập → phải là chủ đơn hàng (và đơn không phải guest)
  if ($user && $order->user_id !== $user->id) {
    return redirect('/')->with('error', 'Bạn không có quyền xem đơn hàng này khi chưa đăng nhập .');
}

    // Tách địa chỉ dạng "|"
    $provinceName = $districtName = $wardName = '';
    $detail = '';
    $parts = explode('|', $order->shipping_address);
    $provinceName = $parts[0] ?? '';
    $districtName = $parts[1] ?? '';
    $wardName     = $parts[2] ?? '';
    $detail       = $parts[3] ?? '';
   $shippingFee = $order->shipping_fee ?? 0;
    return view('orders.show', compact(
        'order', 'provinceName', 'districtName', 'wardName', 'detail'
    ));
}

   public function processCheckout(Request $request)
{
    $selectedIds = explode(',', $request->input('selected_ids'));

    if (empty($selectedIds)) {
        return redirect()->back()->with('error', 'Vui lòng chọn sản phẩm để thanh toán.');
    }

   $userId = Auth::check() ? Auth::id() : session()->getId();

$cartItems = CartItem::whereIn('id', $selectedIds)
    ->where('user_id', $userId)
    ->get();

    if ($cartItems->isEmpty()) {
        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm để thanh toán.');
    }

$totalAmount = $cartItems->sum(function ($item) {
    return $item->product->price  * $item->quantity;
});

$shippingAddress = '';
$shippingFee = $this->calculateShippingFee($totalAmount, $shippingAddress);

    // ✅ Ghi dữ liệu vào session để showCheckout dùng được
    session([
        'checkout_items' => $cartItems,
        'checkout_total' => $totalAmount,
        'voucher_code' => session('voucher_code'),
        'voucher_discount' => session('voucher_discount', 0),
        'checkout_ids' => $selectedIds, 
        'shipping_fee' => $shippingFee, 
    ]);

    return redirect()->route('checkout')->with('success', 'Chuyển đến trang thanh toán');
}
public function placeOrder(Request $request)
{
    $user = Auth::user();

    // ✅ 1. VALIDATE
    $rules = [
        'address_id'       => 'nullable|exists:addresses,id',
        'province_name'    => 'required_without:address_id|string|max:100',
        'district_name'    => 'required_without:address_id|string|max:100',
        'ward_name'        => 'required_without:address_id|string|max:100',
        'detail'           => 'required_without:address_id|string|max:255',
        'full_name'        => ['required','string','max:40','regex:/^[A-Za-zÀ-ỹ\s]+$/'],
        'phone_number'     => ['required','string','regex:/^[0-9]{10}$/'],
        'payment_method'   => 'required|in:cod,credit_card,paypal,bank_transfer',
    ];

    if ($user) {
        $rules['confirm_password'] = 'required|string';
    } else {
        $rules['phone_confirm'] = [
            'required',
            'same:phone_number',
            'regex:/^[0-9]{10}$/'
        ];
    }

    $request->validate($rules);

    if ($user && !Hash::check($request->confirm_password, $user->password)) {
        return response()->json(['error' => 'Mật khẩu không đúng.'], 403);
    }

    DB::beginTransaction();
    try {
        $selectedIds = $request->input('checkout_ids', []);

        $cartItems = CartItem::with('product')
            ->where('user_id', $user ? $user->id : session()->getId())
            ->whereIn('id', $selectedIds)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Giỏ hàng trống!'], 422);
        }

        // ✅ 2. Xử lý địa chỉ
        $shippingAddress = '';
        $address_id = null;

        if ($request->address_id && $user) {
            $address = Address::findOrFail($request->address_id);
            $address->update(['phone_number' => $request->phone_number]);
            $shippingAddress = implode(', ', [
                $address->detail, $address->ward, $address->district, $address->province
            ]);
            $address_id = $address->id;
        } else {
            $shippingAddress = implode(', ', [
                $request->detail,
                $request->ward_name,
                $request->district_name,
                $request->province_name,
            ]);

            if ($user) {
                if ($request->is_default) {
                    Address::where('user_id', $user->id)->update(['is_default' => false]);
                }

                $newAddress = Address::create([
                    'user_id'      => $user->id,
                    'province'     => $request->province_name,
                    'district'     => $request->district_name,
                    'ward'         => $request->ward_name,
                    'detail'       => $request->detail,
                    'full_name'    => $request->full_name,
                    'phone_number' => $request->phone_number,
                    'is_default'   => $request->is_default ?? false,
                ]);

                $address_id = $newAddress->id;
            }
        }

        // ✅ 3. Lấy voucher từ session
        $voucherCode = session('voucher_code');
        $voucher = null;

        if ($voucherCode) {
            $voucher = \App\Models\Voucher::where('code', $voucherCode)->first();
        }

        // ✅ 4. Tạo đơn hàng
        $order = Order::create([
            'user_id'          => $user?->id,
            'address_id'       => $address_id,
            'full_name'        => $request->full_name,
            'order_date'       => now(),
            'total_amount'     => 0,
            'status'           => 'pending',
            'voucher_code'     => $voucher?->code,
            'voucher_id'       => $voucher?->id,
            'payment_method'   => $request->payment_method,
            'shipping_address' => $shippingAddress,
            'phone_number'     => $request->phone_number,
        ]);

        $cartId = $cartItems->first()->cart_id ?? null;
        if ($cartId) {
            $order->cart_id = $cartId;
            $order->save();
        }

        // ✅ 5. Lưu chi tiết sản phẩm
        $itemsTotal = 0;

        foreach ($cartItems as $item) {
            $variant = ProductVariant::where('product_id', $item->product_id)
                ->where('size', $item->size)
                ->where('color', $item->color)
                ->firstOrFail();

            if ($variant->stock_quantity < $item->quantity) {
                throw new \Exception("Biến thể ID {$variant->id} không đủ tồn kho.");
            }

            $unitPrice  = $variant->price;
            $totalPrice = $unitPrice * $item->quantity;
            $itemsTotal += $totalPrice;

            OrderItem::create([
                'order_id'    => $order->id,
                'cart_id'     => $cartId,
                'product_id'  => $item->product_id,
                'variant_id'  => $variant->id,
                'quantity'    => $item->quantity,
                'size'        => $item->size,
                'color'       => $item->color,
                'unit_price'  => $unitPrice,
                'total_price' => $totalPrice,
            ]);

            $variant->decrement('stock_quantity', $item->quantity);

            Stock::create([
                'variant_id' => $variant->id,
                'quantity'   => $item->quantity,
                'type'       => 'export',
                'note'       => "Xuất kho tự động khi đặt đơn #{$order->id}",
            ]);
        }

        // ✅ 6. Tính giảm giá và cập nhật tổng tiền
        $discount = session('voucher_discount', 0);
        $subTotal = $itemsTotal - $discount;

        $shippingFullAddress = $request->address_id && isset($address)
            ? implode(', ', [$address->detail, $address->ward, $address->district, $address->province])
            : $shippingAddress;

        $shippingFee = $this->calculateShippingFee($subTotal, $shippingFullAddress);
       $finalTotal = $subTotal + $shippingFee;

        $order->update([
            'total_amount' => $finalTotal,
            'shipping_fee' => $shippingFee,
        ]);

        // ✅ 7. Ghi nhận voucher đã dùng
        if ($voucher) {
            $voucher->incrementUsedCount();
        }

        // ✅ 8. Xóa giỏ hàng và session
        CartItem::where('user_id', $user ? $user->id : session()->getId())
            ->whereIn('id', $selectedIds)->delete();

        session()->forget(['checkout_ids', 'checkout_items', 'checkout_total', 'voucher_code', 'voucher_discount']);

        if (!$user) {
            $guestOrderIds = session('guest_order_ids', []);
            $guestOrderIds[] = $order->id;
            session(['guest_order_ids' => $guestOrderIds]);
        }

        DB::commit();

        return response()->json([
            'order_id' => $order->id,
            'message' => 'Đặt hàng thành công!'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Order creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại.'], 500);
    }
}

  public function applyVoucher(Request $request)
{
    $request->validate([
        'voucher_code' => 'required|string',
        'selected_ids' => 'required|array', // Thêm để nhận danh sách ID giỏ hàng được chọn
    ]);

    $voucherCode = trim($request->voucher_code); // bỏ khoảng trắng đầu/cuối
$voucherCode = Str::upper(Str::ascii($voucherCode)); // bỏ dấu và chuyển về in hoa

$voucher = Voucher::whereRaw('UPPER(code) = ?', [$voucherCode])->first();

    if (!$voucher) {
        return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại.']);
    }

    // Các kiểm tra trạng thái, ngày, số lượt sử dụng... giữ nguyên như code cũ
    if (!$voucher->status) {
        return response()->json(['success' => false, 'message' => 'Mã giảm giá không còn hiệu lực.']);
    }
    if ($voucher->start_date && now()->lessThan($voucher->start_date)) {
        return response()->json(['success' => false, 'message' => 'Mã giảm giá chưa được áp dụng.']);
    }
    if ($voucher->expires_at && now()->greaterThan($voucher->expires_at)) {
        return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn.']);
    }
    if ($voucher->usage_limit > 0 && $voucher->used_count >= $voucher->usage_limit) {
        return response()->json(['success' => false, 'message' => 'Mã giảm giá đã được dùng hết lượt.']);
    }
    if ($voucher->only_new_users) {
        $user = auth()->user();
        $hasOrders = $user->orders()->exists();
        if ($hasOrders) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá chỉ áp dụng cho người dùng mới.']);
        }
    }

    // Tính tổng tiền chỉ dựa trên sản phẩm được chọn
    $cartTotal = $this->getCartTotal($request->selected_ids);

    if ($voucher->min_order_amount && $cartTotal < $voucher->min_order_amount) {
        return response()->json([
            'success' => false,
            'message' => 'Đơn hàng chưa đạt mức tối thiểu để áp dụng mã.'
        ]);
    }

    $discountAmount = 0;
    if ($voucher->discount_type === 'percent') {
        $discountAmount = round($cartTotal * $voucher->discount_value / 100);
    } elseif ($voucher->discount_type === 'fixed') {
        $discountAmount = $voucher->discount_value;
    } else {
        return response()->json(['success' => false, 'message' => 'Loại giảm giá không hợp lệ.']);
    }

    $discountAmount = min($discountAmount, $cartTotal);

    session([
        'voucher_code' => $voucher->code,
        'voucher_discount' => $discountAmount,
    ]);

    $totalAfterDiscount = $cartTotal - $discountAmount;

    return response()->json([
        'success' => true,
        'message' => 'Áp dụng mã giảm giá thành công!',
        'discount' => number_format($discountAmount, 0, ',', '.'),
        'total' => number_format($totalAfterDiscount, 0, ',', '.'),
    ]);
}

protected function getCartTotal(array $selectedIds)
{
    $user = auth()->user();
    if (!$user) {
        return 0;
    }

    // Chỉ lấy cart item có ID nằm trong danh sách được chọn
    $cartItems = $user->cartItems()
        ->whereIn('id', $selectedIds)
        ->with('variant')
        ->get();

    return $cartItems->sum(function ($item) {
        $price = $item->variant ? $item->variant->price : 0;
        return $price * $item->quantity;
    });
}



public function showCheckout()
{
    $cartItems = session('checkout_items', collect());
    $total = session('checkout_total', 0) * 1000;
    $checkoutIds = session('checkout_ids', []);
    $voucher_code = session('voucher_code');
    $discount = session('voucher_discount', 0);

    if ($cartItems->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Không có sản phẩm được chọn để thanh toán.');
    }

    $addresses = collect();
    $shippingFee = 0;

   if (Auth::check()) {
    $addresses = Address::where('user_id', Auth::id())->get();

    if ($addresses->isNotEmpty()) {
        $selectedAddressId = session('selected_address');
        $selectedAddress = $addresses->firstWhere('id', $selectedAddressId);

        // Nếu không có selected_address trong session hoặc không khớp, dùng địa chỉ đầu tiên
        if (!$selectedAddress) {
            $selectedAddress = $addresses->first();
            session(['selected_address' => $selectedAddress->id]); // ✅ Ghi lại vào session
        }
if ($selectedAddress) {
    $shippingFee = $this->calculateShippingFee($total, $selectedAddress->full_address);
}
        if ($selectedAddress) {
            $shippingFee = $this->calculateShippingFee($total, $selectedAddress->full_address);
        }
    }
}
$finalTotal = $total + $shippingFee - $discount;


$availableVouchers = Voucher::available($total, $voucher_code)->get();

   return view('checkout', compact(
    'cartItems', 'addresses', 'voucher_code', 'discount', 'total', 'checkoutIds', 'shippingFee', 'availableVouchers', 'finalTotal'
));


}


private function calculateShippingFee($orderTotal, $shippingAddress)
{
    
    if ($orderTotal >= 500000) {
        return 0;
    }

    // Các thành phố có phí ship 15,000đ
    $innerCities = [ 'TP.HCM', 'Thành phố Hồ Chí Minh', 'Cần Thơ'];

    foreach ($innerCities as $city) {
        if (stripos($shippingAddress, $city) !== false) {
            return 15000;
        }
    }

    // Các địa chỉ còn lại phí 30,000đ
    return 30000;
   
}
public function calculateShippingAjax(Request $request)

{
    try {
        $address = Address::findOrFail($request->address_id);
if (!$address) {
    return response()->json(['error' => 'Địa chỉ không tồn tại'], 400);
}

        $total = session('checkout_total', 0);
        $discount = session('voucher_discount', 0);

        $fullAddress = $address->detail . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->province;

$fee = $this->calculateShippingFee($total, $fullAddress);

        $finalTotal = $total + $fee - $discount;

        return response()->json([
            'fee' => $fee,
            'formatted_fee' => number_format($fee, 0, ',', '.') . '₫',
            'formatted_total' => number_format($finalTotal, 0, ',', '.') . '₫',
        ]);
    } catch (\Exception $e) {
        \Log::error('Lỗi tính phí vận chuyển: ' . $e->getMessage());
        return response()->json(['error' => 'Không thể tính phí vận chuyển'], 500);
        
    }
}

public function getShippingFee(Request $request)
{
    $address = $request->input('address');
    $total = session('checkout_total', 0) ;

$orderTotal = $this->tinhTongTien($cart);
    $shippingFee = $this->calculateShippingFee($total, $address);

    return response()->json([
        'shipping_fee' => $shippingFee,
        'formatted_fee' => number_format($shippingFee, 0, ',', '.') . '₫',
    ]);
}

}