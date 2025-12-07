<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AITrainingQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AITrainingApiController extends Controller
{
    // Lấy câu hỏi gợi ý
    public function getSuggestedQuestions(Request $request)
    {
        $limit = $request->get('limit', 6);
        
        $questions = AITrainingQuestion::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
            
        return response()->json([
            'success' => true,
            'questions' => $questions
        ]);
    }
    
    // Tìm kiếm câu hỏi
    public function searchQuestions(Request $request)
    {
        $keyword = $request->get('keyword', '');
        $limit = $request->get('limit', 5);
        
        if (empty($keyword)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập từ khóa tìm kiếm'
            ]);
        }
        
        $questions = AITrainingQuestion::where('is_active', true)
            ->where(function($query) use ($keyword) {
                $query->where('question', 'like', '%' . $keyword . '%')
                      ->orWhere('answer', 'like', '%' . $keyword . '%')
                      ->orWhere('category', 'like', '%' . $keyword . '%')
                      ->orWhereJsonContains('keywords', $keyword);
            })
            ->orderBy('priority', 'desc')
            ->limit($limit)
            ->get();
            
        return response()->json([
            'success' => true,
            'questions' => $questions,
            'count' => $questions->count()
        ]);
    }
    
    // Lấy thống kê
    public function getStatistics()
    {
        $total = AITrainingQuestion::count();
        $active = AITrainingQuestion::where('is_active', true)->count();
        $categories = AITrainingQuestion::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();
            
        return response()->json([
            'success' => true,
            'total' => $total,
            'active' => $active,
            'categories' => $categories
        ]);
    }
    
    // Lấy danh sách categories
    public function getCategories()
    {
        $categories = AITrainingQuestion::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');
            
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
}