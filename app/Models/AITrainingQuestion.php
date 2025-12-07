<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AITrainingQuestion extends Model
{
    protected $table = 'ai_training_questions';
    
    protected $fillable = [
        'question',
        'answer',
        'category',
        'keywords',
        'priority',
        'is_active'
    ];
    
    protected $casts = [
        'keywords' => 'array', // QUAN TRỌNG: Cast JSON sang array
        'is_active' => 'boolean',
        'priority' => 'integer'
    ];
    
    /**
     * Accessor: Đảm bảo keywords luôn là array
     */
    public function getKeywordsAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }
        
        if (is_array($value)) {
            return $value;
        }
        
        // Nếu là string, decode JSON
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }
    
    /**
     * Mutator: Chuyển array sang JSON khi lưu
     */
    public function setKeywordsAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['keywords'] = json_encode([]);
        } elseif (is_array($value)) {
            $this->attributes['keywords'] = json_encode($value);
        } elseif (is_string($value)) {
            // Nếu là string, cố gắng explode nếu là danh sách từ khóa
            $keywords = array_map('trim', explode(',', $value));
            $this->attributes['keywords'] = json_encode($keywords);
        } else {
            $this->attributes['keywords'] = json_encode([]);
        }
    }
    
    // Phương thức scope để lọc câu hỏi active
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    // Phương thức tìm câu hỏi tương tự
    public static function findSimilar($question, $limit = 5)
    {
        return self::active()
            ->where('question', 'like', '%' . $question . '%')
            ->orWhere(function($query) use ($question) {
                // Tìm trong keywords nếu là array
                $query->whereJsonContains('keywords', $question);
            })
            ->limit($limit)
            ->get();
    }
    
    /**
     * Helper: Lấy keywords dưới dạng string
     */
    public function getKeywordsAsString()
    {
        return implode(', ', $this->keywords);
    }
}