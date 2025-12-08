<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AITrainingQuestion;
use Illuminate\Support\Facades\DB;

class FixAITrainingKeywords extends Command
{
    protected $signature = 'ai-training:fix-keywords';
    protected $description = 'Fix keywords format in AI training questions';
    
    public function handle()
    {
        $questions = AITrainingQuestion::all();
        $fixedCount = 0;
        
        foreach ($questions as $question) {
            $original = $question->keywords;
            
            if (is_string($original)) {
                // Thử decode JSON
                $decoded = json_decode($original, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // Nếu đã là JSON array hợp lệ, không cần fix
                    continue;
                } else {
                    // Nếu là string thông thường, chuyển thành array
                    $keywordsArray = array_map('trim', explode(',', $original));
                    $keywordsArray = array_filter($keywordsArray);
                    
                    $question->keywords = $keywordsArray;
                    $question->save();
                    
                    $fixedCount++;
                    $this->info("Fixed question ID {$question->id}: {$original} -> " . json_encode($keywordsArray));
                }
            } elseif (is_array($original)) {
                // Nếu đã là array, đảm bảo lưu đúng
                $question->keywords = $original;
                $question->save();
            }
        }
        
        $this->info("Fixed {$fixedCount} questions.");
        return 0;
    }
}