<?php

namespace App\Exports;

use App\Models\AITrainingQuestion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AITrainingExport implements FromCollection, WithHeadings, WithMapping
{
    protected $category;
    protected $status;
    
    public function __construct($category = null, $status = null)
    {
        $this->category = $category;
        $this->status = $status;
    }
    
    public function collection()
    {
        $query = AITrainingQuestion::query();
        
        if ($this->category) {
            $query->where('category', $this->category);
        }
        
        if ($this->status === 'active') {
            $query->where('is_active', true);
        } elseif ($this->status === 'inactive') {
            $query->where('is_active', false);
        }
        
        return $query->orderBy('category')
                    ->orderBy('priority', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Câu hỏi',
            'Câu trả lời',
            'Danh mục',
            'Từ khóa',
            'Độ ưu tiên',
            'Trạng thái',
            'Ngày tạo'
        ];
    }
    
    public function map($question): array
    {
        // FIX: Xử lý keywords đúng cách
        $keywords = '';
        if ($question->keywords) {
            // Nếu keywords là array (đã được cast từ JSON)
            if (is_array($question->keywords)) {
                $keywords = implode(', ', $question->keywords);
            } 
            // Nếu keywords là string JSON
            elseif (is_string($question->keywords)) {
                $decoded = json_decode($question->keywords, true);
                if (is_array($decoded)) {
                    $keywords = implode(', ', $decoded);
                } else {
                    $keywords = $question->keywords;
                }
            }
            // Trường hợp khác
            else {
                $keywords = (string) $question->keywords;
            }
        }
        
        return [
            $question->id,
            $question->question,
            $question->answer,
            $question->category ?? 'Khác',
            $keywords,
            $question->priority,
            $question->is_active ? 'Active' : 'Inactive',
            $question->created_at->format('d/m/Y H:i')
        ];
    }
}