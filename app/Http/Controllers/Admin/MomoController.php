<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;

class MomoController extends Controller
{
    // Bước 1: Khởi tạo thanh toán MoMo
    public function momo_payment(Request $request)
    {
        $amount = $request->input('total_momo'); // Tổng tiền cần thanh toán
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey   = 'klm05TvNBzhg7h7j';
        $secretKey   = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo   = "Thanh toán qua MoMo";
        $redirectUrl = route('momo.return'); // URL redirect sau khi thanh toán xong
        $ipnUrl      = route('momo.notify'); // URL MoMo gửi notify

        // ✅ Tạo đơn hàng tạm
$order = \App\Models\Order::create([
    'user_id' => auth()->id(),
    'order_date' => now(),
    'total_amount' => $amount,
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

        session(['pending_order_id' => $order->id]); // lưu session để dùng khi return
        $orderId = $order->id;
        $requestId = time() . "";
        $requestType = "payWithATM";
        $extraData = "";

        // Tạo signature
        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId"     => "MomoTestStore",
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature
        ];

        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);

        return redirect()->to($jsonResult['payUrl']);
    }

    // Hàm POST request
    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }

        curl_close($ch);
        return $response;
    }

    // Bước 2: MoMo gửi notify
    public function notify(Request $request)
    {
        $data = $request->all();
        \Log::info('MoMo notify:', $data);

        if (isset($data['resultCode']) && $data['resultCode'] == 0) {
            $order = Order::find($data['orderId']); // tìm order theo ID
            if ($order) {
                $order->update([
                    'status' => 'paid',
                    'qaid' => $data['transId'],   // lưu mã MoMo
                    'total_amount' => $data['amount'],
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }

    // Bước 3: Khách quay lại website
    public function return(Request $request)
    {
        $order = Order::find(session('pending_order_id') ?? $request->orderId);

        if (!$order) {
            return redirect('/checkout')->with('error', 'Không tìm thấy đơn hàng trong hệ thống.');
        }

       if ($request->resultCode == 0) {
    $order->update([
        'payment_status' => 'paid',  
        'total_amount'   => $request->amount ?? 0,
        'qaid'           => $request->orderId ?? null,
    ]);

    session()->forget('pending_order_id');

            return redirect('/checkout')->with('success', 'Thanh toán MoMo thành công!');
        } else {
            return redirect('/checkout')->with('error', 'Thanh toán MoMo thất bại!');
        }
    }
}
