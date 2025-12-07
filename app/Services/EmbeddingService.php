<?php
// app/Services/EmbeddingService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    protected $apiKey;
    protected $apiUrl;
    
    public function __construct()
    {
        // Có thể dùng OpenAI, Sentence Transformers API, hoặc local model
        $this->apiKey = config('services.openai.api_key');
        $this->apiUrl = 'https://api.openai.com/v1/embeddings';
        
        // Hoặc dùng local sentence-transformers
        // $this->apiUrl = 'http://localhost:8000/embed'; // Nếu chạy local model
    }
    
    /**
     * Generate embedding vector
     */
    public function generateEmbedding($text)
    {
        try {
            // Nếu có OpenAI API
            if ($this->apiKey && config('services.openai.enabled')) {
                return $this->generateOpenAIEmbedding($text);
            }
            
            // Hoặc dùng local sentence-transformers
            return $this->generateLocalEmbedding($text);
            
        } catch (\Exception $e) {
            Log::error("Embedding generation failed: " . $e->getMessage());
            
            // Fallback: return simple TF-IDF like vector
            return $this->generateSimpleVector($text);
        }
    }
    
    /**
     * Generate embedding using OpenAI
     */
    private function generateOpenAIEmbedding($text)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => 'text-embedding-ada-002',
            'input' => $text,
        ]);
        
        if ($response->successful()) {
            return $response->json()['data'][0]['embedding'];
        }
        
        throw new \Exception('OpenAI API error: ' . $response->body());
    }
    
    /**
     * Generate embedding using local model (sentence-transformers)
     */
    private function generateLocalEmbedding($text)
    {
        // Giả sử bạn đang chạy một local API với sentence-transformers
        $response = Http::post('http://localhost:8000/embed', [
            'text' => $text,
            'model' => 'sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2'
        ]);
        
        if ($response->successful()) {
            return $response->json()['embedding'];
        }
        
        // Fallback to simple vector
        return $this->generateSimpleVector($text);
    }
    
    /**
     * Simple vector for fallback
     */
    private function generateSimpleVector($text)
    {
        // Simple bag-of-words like vector (for demonstration)
        $words = explode(' ', strtolower($text));
        $uniqueWords = array_unique($words);
        
        // Create a simple hash-based vector of length 50
        $vector = array_fill(0, 50, 0);
        
        foreach ($uniqueWords as $word) {
            $hash = crc32($word) % 50;
            $vector[$hash] += 1;
        }
        
        // Normalize
        $norm = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $vector)));
        if ($norm > 0) {
            $vector = array_map(function($x) use ($norm) { return $x / $norm; }, $vector);
        }
        
        return $vector;
    }
    
    /**
     * Calculate cosine similarity between two vectors
     */
    public function cosineSimilarity($vectorA, $vectorB)
    {
        if (!is_array($vectorA) || !is_array($vectorB)) {
            return 0;
        }
        
        if (count($vectorA) !== count($vectorB)) {
            return 0;
        }
        
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;
        
        for ($i = 0; $i < count($vectorA); $i++) {
            $dotProduct += $vectorA[$i] * $vectorB[$i];
            $normA += $vectorA[$i] * $vectorA[$i];
            $normB += $vectorB[$i] * $vectorB[$i];
        }
        
        if ($normA == 0 || $normB == 0) {
            return 0;
        }
        
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}