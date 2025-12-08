<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AIConversation;
use App\Services\OrderLookupService;
use App\Services\FileUploadService;
use App\Services\MediaAnalysisService;
use Illuminate\Support\Facades\Auth;


    class AIChatController extends Controller
    {
        protected $orderLookupService;
        protected $fileUploadService;
        protected $mediaAnalysisService;

        public function __construct(
            OrderLookupService $orderLookupService,
            FileUploadService $fileUploadService,
            MediaAnalysisService $mediaAnalysisService
        ) {
            $this->orderLookupService = $orderLookupService;
            $this->fileUploadService = $fileUploadService;
            $this->mediaAnalysisService = $mediaAnalysisService;
        }

        public function aiChat()
        {
            $sessionId = session()->getId();
            
            $conversations = AIConversation::where('session_id', $sessionId)
                ->orderBy('created_at', 'asc')
                ->get();

            return view('support.ai-chat', compact('conversations'));
        }


        public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'nullable|string|max:1000',
                'session_id' => 'nullable|string',
                'attachments' => 'nullable|array',
                'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt',
            ]);

            $sessionId = $request->session_id ?? session()->getId();
            $message = $request->message ?? '';
            $attachments = $request->file('attachments', []);

            // Process attachments
            $uploadedFiles = [];
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    $fileName = time() . '_' . $attachment->getClientOriginalName();
                    $path = $attachment->storeAs('ai_attachments', $fileName, 'public');
                    
                    $uploadedFiles[] = [
                        'path' => $path,
                        'name' => $attachment->getClientOriginalName(),
                        'type' => $attachment->getMimeType(),
                        'size' => $attachment->getSize(),
                    ];
                }
            }

            // Process message with AI
            $response = $this->processAIMessage($message, $uploadedFiles);

            // Save conversation
            $conversation = AIConversation::create([
                'session_id' => $sessionId,
                'user_id' => Auth::check() ? Auth::id() : null,
                'message' => $message,
                'response' => is_string($response['message']) ? $response['message'] : json_encode($response['message']),
                'message_type' => $response['type'],
                'intent' => $response['intent'],
                'confidence' => $response['confidence'],
                'context' => $response['context'] ?? [],
                'attachments' => $uploadedFiles,
            ]);

            return response()->json([
                'success' => true,
                'response' => $response['message'],
                'session_id' => $sessionId,
                'conversation_id' => $conversation->id,
                'attachments' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

        private function processAIMessage($message, $attachments = [])
        {
            $message = strtolower(trim($message));
            
            // ✅ Nếu có file đính kèm
            if (!empty($attachments)) {
                return $this->handleMediaAnalysis($message, $attachments);
            }

            // ✅ Phân tích ý định
            $intent = $this->detectIntent($message);

            if ($intent === 'order_lookup') {
                return $this->handleOrderLookup($message);
            }

            return $this->generateResponse($intent, $message);
        }

        // 🔹 Xử lý media
        private function handleMediaAnalysis($message, $attachments)
        {
            $analysisResults = $this->mediaAnalysisService->analyzeMedia($attachments, $message);

            $responseMessage = "📎 **Đã nhận file đính kèm của bạn!**\n\n";
            foreach ($analysisResults as $index => $analysis) {
                $responseMessage .= "**File " . ($index + 1) . ":** {$analysis['file_name']}\n";
                $responseMessage .= "📊 Loại: " . $this->getFileTypeLabel($analysis['file_type']) . "\n";
                $responseMessage .= "💾 Kích thước: {$analysis['file_size']}\n\n";
            }
            $responseMessage .= $analysisResults[0]['message'] ?? "Bạn muốn tôi giúp gì với các file này?";

            return [
                'message' => $responseMessage,
                'type' => 'media_analysis',
                'intent' => 'media_upload',
                'confidence' => 1.0,
                'attachments' => $attachments,
                'analysis' => $analysisResults,
                'buttons' => $analysisResults[0]['suggestions'] ?? $this->getDefaultMediaSuggestions()
            ];
        }

        private function getFileTypeLabel($fileType)
        {
            $labels = [
                'image' => '🖼️ Hình ảnh',
                'video' => '🎥 Video',
                'document' => '📄 Tài liệu',
                'other' => '📁 File'
            ];

            return $labels[$fileType] ?? '📁 File';
        }

        private function getDefaultMediaSuggestions()
        {
            return [
                ['text' => '🛍️ Tìm sản phẩm', 'type' => 'find_similar_products'],
                ['text' => '🔧 Báo lỗi', 'type' => 'report_issue'],
                ['text' => '💬 Mô tả file', 'type' => 'describe_files'],
                ['text' => '📞 Hỗ trợ thêm', 'type' => 'contact_support']
            ];
        }

        // 🔹 Phần tra cứu đơn hàng, ý định, phản hồi AI
        private function handleOrderLookup($message)
        {
            $orderInfo = $this->orderLookupService->extractOrderInfoFromMessage($message);
            
            $orderCode = $orderInfo['order_code'];
            $phone = $orderInfo['phone'];
            $fullName = $orderInfo['full_name'];

            if ($orderCode || $phone) {
                $result = $this->orderLookupService->lookupOrder($orderCode, $phone, $fullName);
                if ($result['found']) {
                    return $this->formatOrderLookupResponse($result);
                } else {
                    return [
                        'message' => $result['message'] . "\n\nVui lòng cung cấp:\n• 📦 Mã đơn hàng\n• 📞 Số điện thoại\n• 👤 Họ tên đầy đủ",
                        'type' => 'order_not_found',
                        'intent' => 'order_lookup',
                        'confidence' => 1.0,
                        'buttons' => [
                            ['text' => '📦 Nhập mã đơn hàng', 'type' => 'input_order'],
                            ['text' => '📞 Nhập số điện thoại', 'type' => 'input_phone'],
                            ['text' => '👤 Nhập họ tên', 'type' => 'input_name']
                        ]
                    ];
                }
            }

            return [
                'message' => "Để tra cứu đơn hàng, vui lòng cung cấp thông tin:\n\n📦 **Mã đơn hàng** (nếu có)\n📞 **Số điện thoại** đặt hàng\n👤 **Họ tên** khách hàng\n\nBạn có thể cung cấp một hoặc nhiều thông tin trên.",
                'type' => 'order_info_request',
                'intent' => 'order_lookup',
                'confidence' => 0.9,
                'buttons' => [
                    ['text' => '📦 Nhập mã đơn hàng', 'type' => 'input_order'],
                    ['text' => '📞 Nhập số điện thoại', 'type' => 'input_phone'],
                    ['text' => '👤 Nhập họ tên', 'type' => 'input_name'],
                    ['text' => '🔍 Tra cứu tất cả', 'type' => 'lookup_all']
                ]
            ];
        }

        private function formatOrderLookupResponse($result)
        {
            $orders = $result['orders'];
            $totalOrders = $result['total_orders'];

            if ($totalOrders === 1) {
                $order = $orders[0];
                $message = "✅ **Tìm thấy đơn hàng của bạn!**\n\n";
                $message .= "📦 **Mã đơn hàng:** {$order['order_code']}\n";
                $message .= "👤 **Khách hàng:** {$order['customer_name']}\n";
                $message .= "📞 **SĐT:** {$order['phone_number']}\n";
                $message .= "💰 **Tổng tiền:** {$order['total_amount']}\n";
                $message .= "📮 **Địa chỉ:** {$order['shipping_address']}\n";
                $message .= "🕒 **Ngày đặt:** {$order['created_at']}\n";
                $message .= "📅 **Dự kiến giao:** {$order['estimated_delivery']}\n";
                $message .= "{$order['status_icon']} **Trạng thái:** {$order['status']}\n";
                $message .= "💬 *{$order['status_description']}*\n";
                $message .= "💳 **Thanh toán:** {$order['payment_status']}";
            } else {
                $message = "✅ **Tìm thấy {$totalOrders} đơn hàng:**\n\n";
                foreach ($orders as $index => $order) {
                    $message .= "**Đơn hàng " . ($index + 1) . ":**\n";
                    $message .= "📦 {$order['order_code']} | {$order['status_icon']} {$order['status']}\n";
                    $message .= "💰 {$order['total_amount']} | 🕒 {$order['created_at']}\n";
                    $message .= "---\n";
                }
                $message .= "\n💡 *Chọn một đơn hàng để xem chi tiết*";
            }

            return [
                'message' => $message,
                'type' => 'order_found',
                'intent' => 'order_lookup',
                'confidence' => 1.0,
                'data' => $orders,
                'buttons' => [
                    ['text' => '📦 Tra cứu đơn khác', 'type' => 'order_lookup'],
                    ['text' => '🛍️ Mua sắm', 'type' => 'product_inquiry'],
                    ['text' => '💬 Hỗ trợ thêm', 'type' => 'contact_support']
                ]
            ];
        }

        private function detectIntent($message)
        {
            $intents = [
                'order_lookup' => ['đơn hàng', 'mã đơn', 'tra cứu', 'trạng thái', 'vận chuyển', 'order', 'đh', 'dh'],
                'product_inquiry' => ['sản phẩm', 'hàng', 'còn không', 'giá', 'thông số', 'chất liệu', 'mua'],
                'shipping_info' => ['ship', 'phí ship', 'vận chuyển', 'giao hàng', 'thời gian', 'shipping'],
                'faq' => ['đổi trả', 'bảo hành', 'chính sách', 'faq', 'hỏi đáp'],
                'greeting' => ['xin chào', 'hello', 'hi', 'chào', 'có ai không'],
                'thanks' => ['cảm ơn', 'thanks', 'thank you'],
            ];

            foreach ($intents as $intent => $keywords) {
                foreach ($keywords as $keyword) {
                    if (strpos($message, $keyword) !== false) {
                        return $intent;
                    }
                }
            }

            return 'general';
        }

        private function generateResponse($intent, $originalMessage)
        {
            $responses = [
                'product_inquiry' => [
                    'message' => "Tôi có thể giúp bạn tìm kiếm thông tin sản phẩm. Bạn đang quan tâm đến sản phẩm nào?",
                    'type' => 'product_help',
                    'intent' => 'product_inquiry',
                    'confidence' => 0.9
                ],
                'shipping_info' => [
                    'message' => "🚚 **Phí ship:** Nội thành 20k, Ngoại thành 30k.\n⏰ Giao 1-3 ngày.",
                    'type' => 'shipping_info',
                    'intent' => 'shipping_info',
                    'confidence' => 1.0
                ],
                'greeting' => [
                    'message' => "👋 Xin chào! Tôi là trợ lý AI của cửa hàng. Bạn cần tôi giúp gì nào?",
                    'type' => 'greeting',
                    'intent' => 'greeting',
                    'confidence' => 1.0
                ],
                'thanks' => [
                    'message' => "Cảm ơn bạn! ❤️ Chúc bạn một ngày tốt lành!",
                    'type' => 'thanks',
                    'intent' => 'thanks',
                    'confidence' => 1.0
                ],
                'general' => [
                    'message' => "Tôi hiểu bạn nói: \"{$originalMessage}\". Hãy nói rõ hơn để tôi có thể giúp nhé!",
                    'type' => 'general',
                    'intent' => 'general',
                    'confidence' => 0.7
                ]
            ];

            return $responses[$intent] ?? $responses['general'];
        }

        public function getChatHistory($sessionId)
        {
            try {
                $conversations = AIConversation::where('session_id', $sessionId)
                    ->orderBy('created_at', 'asc')
                    ->get();

                return response()->json([
                    'success' => true,
                    'conversations' => $conversations
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi tải lịch sử chat'
                ], 500);
            }
        }

        public function clearChatHistory($sessionId)
        {
            try {
                AIConversation::where('session_id', $sessionId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa lịch sử trò chuyện'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi xóa lịch sử'
                ], 500);
            }
        }
        // Thêm vào AIChatController.php
private function handleFAQ($message, $intentAnalysis)
{
    // Sử dụng AI Training Service để tìm câu trả lời
    $result = app(AITrainingService::class)->findBestMatch($message);
    
    if ($result) {
        return [
            'type' => 'faq_response',
            'message' => $result['question']->answer,
            'intent' => 'faq',
            'confidence' => $result['score'],
            'source' => 'ai_training',
            'data' => [
                'question_id' => $result['question']->id,
                'category' => $result['question']->category,
                'tags' => $result['question']->tags
            ]
        ];
    }
    
    // Fallback to general FAQ response
    return $this->handleGeneralInquiry($message, $intentAnalysis);
}
    }
