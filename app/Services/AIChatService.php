<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AIChatService
{
    protected $orderService;
    protected $productService;
    protected $knowledgeBase;

    public function __construct(OrderService $orderService, ProductService $productService)
    {
        $this->orderService = $orderService;
        $this->productService = $productService;
        $this->knowledgeBase = $this->loadKnowledgeBase();
    }

    public function processMessage($message, $sessionId, $attachments = [])
    {
        // Phân tích ý định
        $intentAnalysis = $this->analyzeIntent($message);
        
        // Xử lý dựa trên ý định
        switch ($intentAnalysis['intent']) {
            case 'order_lookup':
                return $this->handleOrderLookup($message, $intentAnalysis);
            
            case 'product_inquiry':
                return $this->handleProductInquiry($message, $intentAnalysis);
            
            case 'shipping_info':
                return $this->handleShippingInfo($message);
            
            case 'faq':
                return $this->handleFAQ($message, $intentAnalysis);
            
            case 'create_order':
                return $this->handleCreateOrder($message, $sessionId);
            
            case 'image_analysis':
                return $this->handleImageAnalysis($attachments, $message);
            
            default:
                return $this->handleGeneralInquiry($message, $intentAnalysis);
        }
    }

    private function analyzeIntent($message)
    {
        $message = strtolower(trim($message));
        
        // Phân tích từ khóa để xác định ý định
        $intents = [
            'order_lookup' => ['đơn hàng', 'mã đơn', 'tra cứu', 'trạng thái', 'vận chuyển'],
            'product_inquiry' => ['sản phẩm', 'hàng', 'còn không', 'giá', 'thông số', 'chất liệu'],
            'shipping_info' => ['ship', 'phí ship', 'vận chuyển', 'giao hàng', 'thời gian'],
            'faq' => ['đổi trả', 'bảo hành', 'kiểm tra', 'chính sách'],
            'create_order' => ['đặt hàng', 'mua', 'thêm vào giỏ', 'thanh toán'],
            'image_analysis' => ['ảnh', 'hình', 'video', 'xem cái này']
        ];

        $detectedIntent = 'general';
        $confidence = 0;
        $entities = [];

        foreach ($intents as $intent => $keywords) {
            $matches = array_filter($keywords, function($keyword) use ($message) {
                return strpos($message, $keyword) !== false;
            });
            
            $matchCount = count($matches);
            if ($matchCount > $confidence) {
                $confidence = $matchCount;
                $detectedIntent = $intent;
                $entities = array_values($matches);
            }
        }

        return [
            'intent' => $detectedIntent,
            'confidence' => $confidence / max(1, count(explode(' ', $message))),
            'entities' => $entities
        ];
    }

    private function handleOrderLookup($message, $intentAnalysis)
    {
        // Trích xuất mã đơn hàng hoặc số điện thoại
        preg_match('/\b(DH|ĐH)?(\d{6,8})\b/', $message, $orderMatches);
        preg_match('/\b(0|\+84)(\d{9,10})\b/', $message, $phoneMatches);
        
        $orderCode = $orderMatches[2] ?? null;
        $phone = $phoneMatches[0] ?? null;

        if (!$orderCode && !$phone) {
            return [
                'type' => 'question',
                'message' => "Để tra cứu đơn hàng, vui lòng cung cấp Mã đơn hàng hoặc Số điện thoại bạn đã dùng khi đặt hàng.",
                'intent' => 'order_lookup',
                'confidence' => $intentAnalysis['confidence'],
                'buttons' => [
                    ['text' => '📦 Nhập mã đơn hàng', 'type' => 'input', 'placeholder' => 'Nhập mã đơn hàng...'],
                    ['text' => '📞 Nhập số điện thoại', 'type' => 'input', 'placeholder' => 'Nhập số điện thoại...']
                ]
            ];
        }

        // Gọi API tra cứu đơn hàng
        try {
            $orderInfo = $this->orderService->lookupOrder($orderCode, $phone);
            
            if ($orderInfo) {
                return [
                    'type' => 'order_info',
                    'message' => $this->formatOrderInfo($orderInfo),
                    'intent' => 'order_lookup',
                    'confidence' => 1.0,
                    'data' => $orderInfo
                ];
            } else {
                return [
                    'type' => 'error',
                    'message' => "Không tìm thấy thông tin đơn hàng. Vui lòng kiểm tra lại mã đơn hàng hoặc số điện thoại.",
                    'intent' => 'order_lookup',
                    'confidence' => 1.0
                ];
            }
        } catch (\Exception $e) {
            return [
                'type' => 'error',
                'message' => "Hiện tại không thể tra cứu đơn hàng. Vui lòng thử lại sau hoặc liên hệ nhân viên hỗ trợ.",
                'intent' => 'order_lookup',
                'confidence' => 1.0
            ];
        }
    }

    private function handleProductInquiry($message, $intentAnalysis)
    {
        // Trích xuất tên sản phẩm từ tin nhắn
        $productName = $this->extractProductName($message);
        
        if (!$productName) {
            $products = $this->productService->getSuggestedProducts();
            
            return [
                'type' => 'product_suggestions',
                'message' => "Bạn đang quan tâm đến sản phẩm nào? Dưới đây là một số sản phẩm phổ biến:",
                'intent' => 'product_inquiry',
                'confidence' => $intentAnalysis['confidence'],
                'products' => $products,
                'buttons' => [
                    ['text' => '🔍 Tìm kiếm sản phẩm', 'type' => 'input', 'placeholder' => 'Nhập tên sản phẩm...'],
                    ['text' => '📱 Xem danh mục', 'type' => 'category_selection']
                ]
            ];
        }

        // Tìm kiếm sản phẩm
        $products = $this->productService->searchProducts($productName);
        
        if (count($products) > 0) {
            return [
                'type' => 'product_list',
                'message' => "Tìm thấy " . count($products) . " sản phẩm phù hợp:",
                'intent' => 'product_inquiry',
                'confidence' => 1.0,
                'products' => $products
            ];
        } else {
            $suggestions = $this->productService->getSuggestedProducts();
            
            return [
                'type' => 'product_not_found',
                'message' => "Không tìm thấy sản phẩm '$productName'. Bạn có thể tham khảo các sản phẩm khác:",
                'intent' => 'product_inquiry',
                'confidence' => 1.0,
                'suggestions' => $suggestions
            ];
        }
    }

    private function handleImageAnalysis($attachments, $message)
    {
        if (empty($attachments)) {
            return [
                'type' => 'question',
                'message' => "Bạn có thể gửi hình ảnh hoặc video sản phẩm để được tư vấn chi tiết hơn.",
                'intent' => 'image_analysis',
                'confidence' => 0.8
            ];
        }

        // Xử lý phân tích ảnh/video
        $analysisResults = [];
        
        foreach ($attachments as $attachment) {
            $result = $this->analyzeMedia($attachment);
            $analysisResults[] = $result;
        }

        return [
            'type' => 'media_analysis',
            'message' => "Đã nhận được file của bạn. " . $this->formatMediaAnalysis($analysisResults),
            'intent' => 'image_analysis',
            'confidence' => 1.0,
            'analysis' => $analysisResults,
            'buttons' => [
                ['text' => '💬 Cần tư vấn thêm', 'type' => 'transfer_to_agent'],
                ['text' => '🛒 Tìm sản phẩm tương tự', 'type' => 'find_similar_products']
            ]
        ];
    }

    private function loadKnowledgeBase()
    {
        return [
            'faqs' => [
                'còn hàng' => [
                    'question' => 'Sản phẩm còn hàng không?',
                    'answer' => 'Để kiểm tra tình trạng tồn kho, vui lòng cho tôi biết tên sản phẩm bạn quan tâm. Tôi sẽ kiểm tra ngay!',
                    'follow_up' => 'product_inquiry'
                ],
                'phí ship' => [
                    'question' => 'Phí ship bao nhiêu?',
                    'answer' => 'Phí vận chuyển phụ thuộc vào khu vực:\n- Nội thành: 20,000đ\n- Ngoại thành: 30,000đ\n- Tỉnh thành khác: 35,000đ\nMiễn phí ship cho đơn hàng từ 500,000đ',
                    'buttons' => [
                        ['text' => '🚚 Xem chi tiết vận chuyển', 'type' => 'shipping_info']
                    ]
                ],
                // Thêm các FAQ khác...
            ]
        ];
    }
}