<?php
// app/Services/AITrainingService.php

namespace App\Services;

use App\Models\AITrainingQuestion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AITrainingService
{
    protected $embeddingService;
    
    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }
    
    /**
     * Xử lý import từ Excel
     */
    public function importFromExcel(array $data)
    {
        $processed = 0;
        $errors = [];
        
        foreach ($data as $index => $item) {
            try {
                // Chuẩn hóa dữ liệu
                $question = $this->normalizeText($item['question'] ?? '');
                $answer = $this->normalizeText($item['answer'] ?? '');
                
                if (empty($question) || empty($answer)) {
                    $errors[] = "Dòng {$index}: Câu hỏi hoặc câu trả lời trống";
                    continue;
                }
                
                // Kiểm tra trùng lặp
                $existing = AITrainingQuestion::where('question', $question)->first();
                
                if ($existing) {
                    // Cập nhật câu hỏi đã tồn tại
                    $existing->update([
                        'answer' => $answer,
                        'category' => $item['category'] ?? 'general',
                        'intent' => $item['intent'] ?? 'faq',
                        'tags' => $this->extractTags($question),
                    ]);
                    
                    // Cập nhật embedding nếu cần
                    $this->updateEmbedding($existing);
                } else {
                    // Tạo mới
                    $embedding = $this->embeddingService->generateEmbedding($question);
                    
                    AITrainingQuestion::create([
                        'question' => $question,
                        'answer' => $answer,
                        'category' => $item['category'] ?? 'general',
                        'intent' => $item['intent'] ?? 'faq',
                        'tags' => $this->extractTags($question),
                        'embedding' => $embedding,
                        'is_active' => true,
                        'created_by' => auth()->id(),
                    ]);
                    
                    $processed++;
                }
            } catch (\Exception $e) {
                $errors[] = "Dòng {$index}: " . $e->getMessage();
            }
        }
        
        return [
            'processed' => $processed,
            'errors' => $errors,
        ];
    }
    
    /**
     * Tìm kiếm câu trả lời phù hợp
     */
    public function findBestMatch($userQuery, $threshold = 0.75)
    {
        $userQuery = $this->normalizeText($userQuery);
        
        // Phương án 1: Tìm kiếm exact match
        $exactMatch = AITrainingQuestion::where('question', $userQuery)
            ->where('is_active', true)
            ->first();
            
        if ($exactMatch) {
            $exactMatch->increment('usage_count');
            return [
                'question' => $exactMatch,
                'score' => 1.0,
                'method' => 'exact'
            ];
        }
        
        // Phương án 2: Tìm kiếm với embeddings
        $userEmbedding = $this->embeddingService->generateEmbedding($userQuery);
        
        $allQuestions = AITrainingQuestion::where('is_active', true)->get();
        
        $bestMatch = null;
        $bestScore = 0;
        
        foreach ($allQuestions as $question) {
            if (!$question->embedding) {
                continue;
            }
            
            $score = $this->embeddingService->cosineSimilarity(
                $userEmbedding, 
                $question->embedding
            );
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $question;
            }
        }
        
        if ($bestMatch && $bestScore >= $threshold) {
            $bestMatch->increment('usage_count');
            return [
                'question' => $bestMatch,
                'score' => $bestScore,
                'method' => 'embedding'
            ];
        }
        
        // Phương án 3: Tìm kiếm với TF-IDF hoặc keyword matching
        $keywords = $this->extractKeywords($userQuery);
        
        $keywordMatches = AITrainingQuestion::where('is_active', true)
            ->where(function($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('question', 'LIKE', "%{$keyword}%");
                }
            })
            ->get();
            
        if ($keywordMatches->count() > 0) {
            $bestKeywordMatch = $this->findBestKeywordMatch($userQuery, $keywordMatches);
            
            if ($bestKeywordMatch) {
                $bestKeywordMatch->increment('usage_count');
                return [
                    'question' => $bestKeywordMatch,
                    'score' => 0.6, // Score trung bình cho keyword matching
                    'method' => 'keyword'
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Chuẩn hóa văn bản tiếng Việt
     */
    private function normalizeText($text)
    {
        // Chuyển về chữ thường
        $text = mb_strtolower($text, 'UTF-8');
        
        // Loại bỏ khoảng trắng thừa
        $text = preg_replace('/\s+/', ' ', trim($text));
        
        // Chuẩn hóa dấu câu
        $text = str_replace(['?', '!', '.'], '', $text);
        
        // Xử lý unicode
        $text = \Normalizer::normalize($text, \Normalizer::FORM_C);
        
        // Loại bỏ stopwords (có thể tùy chỉnh)
        $text = $this->removeVietnameseStopwords($text);
        
        return $text;
    }
    
    /**
     * Trích xuất tags từ câu hỏi
     */
    private function extractTags($question)
    {
        $tags = [];
        $keywords = [
            'giá', 'giá cả', 'tiền',
            'size', 'kích thước', 'mẫu mã',
            'màu', 'màu sắc',
            'đổi', 'trả', 'hoàn tiền',
            'ship', 'vận chuyển', 'giao hàng',
            'thanh toán', 'payment',
            'bảo hành', 'warranty',
            'chất liệu', 'vải', 'material',
        ];
        
        foreach ($keywords as $keyword) {
            if (strpos($question, $keyword) !== false) {
                $tags[] = $keyword;
            }
        }
        
        return array_unique($tags);
    }
    
    /**
     * Trích xuất keywords
     */
    private function extractKeywords($text)
    {
        $words = explode(' ', $text);
        $keywords = array_filter($words, function($word) {
            // Loại bỏ các từ quá ngắn và stopwords
            return strlen($word) > 2 && !$this->isStopword($word);
        });
        
        return array_values($keywords);
    }
    
    /**
     * Tìm best match với keyword
     */
    private function findBestKeywordMatch($userQuery, $candidates)
    {
        $userWords = explode(' ', $userQuery);
        $bestMatch = null;
        $bestScore = 0;
        
        foreach ($candidates as $candidate) {
            $candidateWords = explode(' ', $candidate->question);
            
            // Tính Jaccard similarity
            $intersection = count(array_intersect($userWords, $candidateWords));
            $union = count(array_unique(array_merge($userWords, $candidateWords)));
            
            if ($union > 0) {
                $score = $intersection / $union;
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $candidate;
                }
            }
        }
        
        return $bestMatch;
    }
    
    /**
     * Cập nhật embedding cho câu hỏi
     */
    private function updateEmbedding($question)
    {
        try {
            $embedding = $this->embeddingService->generateEmbedding($question->question);
            $question->update(['embedding' => $embedding]);
        } catch (\Exception $e) {
            Log::error("Error updating embedding: " . $e->getMessage());
        }
    }
    
    /**
     * Loại bỏ stopwords tiếng Việt
     */
    private function removeVietnameseStopwords($text)
    {
        $stopwords = [
            'có', 'của', 'và', 'là', 'tại', 'các', 'đã', 'được',
            'cho', 'với', 'như', 'không', 'này', 'nọ', 'kia',
            'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín', 'mười',
            'gì', 'nào', 'sao', 'vì', 'do', 'bởi', 'từ',
        ];
        
        $words = explode(' ', $text);
        $filtered = array_filter($words, function($word) use ($stopwords) {
            return !in_array($word, $stopwords);
        });
        
        return implode(' ', $filtered);
    }
    
    private function isStopword($word)
    {
        $stopwords = ['có', 'của', 'và', 'là', 'tại'];
        return in_array($word, $stopwords);
    }
}