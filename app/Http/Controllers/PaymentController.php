<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function vnpay_payment(Request $request)
    {
        $data = $request->all();

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.return'); // Laravel route callback
        $vnp_TmnCode = "LTYIA6SI"; 
        $vnp_HashSecret = "27K6DM746RK5939FAVSF3CRPJVSK0L1W"; 
        
        $vnp_TxnRef = time(); // tạo mã đơn ngẫu nhiên
        $vnp_OrderInfo = "Thanh toán hóa đơn QT-Shop";
        $vnp_OrderType = "QT-Shop";
   $rawTotal = $request->input('total'); // 715000 (int)
$total = (int) str_replace('.', '', $rawTotal);;
 \Log::info("Debug tổng gửi đi: " . $total);

// VNPay yêu cầu nhân 100
$vnp_Amount = $total * 100;


    
        $vnp_Locale = "VN";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $request->ip();
    
        $inputData = [
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
        ];
    
        if ($vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
    
        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;
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
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
    
        // 🚀 Redirect sang VNPay
        return redirect()->away($vnp_Url);
    }
     public function vnpayReturn(Request $request)
    {
        return "Thanh toán VNPay callback";
    }
}
