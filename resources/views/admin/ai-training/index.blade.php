@extends('layouts.admin')  

@section('title', 'Training AI - Quản lý Câu hỏi')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title text-white">
                <i class="fas fa-robot mr-2"></i>Training AI - Quản lý Câu hỏi Thông minh
            </h3>
        </div>
        
        <div class="card-body">
            <!-- Thông báo -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Thống kê nhanh -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-question-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tổng câu hỏi</span>
                            <span class="info-box-number">{{ $totalQuestions }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Câu hỏi Active</span>
                            <span class="info-box-number">{{ $activeQuestions }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-folder"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Số danh mục</span>
                            <span class="info-box-number">{{ $categories->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-star"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Độ ưu tiên cao</span>
                            <span class="info-box-number">{{ $highPriorityQuestions }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card Import Excel -->
            <div class="card card-primary">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-file-excel mr-2"></i>Nhập câu hỏi từ Excel
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ai-training.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        <div class="form-group">
                            <label for="excelFile">Chọn file Excel</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="excelFile" name="excel_file" accept=".xlsx,.xls" required>
                                    <label class="custom-file-label" for="excelFile">Chọn file Excel</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-upload mr-1"></i>Tải lên
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                File Excel phải có cấu trúc: Câu hỏi | Câu trả lời | Danh mục | Từ khóa (cách nhau bằng dấu phẩy) | Độ ưu tiên (1-5)
                            </small>
                        </div>
                    </form>
                    
                    <!-- Template file mẫu -->
                    <div class="mt-3">
                        <a href="{{ asset('templates/ai-training-template.xlsx') }}" class="btn btn-outline-primary btn-sm" download>
                            <i class="fas fa-download mr-1"></i>Tải file mẫu
                        </a>
                        <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#templateGuide">
                            <i class="fas fa-question-circle mr-1"></i>Hướng dẫn
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tìm kiếm và Bộ lọc -->
            <div class="card card-secondary mt-3">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-search mr-2"></i>Tìm kiếm & Lọc câu hỏi
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ai-training.search') }}" method="GET" id="searchForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="searchKeyword">Từ khóa</label>
                                    <input type="text" class="form-control" id="searchKeyword" name="keyword" 
                                           placeholder="Nhập từ khóa tìm kiếm..." value="{{ request('keyword') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="searchCategory">Danh mục</label>
                                    <select class="form-control" id="searchCategory" name="category">
                                        <option value="">Tất cả danh mục</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="searchStatus">Trạng thái</label>
                                    <select class="form-control" id="searchStatus" name="status">
                                        <option value="">Tất cả</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search mr-1"></i>Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Danh sách câu hỏi -->
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-list mr-2"></i>Danh sách câu hỏi ({{ $questions->total() }})
                    </h4>
                    <div class="card-tools">
                        <a href="{{ route('admin.ai-training.export') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-export mr-1"></i>Xuất Excel
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Câu hỏi</th>
                                    <th>Câu trả lời</th>
                                    <th>Danh mục</th>
                                    <th>Độ ưu tiên</th>
                                    <th width="100">Trạng thái</th>
                                    <th width="150">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questions as $question)
                                    <tr>
                                        <td>{{ $question->id }}</td>
                                        <td>
    <strong>{{ Str::limit($question->question, 50) }}</strong>
    @if($question->keywords && count($question->keywords) > 0)
        <br>
        <small class="text-muted">
            <i class="fas fa-tags"></i>
            {{ implode(', ', $question->keywords) }}
        </small>
    @endif
</td>
                                        <td>{{ Str::limit($question->answer, 70) }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $question->category }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $priority = $question->priority ?? 1;
                                                $stars = str_repeat('★', $priority) . str_repeat('☆', 5 - $priority);
                                            @endphp
                                            <span class="text-warning">{{ $stars }}</span>
                                            <span class="badge badge-secondary ml-2">{{ $priority }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $question->is_active ? 'badge-success' : 'badge-danger' }}">
                                                {{ $question->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-question" 
                                                    data-id="{{ $question->id }}"
                                                    data-toggle="modal" data-target="#editModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-question" 
                                                    data-id="{{ $question->id }}"
                                                    data-question="{{ Str::limit($question->question, 30) }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $question->id }}" 
                                                  action="{{ route('admin.ai-training.destroy', $question->id) }}" 
                                                  method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>Chưa có câu hỏi nào. Hãy thêm câu hỏi để training AI!</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                @if($questions->hasPages())
                    <div class="card-footer">
                        {{ $questions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Question -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Chỉnh sửa câu hỏi</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_question">Câu hỏi *</label>
                        <input type="text" class="form-control" id="edit_question" name="question" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_answer">Câu trả lời *</label>
                        <textarea class="form-control" id="edit_answer" name="answer" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_category">Danh mục</label>
                                <input type="text" class="form-control" id="edit_category" name="category" 
                                       list="categoryList">
                                <datalist id="categoryList">
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_priority">Độ ưu tiên (1-5)</label>
                                <select class="form-control" id="edit_priority" name="priority">
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}">{{ $i }} - {{ $i == 5 ? 'Rất cao' : ($i == 4 ? 'Cao' : ($i == 3 ? 'Trung bình' : ($i == 2 ? 'Thấp' : 'Rất thấp'))) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                 <div class="form-group">
    <label for="edit_keywords">Từ khóa (cách nhau bằng dấu phẩy)</label>
    <input type="text" class="form-control" id="edit_keywords" name="keywords" 
           placeholder="ví dụ: size, màu sắc, giá cả, chất liệu"
           value="{{ $question->keywords ? implode(', ', $question->keywords) : '' }}">
</div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="edit_is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hướng dẫn Template -->
<div class="modal fade" id="templateGuide" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">Hướng dẫn file Excel</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Cấu trúc file Excel mẫu:</h6>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Cột</th>
                            <th>Mô tả</th>
                            <th>Ví dụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>A - Câu hỏi</td>
                            <td>Nội dung câu hỏi</td>
                            <td>"Size áo này như thế nào?"</td>
                        </tr>
                        <tr>
                            <td>B - Câu trả lời</td>
                            <td>Nội dung trả lời</td>
                            <td>"Áo có các size từ S đến XL..."</td>
                        </tr>
                        <tr>
                            <td>C - Danh mục</td>
                            <td>Phân loại câu hỏi</td>
                            <td>"Kích thước", "Giá cả", "Chất liệu"</td>
                        </tr>
                        <tr>
                            <td>D - Từ khóa</td>
                            <td>Từ khóa liên quan</td>
                            <td>"size, kích thước, fit"</td>
                        </tr>
                        <tr>
                            <td>E - Độ ưu tiên</td>
                            <td>1-5 (5 là cao nhất)</td>
                            <td>3</td>
                        </tr>
                    </tbody>
                </table>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Lưu ý:</strong> File Excel phải có định dạng .xlsx hoặc .xls, không quá 2MB
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Hiển thị tên file khi chọn
    $('#excelFile').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
    
    // Xử lý edit question
   $('.edit-question').on('click', function() {
    var questionId = $(this).data('id');
    
    // Gọi API lấy thông tin câu hỏi
    $.ajax({
        url: '/admin/ai-training/' + questionId + '/edit',
        method: 'GET',
        success: function(response) {
            $('#edit_question').val(response.question);
            $('#edit_answer').val(response.answer);
            $('#edit_category').val(response.category);
            $('#edit_priority').val(response.priority);
            
            // FIX: Xử lý keywords đúng cách
            var keywords = '';
            if (response.keywords) {
                if (Array.isArray(response.keywords)) {
                    keywords = response.keywords.join(', ');
                } else if (typeof response.keywords === 'string') {
                    try {
                        var parsed = JSON.parse(response.keywords);
                        if (Array.isArray(parsed)) {
                            keywords = parsed.join(', ');
                        } else {
                            keywords = response.keywords;
                        }
                    } catch(e) {
                        keywords = response.keywords;
                    }
                }
            }
            $('#edit_keywords').val(keywords);
            
            $('#edit_is_active').prop('checked', response.is_active);
            
            // Cập nhật action form
            $('#editForm').attr('action', '/admin/ai-training/' + questionId);
        },
        error: function() {
            alert('Lỗi khi tải thông tin câu hỏi');
        }
    });
});
    
    // Xử lý xóa question
    $('.delete-question').on('click', function() {
        var questionId = $(this).data('id');
        var questionText = $(this).data('question');
        
        if (confirm('Bạn có chắc muốn xóa câu hỏi: "' + questionText + '"?')) {
            $('#delete-form-' + questionId).submit();
        }
    });
    
    // Thông báo khi upload thành công
    @if(session('upload_success'))
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: '{{ session("upload_success") }}',
            timer: 3000
        });
    @endif
});
</script>
@endsection