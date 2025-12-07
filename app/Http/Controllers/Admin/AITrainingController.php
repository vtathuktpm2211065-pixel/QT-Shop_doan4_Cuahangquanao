<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AITrainingQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AiTrainingImport;
use App\Exports\AITrainingExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AITrainingController extends Controller
{
    /**
     * Hiển thị danh sách câu hỏi
     */
    public function index()
    {
        $questions = AITrainingQuestion::orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Lấy thống kê
        $totalQuestions = AITrainingQuestion::count();
        $activeQuestions = AITrainingQuestion::where('is_active', true)->count();
        $highPriorityQuestions = AITrainingQuestion::where('priority', '>=', 4)->count();
        
        // Lấy danh sách categories
        $categories = AITrainingQuestion::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');
        
        return view('admin.ai-training.index', compact(
            'questions',
            'totalQuestions',
            'activeQuestions',
            'highPriorityQuestions',
            'categories'
        ));
    }
    
    /**
     * API Edit cho AJAX (quick edit)
     */
    public function edit($id)
    {
        $question = AITrainingQuestion::findOrFail($id);
        return response()->json($question);
    }
    
    /**
     * Hiển thị form thêm mới
     */
    public function create()
    {
        return view('admin.ai-training.create');
    }
    
    /**
     * Lưu câu hỏi mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500|unique:ai_training_questions,question',
            'answer' => 'required|string|max:2000',
            'category' => 'required|string|max:100',
            'keywords' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'is_active' => 'boolean'
        ]);
        
        // Xử lý category
        if ($request->category === 'other' && $request->filled('category_other')) {
            $validated['category'] = $request->category_other;
        }
        
        // Xử lý keywords
        $keywords = [];
        if (!empty($request->keywords)) {
            $keywords = array_map('trim', explode(',', $request->keywords));
        }
        
        $validated['keywords'] = $keywords;
        $validated['priority'] = $validated['priority'] ?? 'medium';
        $validated['is_active'] = $validated['is_active'] ?? true;
        
        // Chuyển đổi priority từ text sang số nếu cần
        if (!is_numeric($validated['priority'])) {
            $priorityMap = ['low' => 1, 'medium' => 3, 'high' => 5];
            $validated['priority'] = $priorityMap[$validated['priority']] ?? 3;
        }
        
        AITrainingQuestion::create($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm câu hỏi thành công!'
            ]);
        }
        
        return redirect()->route('admin.ai-training.index')
            ->with('success', 'Thêm câu hỏi thành công!');
    }
    
    /**
     * Hiển thị form chỉnh sửa (phiên bản đầy đủ)
     */
    public function editFull($id)
    {
        $question = AITrainingQuestion::findOrFail($id);
        $categories = AITrainingQuestion::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');
        
        return view('admin.ai-training.edit', compact('question', 'categories'));
    }
    
    /**
     * Cập nhật câu hỏi
     */
    public function update(Request $request, $id)
    {
        if ($id == 0) {
            return $this->store($request);
        }
        
        $question = AITrainingQuestion::findOrFail($id);
        
        $request->validate([
            'question' => 'required|string|max:500|unique:ai_training_questions,question,' . $id,
            'answer' => 'required|string|max:2000',
            'category' => 'nullable|string|max:100',
            'keywords' => 'nullable|string|max:500',
            'priority' => 'nullable|integer|min:1|max:5',
            'is_active' => 'boolean'
        ]);
        
        // Xử lý keywords
        $keywords = [];
        if (!empty($request->keywords)) {
            $keywords = array_map('trim', explode(',', $request->keywords));
        }
        
        $updateData = [
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category ?? 'Khác',
            'keywords' => $keywords,
            'priority' => $request->priority ?? 3,
            'is_active' => $request->has('is_active') ? 1 : 0
        ];
        
        // Xử lý category từ form đầy đủ
        if ($request->category === 'other' && $request->filled('category_other')) {
            $updateData['category'] = $request->category_other;
        }
        
        // Chuyển đổi priority từ text sang số nếu cần
        if ($request->has('priority') && !is_numeric($request->priority)) {
            $priorityMap = ['low' => 1, 'medium' => 3, 'high' => 5];
            $updateData['priority'] = $priorityMap[$request->priority] ?? 3;
        }
        
        $question->update($updateData);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công!'
            ]);
        }
        
        return back()->with('success', 'Cập nhật câu hỏi thành công!');
    }
    
    /**
     * Xóa câu hỏi
     */
    public function destroy($id)
    {
        $question = AITrainingQuestion::findOrFail($id);
        $question->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa câu hỏi thành công!'
            ]);
        }
        
        return back()->with('success', 'Xóa câu hỏi thành công!');
    }
    
    /**
     * Upload file Excel - Version đơn giản (từ controller đầu)
     */
   public function uploadExcel(Request $request)
{
    $request->validate([
        'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120'
    ]);
    
    try {
        if (!$request->hasFile('excel_file')) {
            return back()->with('error', 'Vui lòng chọn file để upload.');
        }
        
        $file = $request->file('excel_file');
        
        $import = new AiTrainingImport();
        Excel::import($import, $file);
        
        // Lấy thống kê - sử dụng phương thức đúng
        $stats = $import->getStats();
        
        $message = "<strong>Kết quả import:</strong><br>";
        $message .= "- Thêm mới: " . ($stats['imported'] ?? 0) . " câu hỏi<br>";
        $message .= "- Cập nhật: " . ($stats['updated'] ?? 0) . " câu hỏi<br>";
        $message .= "- Bỏ qua: " . ($stats['skipped'] ?? 0) . " dòng<br>";
        
        // Hiển thị lỗi nếu có
        if (!empty($stats['error_list'] ?? [])) {
            $errorCount = count($stats['error_list']);
            $message .= "<br><strong>Lỗi ($errorCount):</strong><br>";
            
            // Hiển thị tối đa 5 lỗi đầu tiên
            $maxErrors = min(5, $errorCount);
            for ($i = 0; $i < $maxErrors; $i++) {
                $message .= ($i + 1) . ". " . $stats['error_list'][$i] . "<br>";
            }
            
            if ($errorCount > 5) {
                $message .= "... và " . ($errorCount - 5) . " lỗi khác<br>";
            }
            
            return back()->with('warning', $message);
        }
        
        return back()->with('success', $message);
        
    } catch (\Exception $e) {
        \Log::error('Import Excel Error: ' . $e->getMessage());
        
        $errorMsg = 'Lỗi import: ' . $e->getMessage();
        if (strpos($e->getMessage(), 'Undefined array key') !== false) {
            $errorMsg .= '<br>Lỗi định dạng file. Vui lòng tải file mẫu và sử dụng đúng cấu trúc.';
        }
        
        return back()->with('error', $errorMsg);
    }
}
    
    /**
     * Upload file Excel với options (từ controller thứ hai)
     */
    public function uploadExcelAdvanced(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'category' => 'nullable|string',
            'overwrite' => 'boolean'
        ]);
        
        try {
            $category = $request->input('category');
            $overwrite = $request->boolean('overwrite');
            
            if ($overwrite && $category) {
                // Xóa các câu hỏi cũ trong cùng category
                AITrainingQuestion::where('category', $category)->delete();
            }
            
            if ($request->has('file_path')) {
                // Version 2: Process with mapping
                $import = new AiTrainingImport(
                    $category,
                    $request->input('mapping', [])
                );
                
                Excel::import($import, $request->file_path);
                
                // Xóa file tạm
                Storage::delete($request->file_path);
                
                $importedCount = $import->getRowCount();
            } else {
                // Version 1: Simple import
                $file = $request->file('excel_file');
                $import = new AiTrainingImport($category);
                Excel::import($import, $file);
                $importedCount = $import->getRowCount();
            }
            
            return response()->json([
                'success' => true,
                'message' => "Import thành công {$importedCount} câu hỏi!",
                'count' => $importedCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi import: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Upload file Excel preview (Version 2)
     */
    public function uploadExcelPreview(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);
        
        try {
            $file = $request->file('excel_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('temp', $fileName);
            
            // Đọc file để preview
            $import = new AiTrainingImport();
            $collection = Excel::toCollection($import, $file);
            
            $previewData = $collection->first()->take(5);
            
            return response()->json([
                'success' => true,
                'file_path' => $filePath,
                'total_rows' => $collection->first()->count(),
                'preview' => $previewData,
                'columns' => $previewData->first()->keys()->toArray()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export dữ liệu ra Excel/CSV
     */
    public function export(Request $request)
    {
        $category = $request->get('category');
        $status = $request->get('status');
        $priority = $request->get('priority');
        $format = $request->get('format');
        
        $query = AITrainingQuestion::query();
        
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }
        
        if ($priority) {
            $query->where('priority', $priority);
        }
        
        if ($format === 'csv') {
            // CSV Export
            $questions = $query->get();
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="ai_training_questions_' . date('Y-m-d') . '.csv"',
            ];
            
            $callback = function() use ($questions) {
                $file = fopen('php://output', 'w');
                
                // Header
                fputcsv($file, [
                    'ID', 
                    'Câu hỏi', 
                    'Câu trả lời', 
                    'Danh mục', 
                    'Từ khóa', 
                    'Độ ưu tiên', 
                    'Trạng thái',
                    'Ngày tạo'
                ]);
                
                // Data
                foreach ($questions as $question) {
                    fputcsv($file, [
                        $question->id,
                        $question->question,
                        $question->answer,
                        $question->category,
                        $question->keywords ? implode(', ', (array)$question->keywords) : '',
                        $question->priority,
                        $question->is_active ? 'Hoạt động' : 'Ngừng',
                        $question->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        } else {
            // Excel Export
            $filename = 'ai_training_questions_' . date('Y-m-d_H-i') . '.xlsx';
            return Excel::download(new AITrainingExport($query), $filename);
        }
    }
    
    /**
     * Tải template Excel
     */
    public function downloadTemplate()
    {
        $templatePath = storage_path('app/templates/ai-training-template.xlsx');
        
        if (!file_exists($templatePath)) {
            // Tạo template nếu chưa có
            $this->generateTemplate();
        }
        
        return response()->download($templatePath, 'ai-training-template.xlsx');
    }
    
    /**
     * Tìm kiếm nâng cao
     */
    public function search(Request $request)
    {
        $keyword = $request->get('keyword');
        $category = $request->get('category');
        $status = $request->get('status');
        $search = $request->input('search', $keyword);
        $checkDuplicate = $request->boolean('check_duplicate');
        
        if ($checkDuplicate) {
            $existing = AITrainingQuestion::where('question', 'like', "%{$search}%")
                ->first();
                
            return response()->json([
                'duplicate' => $existing ? true : false,
                'existing' => $existing ? $existing->question : null
            ]);
        }
        
        $query = AITrainingQuestion::query();
        
        if ($keyword || $search) {
            $searchTerm = $keyword ?: $search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('question', 'like', "%$searchTerm%")
                  ->orWhere('answer', 'like', "%$searchTerm%")
                  ->orWhere('keywords', 'like', "%$searchTerm%")
                  ->orWhereJsonContains('keywords', $searchTerm);
            });
        }
        
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            $results = $query->where('is_active', true)
                ->select('id', 'question', 'category')
                ->limit($request->has('limit') ? $request->limit : 20)
                ->get();
            
            return response()->json([
                'success' => true,
                'results' => $results
            ]);
        }
        
        $questions = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Lấy thống kê
        $totalQuestions = AITrainingQuestion::count();
        $activeQuestions = AITrainingQuestion::where('is_active', true)->count();
        $highPriorityQuestions = AITrainingQuestion::where('priority', '>=', 4)->count();
        
        // Lấy danh sách categories
        $categories = AITrainingQuestion::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');
        
        return view('admin.ai-training.index', compact(
            'questions',
            'totalQuestions',
            'activeQuestions',
            'highPriorityQuestions',
            'categories'
        ))->with([
            'keyword' => $keyword,
            'selectedCategory' => $category,
            'selectedStatus' => $status
        ]);
    }
    
    /**
     * Thống kê dữ liệu
     */
    public function statistics()
    {
        if (request()->ajax() || request()->wantsJson()) {
            $stats = DB::table('ai_training_questions')
                ->select([
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active'),
                    DB::raw('SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive'),
                    DB::raw('COUNT(DISTINCT category) as categories'),
                    DB::raw('AVG(priority) as avg_priority')
                ])
                ->first();
            
            // Daily statistics
            $dailyStats = AITrainingQuestion::select([
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active'),
                    DB::raw('COUNT(DISTINCT category) as categories'),
                    DB::raw('DATE(created_at) as date')
                ])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get();
                
            // Category statistics
            $categoryStats = AITrainingQuestion::select('category', 
                    DB::raw('COUNT(*) as count'),
                    DB::raw('AVG(priority) as avg_priority')
                )
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->get();
            
            $recentQuestions = AITrainingQuestion::latest()
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'daily_stats' => $dailyStats,
                'category_stats' => $categoryStats,
                'recent_questions' => $recentQuestions
            ]);
        }
        
        // Phiên bản view cho trang thống kê
        $total = AITrainingQuestion::count();
        $active = AITrainingQuestion::where('is_active', true)->count();
        
        $categories = AITrainingQuestion::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();
            
        $priorityStats = AITrainingQuestion::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->orderBy('priority', 'desc')
            ->get();
            
        $recentQuestions = AITrainingQuestion::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('admin.ai-training.statistics', compact(
            'total', 'active', 'categories', 'priorityStats', 'recentQuestions'
        ));
    }
    
    /**
     * Bật/tắt trạng thái
     */
    public function toggleStatus($id)
    {
        $question = AITrainingQuestion::findOrFail($id);
        $question->update(['is_active' => !$question->is_active]);
        
        return response()->json([
            'success' => true,
            'is_active' => $question->is_active,
            'message' => $question->is_active ? 'Đã kích hoạt' : 'Đã ẩn'
        ]);
    }
    
    /**
     * Cập nhật độ ưu tiên
     */
    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required'
        ]);
        
        $question = AITrainingQuestion::findOrFail($id);
        
        // Chuyển đổi priority từ text sang số nếu cần
        $priority = $request->priority;
        if (!is_numeric($priority)) {
            $priorityMap = ['low' => 1, 'medium' => 3, 'high' => 5];
            $priority = $priorityMap[$priority] ?? 3;
        }
        
        $question->update(['priority' => $priority]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * API cho support page - Lấy câu hỏi
     */
    public function getQuestionsForSupport()
    {
        $questions = AITrainingQuestion::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'question', 'answer', 'category', 'keywords']);
        
        return response()->json([
            'success' => true,
            'questions' => $questions,
            'count' => $questions->count()
        ]);
    }
    
    /**
     * API cho support page - Tìm câu trả lời
     */
    public function findAnswer(Request $request)
    {
        $request->validate([
            'question' => 'required|string'
        ]);
        
        $userQuestion = strtolower(trim($request->question));
        
        // Tìm câu hỏi chính xác
        $exactMatch = AITrainingQuestion::where('is_active', true)
            ->whereRaw('LOWER(question) = ?', [$userQuestion])
            ->first();
        
        if ($exactMatch) {
            return response()->json([
                'success' => true,
                'answer' => $exactMatch->answer,
                'match_type' => 'exact'
            ]);
        }
        
        // Tìm bằng keywords
        $keywordMatches = AITrainingQuestion::where('is_active', true)
            ->whereNotNull('keywords')
            ->get()
            ->filter(function ($item) use ($userQuestion) {
                if (!$item->keywords) return false;
                
                return collect($item->keywords)->contains(function ($keyword) use ($userQuestion) {
                    return stripos($userQuestion, strtolower($keyword)) !== false;
                });
            })
            ->sortByDesc(function ($item) use ($userQuestion) {
                // Đếm số keywords match
                return collect($item->keywords)->filter(function ($keyword) use ($userQuestion) {
                    return stripos($userQuestion, strtolower($keyword)) !== false;
                })->count();
            });
        
        if ($keywordMatches->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'answer' => $keywordMatches->first()->answer,
                'match_type' => 'keyword',
                'match_score' => collect($keywordMatches->first()->keywords)
                    ->filter(function ($keyword) use ($userQuestion) {
                        return stripos($userQuestion, strtolower($keyword)) !== false;
                    })->count()
            ]);
        }
        
        // Tìm tương tự
        $similar = AITrainingQuestion::where('is_active', true)
            ->where('question', 'like', '%' . $request->question . '%')
            ->orWhere('answer', 'like', '%' . $request->question . '%')
            ->first();
        
        if ($similar) {
            return response()->json([
                'success' => true,
                'answer' => $similar->answer,
                'match_type' => 'similar'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'answer' => 'Xin lỗi, tôi chưa hiểu câu hỏi của bạn. Vui lòng liên hệ nhân viên hỗ trợ.',
            'match_type' => 'none'
        ]);
    }
    
    /**
     * Tạo template Excel
     */
    private function generateTemplate()
    {
        $headers = [
            'question' => 'Câu hỏi',
            'answer' => 'Câu trả lời',
            'category' => 'Danh mục',
            'keywords' => 'Từ khóa (phân cách bằng dấu phẩy)',
            'priority' => 'Độ ưu tiên (1-5)',
            'is_active' => 'Trạng thái (1: Hoạt động, 0: Ẩn)'
        ];
        
        $exampleData = [
            [
                'question' => 'Thời gian giao hàng trong bao lâu?',
                'answer' => 'Thời gian giao hàng từ 2-5 ngày tùy khu vực.',
                'category' => 'Vận chuyển',
                'keywords' => 'giao hàng, thời gian, bao lâu',
                'priority' => 5,
                'is_active' => 1
            ]
        ];
        
        Excel::store(new AITrainingExport(collect($exampleData)), 
            'templates/ai-training-template.xlsx');
    }
    
    /**
     * Xử lý import từ form cũ (compatibility)
     */
    public function processImport(Request $request)
    {
        return redirect()->route('admin.ai-training.index')
            ->with('info', 'Vui lòng sử dụng tính năng upload Excel trực tiếp');
    }
}