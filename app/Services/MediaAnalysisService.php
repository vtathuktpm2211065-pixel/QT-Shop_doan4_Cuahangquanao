<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MediaAnalysisService
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function analyzeMedia($files, $message = '')
    {
        $analysisResults = [];

        foreach ($files as $file) {
            $analysis = $this->analyzeSingleFile($file, $message);
            if ($analysis) {
                $analysisResults[] = $analysis;
            }
        }

        return $analysisResults;
    }

    private function analyzeSingleFile($file, $message)
    {
        $fileInfo = pathinfo($file->getClientOriginalName());
        $extension = strtolower($fileInfo['extension'] ?? '');
        $fileType = $this->getFileType($extension);

        $baseAnalysis = [
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $fileType,
            'file_size' => $this->formatFileSize($file->getSize()),
            'extension' => $extension,
        ];

        switch ($fileType) {
            case 'image':
                return array_merge($baseAnalysis, $this->analyzeImage($file, $message));
            case 'video':
                return array_merge($baseAnalysis, $this->analyzeVideo($file, $message));
            case 'document':
                return array_merge($baseAnalysis, $this->analyzeDocument($file, $message));
            default:
                return array_merge($baseAnalysis, [
                    'analysis' => 'file_uploaded',
                    'message' => 'Đã nhận file của bạn. Bạn muốn tôi giúp gì với file này?',
                    'suggestions' => $this->getGeneralSuggestions()
                ]);
        }
    }

    private function analyzeImage($file, $message)
    {
        // Phân tích cơ bản ảnh
        try {
            $imageInfo = getimagesize($file->getPathname());
            $width = $imageInfo[0] ?? 0;
            $height = $imageInfo[1] ?? 0;
            $mimeType = $imageInfo['mime'] ?? '';

            $analysis = [
                'analysis' => 'image_analysis',
                'dimensions' => "{$width}x{$height}",
                'message' => $this->generateImageResponse($message, $width, $height),
                'suggestions' => $this->getImageSuggestions($message),
                'is_high_quality' => $width >= 1000 && $height >= 1000,
            ];

            // Kiểm tra nếu có thể là ảnh sản phẩm
            if ($this->mightBeProductImage($message, $width, $height)) {
                $analysis['likely_type'] = 'product_image';
                $analysis['message'] = "📸 **Ảnh sản phẩm**\n\nTôi thấy bạn gửi ảnh sản phẩm. Bạn muốn:\n• 🛍️ Tìm sản phẩm tương tự\n• 💰 Hỏi giá sản phẩm\n• 📝 Mô tả sản phẩm\n• 🔍 Kiểm tra tồn kho";
            }

            return $analysis;

        } catch (\Exception $e) {
            Log::error('Image analysis error: ' . $e->getMessage());
            return [
                'analysis' => 'image_uploaded',
                'message' => 'Đã nhận ảnh của bạn! Bạn muốn tôi giúp gì với ảnh này?',
                'suggestions' => $this->getImageSuggestions($message)
            ];
        }
    }

    private function analyzeVideo($file, $message)
    {
        return [
            'analysis' => 'video_uploaded',
            'message' => "🎥 **Video đã được tải lên**\n\nTôi đã nhận video của bạn. Bạn muốn:\n• 📋 Mô tả nội dung video\n• 🛍️ Tìm sản phẩm trong video\n• 🔗 Chia sẻ video\n• 💬 Bình luận về video",
            'suggestions' => [
                ['text' => '📋 Mô tả video', 'type' => 'describe_video'],
                ['text' => '🛍️ Tìm sản phẩm', 'type' => 'find_products_in_video'],
                ['text' => '💬 Hỗ trợ khác', 'type' => 'other_support']
            ]
        ];
    }

    private function analyzeDocument($file, $message)
    {
        $docType = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        
        $typeMessages = [
            'pdf' => "📄 **File PDF**\n\nTôi đã nhận file PDF của bạn.",
            'doc' => "📝 **File tài liệu**\n\nTôi đã nhận file Word của bạn.",
            'docx' => "📝 **File tài liệu**\n\nTôi đã nhận file Word của bạn.",
            'txt' => "📋 **File văn bản**\n\nTôi đã nhận file văn bản của bạn."
        ];

        return [
            'analysis' => 'document_uploaded',
            'message' => $typeMessages[$docType] ?? "📁 **File tài liệu**\n\nTôi đã nhận file của bạn.",
            'suggestions' => [
                ['text' => '📖 Đọc nội dung', 'type' => 'read_document'],
                ['text' => '🔍 Phân tích file', 'type' => 'analyze_document'],
                ['text' => '💬 Hỗ trợ khác', 'type' => 'other_support']
            ]
        ];
    }

    private function generateImageResponse($message, $width, $height)
    {
        $message = strtolower($message);
        
        if (strpos($message, 'sản phẩm') !== false || strpos($message, 'hàng') !== false) {
            return "🛍️ **Ảnh sản phẩm**\n\nTôi thấy bạn gửi ảnh sản phẩm. Bạn muốn tôi giúp gì?\n\n• Tìm sản phẩm tương tự\n• Kiểm tra giá\n• Xem thông tin chi tiết\n• Kiểm tra tồn kho";
        }

        if (strpos($message, 'lỗi') !== false || strpos($message, 'hỏng') !== false) {
            return "🔧 **Ảnh báo lỗi**\n\nTôi thấy bạn gửi ảnh về vấn đề/sự cố. Bạn cần:\n\n• Hỗ trợ kỹ thuật\n• Hướng dẫn sửa chữa\n• Liên hệ nhân viên\n• Đổi trả sản phẩm";
        }

        return "📸 **Ảnh đã được tải lên**\n\nĐộ phân giải: {$width}x{$height}\n\nBạn muốn tôi giúp gì với ảnh này?\n• 🛍️ Tìm sản phẩm tương tự\n• 💬 Mô tả ảnh\n• 🔧 Báo lỗi/sự cố\n• 📝 Ghi chú về ảnh";
    }

    private function getImageSuggestions($message)
    {
        $message = strtolower($message);
        
        if (strpos($message, 'sản phẩm') !== false) {
            return [
                ['text' => '🛍️ Tìm sản phẩm tương tự', 'type' => 'find_similar_products'],
                ['text' => '💰 Hỏi giá', 'type' => 'ask_price'],
                ['text' => '📊 Kiểm tra tồn kho', 'type' => 'check_stock']
            ];
        }

        if (strpos($message, 'lỗi') !== false) {
            return [
                ['text' => '🔧 Hỗ trợ kỹ thuật', 'type' => 'technical_support'],
                ['text' => '📞 Liên hệ nhân viên', 'type' => 'contact_support'],
                ['text' => '🔄 Đổi trả', 'type' => 'return_product']
            ];
        }

        return [
            ['text' => '🛍️ Tìm sản phẩm', 'type' => 'find_similar_products'],
            ['text' => '💬 Mô tả ảnh', 'type' => 'describe_image'],
            ['text' => '🔧 Báo lỗi', 'type' => 'report_issue'],
            ['text' => '📝 Ghi chú', 'type' => 'add_note']
        ];
    }

    private function getGeneralSuggestions()
    {
        return [
            ['text' => '🛍️ Tư vấn sản phẩm', 'type' => 'product_inquiry'],
            ['text' => '🔧 Hỗ trợ kỹ thuật', 'type' => 'technical_support'],
            ['text' => '📞 Liên hệ nhân viên', 'type' => 'contact_support']
        ];
    }

    private function mightBeProductImage($message, $width, $height)
    {
        $message = strtolower($message);
        $isProductKeywords = strpos($message, 'sản phẩm') !== false || 
                           strpos($message, 'hàng') !== false ||
                           strpos($message, 'mua') !== false;
        
        $isGoodQuality = $width >= 800 && $height >= 800;
        
        return $isProductKeywords && $isGoodQuality;
    }

    private function getFileType($extension)
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $videoTypes = ['mp4', 'mov', 'avi', 'mkv', 'wmv'];
        $documentTypes = ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx'];

        if (in_array($extension, $imageTypes)) return 'image';
        if (in_array($extension, $videoTypes)) return 'video';
        if (in_array($extension, $documentTypes)) return 'document';
        return 'other';
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}