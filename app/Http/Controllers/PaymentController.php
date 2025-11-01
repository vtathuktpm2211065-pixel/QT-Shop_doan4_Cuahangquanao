<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
class PaymentController extends Controller
{
    public function vnpay_payment(Request $request)
    {
        $data = $request->all();
        
        // Tạo order trước khi thanh toán
       $order = \App\Models\Order::create([
    'user_id' => auth()->id(),
    'order_date' => now(),
     'total_amount' => $data['total_amount'],
    'status' => 'pending',
    'payment_method' => 'momo',

    // 🩵 Bổ sung các cột NOT NULL để tránh lỗi SQL
    'full_name' => auth()->user()->name ?? 'Khách MoMo',
    'shipping_address' => 'Chưa cập nhật',
    'phone_number' => auth()->user()->phone ?? 'Chưa cập nhật',

    // (nếu bảng bạn có thêm voucher, phí ship… thì có thể set mặc định)
    'voucher_id' => null,
    'voucher_code' => null,
    'shipping_fee' => 0,
]);


        // Xóa cart sau khi tạo order
        Cart::where('user_id', Auth::id())->delete();

        // Cấu hình VNPay
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.return'); // Sử dụng route name
        $vnp_TmnCode = "LTYIA6SI";
        $vnp_HashSecret = "WTNQYHU07XB7YAEMRJ63QKLPPILYO4TR";

        // Sử dụng ID order thực tế làm mã giao dịch
        $vnp_TxnRef = $order->id;
        $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $order->id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $data['total_amount'] * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        // Sắp xếp và tạo URL thanh toán
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }

    // Xử lý khi VNPay redirect về
    public function vnpayReturn(Request $request)
    {
        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $vnp_TxnRef = $request->input('vnp_TxnRef'); // ID order
        $vnp_TransactionNo = $request->input('vnp_TransactionNo'); // Mã GD VNPay

        $order = Order::find($vnp_TxnRef);

        if (!$order) {
            return redirect('/checkout')->with('error', 'Không tìm thấy đơn hàng.');
        }

        if ($vnp_ResponseCode == '00') {
            // Thanh toán thành công
            $order->update([
                'payment_status' => 'paid', 
                'transaction_id' => $vnp_TransactionNo, // Lưu mã GD VNPay
            ]);

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Thanh toán VNPay thành công!');
        } else {
            // Thanh toán thất bại
            $order->update([
                'status' => 'failed',
                'transaction_id' => $vnp_TransactionNo,
            ]);

            return redirect('/checkout')
                ->with('error', 'Thanh toán VNPay thất bại. Mã lỗi: ' . $vnp_ResponseCode);
        }
    }
}