@extends('app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📨 Gửi yêu cầu hỗ trợ</h4>
                    <small class="opacity-75">Chúng tôi sẽ phản hồi trong thời gian sớm nhất</small>
                </div>

                <div class="card-body">
                    {{-- Hiển thị thông báo lỗi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">Vui lòng kiểm tra lại thông tin:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Hiển thị thông báo gửi thành công --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('support.submit') }}" method="POST" enctype="multipart/form-data" id="supportForm">
                        @csrf

                        {{-- Thông tin khách hàng --}}
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">👤 Thông tin liên hệ</h6>
                            @guest
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Họ tên *</label>
                                        <input type="text" name="name" id="name" class="form-control" 
                                               placeholder="Nhập họ tên của bạn" value="{{ old('name') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" name="email" id="email" class="form-control" 
                                               placeholder="email@example.com" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <input type="text" name="phone" id="phone" class="form-control" 
                                               placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Họ tên</label>
                                        <input type="text" value="{{ Auth::user()->name }}" class="form-control" disabled>
                                        <small class="text-muted">Tài khoản đã đăng nhập</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" value="{{ Auth::user()->email }}" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Số điện thoại</label>
                                        @if (!Auth::user()->phone)
                                            <div class="alert alert-warning d-flex justify-content-between align-items-center mb-0">
                                                <span class="small">Bạn chưa cập nhật số điện thoại</span>
                                                <a href="{{ route('hoso.index') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Cập nhật
                                                </a>
                                            </div>
                                        @else
                                            <input type="text" value="{{ Auth::user()->phone }}" class="form-control" disabled>
                                        @endif
                                    </div>
                                </div>
                            @endguest
                        </div>

                        {{-- Loại yêu cầu --}}
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">📋 Phân loại yêu cầu</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Loại hỗ trợ *</label>
                                    <select name="type" id="type" class="form-select" required>
                                        <option value="">-- Chọn loại hỗ trợ --</option>
                                        <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>Hỗ trợ chung</option>
                                        <option value="order" {{ old('type') == 'order' ? 'selected' : '' }}>Vấn đề đơn hàng</option>
                                        <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Thông tin sản phẩm</option>
                                        <option value="shipping" {{ old('type') == 'shipping' ? 'selected' : '' }}>Vận chuyển & Giao hàng</option>
                                        <option value="payment" {{ old('type') == 'payment' ? 'selected' : '' }}>Thanh toán & Hoàn tiền</option>
                                        <option value="technical" {{ old('type') == 'technical' ? 'selected' : '' }}>Vấn đề kỹ thuật</option>
                                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="priority" class="form-label">Mức độ ưu tiên</label>
                                    <select name="priority" id="priority" class="form-select">
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>🟡 Trung bình</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🔴 Cao (Cần giải quyết gấp)</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>🟢 Thấp (Không gấp)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Nội dung yêu cầu --}}
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">💬 Nội dung yêu cầu</h6>
                            <div class="mb-3">
                                <label for="message" class="form-label">Mô tả chi tiết *</label>
                                <textarea name="message" id="message" class="form-control" 
                                          placeholder="Vui lòng mô tả chi tiết vấn đề bạn gặp phải..." 
                                          rows="6" required>{{ old('message') }}</textarea>
                                <div class="form-text">
                                    <span id="charCount">0</span>/1000 ký tự
                                </div>
                            </div>
                        </div>

                        {{-- File đính kèm --}}
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">📎 File đính kèm (Tùy chọn)</h6>
                            <div class="mb-3">
                                <label for="attachment" class="form-label">Tải lên hình ảnh hoặc tài liệu</label>
                                <input type="file" name="attachment" id="attachment" class="form-control" 
                                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                                <div class="form-text">
                                    Hỗ trợ: JPG, PNG, GIF, PDF, DOC (Tối đa 2MB)
                                </div>
                                <div id="filePreview" class="mt-2"></div>
                            </div>
                        </div>

                        {{-- Thông tin bổ sung --}}
                        @guest
                        <div class="alert alert-info">
                            <h6 class="alert-heading">💡 Lưu ý quan trọng</h6>
                            <p class="mb-2">Để theo dõi phản hồi, vui lòng lưu lại mã yêu cầu hoặc đăng ký tài khoản.</p>
                            <small>Bạn cũng có thể tra cứu yêu cầu qua email đã đăng ký.</small>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <h6 class="alert-heading">✅ Đã đăng nhập</h6>
                            <p class="mb-0">Bạn có thể theo dõi phản hồi trong mục <a href="{{ route('support.index') }}" class="alert-link">Hỗ trợ của tôi</a>.</p>
                        </div>
                        @endguest

                        {{-- Nút gửi --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('support.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Gửi yêu cầu hỗ trợ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Hướng dẫn --}}
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="text-primary">ℹ️ Hướng dẫn gửi yêu cầu hiệu quả</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small>✅ Mô tả chi tiết vấn đề bạn gặp phải</small></li>
                        <li><small>✅ Cung cấp mã đơn hàng nếu có liên quan</small></li>
                        <li><small>✅ Đính kèm hình ảnh minh họa nếu cần thiết</small></li>
                        <li><small>✅ Chọn đúng loại hỗ trợ để được xử lý nhanh nhất</small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
}
.card-header {
    border-radius: 15px 15px 0 0 !important;
}
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    border-radius: 10px;
    padding: 10px 25px;
}
.btn-success:hover {
    background: linear-gradient(135deg, #218838, #1e9e8a);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}
.alert {
    border-radius: 10px;
    border: none;
}
</style>
<!-- Thêm highlight.js trước khi gọi hljs -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const attachmentInput = document.getElementById('attachment');
    const filePreview = document.getElementById('filePreview');
    const submitBtn = document.getElementById('submitBtn');
    const supportForm = document.getElementById('supportForm');

    // Đếm ký tự
    messageTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 1000) {
            charCount.classList.add('text-danger');
        } else {
            charCount.classList.remove('text-danger');
        }
    });

    // Xem trước file
    attachmentInput.addEventListener('change', function(e) {
        filePreview.innerHTML = '';
        
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
            
            if (fileSize > 2) {
                alert('File không được vượt quá 2MB');
                this.value = '';
                return;
            }
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'alert alert-info py-2';
            fileInfo.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-file"></i> 
                        <strong>${file.name}</strong>
                        <small class="text-muted">(${fileSize} MB)</small>
                    </div>
                    <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;
            filePreview.appendChild(fileInfo);
        }
    });

    // Xác nhận gửi form
    supportForm.addEventListener('submit', function(e) {
        const message = messageTextarea.value.trim();
        
        if (message.length === 0) {
            e.preventDefault();
            alert('Vui lòng nhập nội dung yêu cầu hỗ trợ');
            messageTextarea.focus();
            return;
        }
        
        if (message.length > 1000) {
            e.preventDefault();
            alert('Nội dung không được vượt quá 1000 ký tự');
            messageTextarea.focus();
            return;
        }
        
        // Hiển thị loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    });

    // Khởi tạo char count
    charCount.textContent = messageTextarea.value.length;
});
</script>
@endsection
