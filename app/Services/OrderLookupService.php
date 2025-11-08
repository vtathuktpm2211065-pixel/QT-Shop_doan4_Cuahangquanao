<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderLookupService
{
    public function lookupOrder($orderCode = null, $phone = null, $fullName = null)
    {
        try {
            // Sử dụng query phù hợp với database structure của bạn
            $query = DB::table('orders')
                ->select('*')
                ->where(function($q) use ($orderCode, $phone, $fullName) {
                    if ($orderCode) {
                        $q->where('order_code', $orderCode)
                          ->orWhere('id', $orderCode);
                    }
                    if ($phone) {
                        $q->orWhere('phone_number', $phone)
                          ->orWhere('customer_phone', $phone);
                    }
                    if ($fullName) {
                        $q->orWhere('customer_name', 'like', '%' . $fullName . '%')
                          ->orWhere('full_name', 'like', '%' . $fullName . '%');
                    }
                })
                ->orderBy('created_at', 'desc');

            $orders = $query->get();

            if ($orders->isEmpty()) {
                return [
                    'found' => false,
                    'message' => 'Không tìm thấy đơn hàng nào với thông tin đã cung cấp.'
                ];
            }

            return [
                'found' => true,
                'orders' => $orders->map(function($order) {
                    return $this->formatOrderInfo($order);
                }),
                'total_orders' => $orders->count()
            ];

        } catch (\Exception $e) {
            Log::error('Order lookup error: ' . $e->getMessage());
            return [
                'found' => false,
                'message' => 'Có lỗi xảy ra khi tra cứu đơn hàng. Vui lòng thử lại sau.'
            ];
        }
    }

    private function formatOrderInfo($order)
    {
        $statusInfo = $this->getStatusInfo($order->status ?? 'pending');
        
        return [
            'order_code' => $order->order_code ?? $order->id,
            'customer_name' => $order->customer_name ?? $order->full_name ?? 'Khách hàng',
            'phone_number' => $order->phone_number ?? $order->customer_phone ?? 'N/A',
            'total_amount' => isset($order->total_amount) ? number_format($order->total_amount, 0, ',', '.') . 'đ' : 'N/A',
            'status' => $order->status ?? 'pending',
            'status_description' => $statusInfo['description'],
            'status_icon' => $statusInfo['icon'],
            'status_color' => $statusInfo['color'],
            'shipping_address' => $order->shipping_address ?? $order->address ?? 'N/A',
            'created_at' => isset($order->created_at) ? \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') : 'N/A',
            'estimated_delivery' => isset($order->estimated_delivery) ? \Carbon\Carbon::parse($order->estimated_delivery)->format('d/m/Y') : 'Đang cập nhật',
            'payment_status' => ($order->payment_status ?? 'unpaid') === 'paid' ? '✅ Đã thanh toán' : '⏳ Chưa thanh toán'
        ];
    }

    private function getStatusInfo($status)
    {
        $statusMap = [
            'pending' => ['icon' => '⏳', 'color' => 'warning', 'description' => 'Đơn hàng đang chờ xử lý'],
            'confirmed' => ['icon' => '✅', 'color' => 'info', 'description' => 'Đơn hàng đã xác nhận'],
            'processing' => ['icon' => '🔄', 'color' => 'primary', 'description' => 'Đang đóng gói và xử lý'],
            'shipped' => ['icon' => '🚚', 'color' => 'success', 'description' => 'Đã giao cho đơn vị vận chuyển'],
            'delivered' => ['icon' => '📦', 'color' => 'success', 'description' => 'Đã giao hàng thành công'],
            'cancelled' => ['icon' => '❌', 'color' => 'danger', 'description' => 'Đơn hàng đã bị hủy'],
        ];

        return $statusMap[$status] ?? ['icon' => '📝', 'color' => 'secondary', 'description' => 'Trạng thái đơn hàng'];
    }

    public function extractOrderInfoFromMessage($message)
    {
        // Trích xuất mã đơn hàng (DH + số)
        preg_match('/\b(DH|ĐH|ORDER)?\s*(\d{6,8})\b/i', $message, $orderMatches);
        $orderCode = $orderMatches[2] ?? null;

        // Trích xuất số điện thoại
        preg_match('/\b(0|\+84)(\d{9,10})\b/', $message, $phoneMatches);
        $phone = $phoneMatches[0] ?? null;

        return [
            'order_code' => $orderCode,
            'phone' => $phone,
            'full_name' => null
        ];
    }
}