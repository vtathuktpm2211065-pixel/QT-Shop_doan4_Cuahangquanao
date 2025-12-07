<?php

namespace App\Imports;

use App\Models\AITrainingQuestion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class AiTrainingImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private $importedCount = 0;
    private $updatedCount = 0;
    private $skippedCount = 0;
    private $importErrors = []; // Đổi tên từ $errors sang $importErrors
    private $failures = [];
    
    public function model(array $row)
    {
        try {
            // Phiên bản 2.x dùng mảng với key là tên cột
            // Hỗ trợ cả tiếng Việt không dấu (do Excel có thể mất dấu)
            $question = $this->getValue($row, ['cau_hoi', 'question', 'cau hoi', 'Câu hỏi']);
            $answer = $this->getValue($row, ['cau_tra_loi', 'answer', 'cau tra loi', 'Câu trả lời']);
            $category = $this->getValue($row, ['danh_muc', 'category', 'danh muc', 'Danh mục']);
            $keywords = $this->getValue($row, ['tu_khoa', 'keywords', 'tu khoa', 'Từ khóa']);
            $priority = $this->getValue($row, ['do_uu_tien', 'priority', 'do uu tien', 'Độ ưu tiên']);
            
            // Bỏ qua hàng trống
            if (empty($question) || empty($answer)) {
                $this->skippedCount++;
                return null;
            }
            
            // Validate cơ bản
            if (strlen($question) > 1000) {
                $this->importErrors[] = "Câu hỏi quá dài: " . substr($question, 0, 50) . "...";
                $this->skippedCount++;
                return null;
            }
            
            // Xử lý từ khóa
            $keywordsArray = [];
            if (!empty($keywords)) {
                $keywordsArray = array_map('trim', explode(',', $keywords));
                $keywordsArray = array_filter($keywordsArray);
            }
            
            // Xử lý danh mục
            $category = $category ?: 'Khác';
            
            // Xử lý độ ưu tiên
            $priority = $this->parsePriority($priority);
            
            // Kiểm tra trùng lặp (không phân biệt hoa thường)
            $existing = AITrainingQuestion::whereRaw('LOWER(question) = ?', [strtolower(trim($question))])
                ->first();
            
            if ($existing) {
                // Update câu hỏi đã tồn tại
                $existing->update([
                    'answer' => trim($answer),
                    'category' => trim($category),
                    'keywords' => $keywordsArray,
                    'priority' => $priority,
                    'is_active' => true,
                    'updated_at' => now()
                ]);
                $this->updatedCount++;
                return null;
            } else {
                // Tạo câu hỏi mới
                $this->importedCount++;
                return new AITrainingQuestion([
                    'question' => trim($question),
                    'answer' => trim($answer),
                    'category' => trim($category),
                    'keywords' => $keywordsArray,
                    'priority' => $priority,
                    'is_active' => true
                ]);
            }
            
        } catch (Throwable $e) {
            $this->skippedCount++;
            $this->importErrors[] = "Lỗi: " . $e->getMessage();
            \Log::error('Import AI Training Error', [
                'error' => $e->getMessage(),
                'row' => $row
            ]);
            return null;
        }
    }
    
    /**
     * Lấy giá trị từ mảng với nhiều key có thể
     */
    private function getValue($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
            }
        }
        return null;
    }
    
    /**
     * Parse độ ưu tiên
     */
    private function parsePriority($priority)
    {
        if (is_numeric($priority)) {
            $priority = intval($priority);
        } elseif (is_string($priority)) {
            $priority = intval(trim($priority));
        } else {
            $priority = 1;
        }
        
        if ($priority < 1) $priority = 1;
        if ($priority > 5) $priority = 5;
        
        return $priority;
    }
    
    /**
     * Implement SkipsOnError interface
     */
    public function onError(Throwable $e)
    {
        $this->importErrors[] = $e->getMessage();
    }
    
    /**
     * Implement SkipsOnFailure interface
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = $failure;
        }
    }
    
    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'cau_hoi' => 'nullable|string|max:1000',
            'question' => 'nullable|string|max:1000',
            'cau_tra_loi' => 'nullable|string|max:2000',
            'answer' => 'nullable|string|max:2000',
            'danh_muc' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'tu_khoa' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'do_uu_tien' => 'nullable|integer|min:1|max:5',
            'priority' => 'nullable|integer|min:1|max:5'
        ];
    }
    
    public function getImportedCount()
    {
        return $this->importedCount;
    }
    
    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }
    
    public function getSkippedCount()
    {
        return $this->skippedCount;
    }
    
    public function getImportErrors()
    {
        return $this->importErrors;
    }
    
    public function getFailures()
    {
        return $this->failures;
    }
    
    public function getStats()
    {
        return [
            'imported' => $this->importedCount,
            'updated' => $this->updatedCount,
            'skipped' => $this->skippedCount,
            'total' => $this->importedCount + $this->updatedCount + $this->skippedCount,
            'errors' => $this->importErrors,
            'failures' => $this->failures
        ];
    }
}