@extends('app') 
@section('content')

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gửi yêu cầu hỗ trợ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px; /* Để tránh bị steps indicator che */
        }
        
        .support-container {
            max-width: 900px;
            margin: 2rem auto;
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-bottom: none;
            padding: 1.5rem 2rem;
        }
        
        .card-header h4 {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .card-header small {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .section-title {
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e2e8f0;
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.3);
        }
        
        .btn-outline-secondary {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.25rem;
        }
        
        .alert-info {
            background-color: rgba(76, 201, 240, 0.1);
            color: #0c5460;
            border-left: 4px solid var(--success);
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        
        .file-preview {
            border-radius: 8px;
            padding: 0.75rem;
            background-color: #f8f9fa;
            border: 1px dashed #dee2e6;
        }
        
        .progress {
            height: 6px;
            border-radius: 3px;
            margin-top: 0.5rem;
        }
        
        .char-counter {
            font-size: 0.85rem;
            text-align: right;
            margin-top: 0.25rem;
        }
        
        .char-counter.warning {
            color: #ffc107;
            font-weight: 600;
        }
        
        .char-counter.danger {
            color: #dc3545;
            font-weight: 700;
        }
        
        /* Steps Indicator Fixed */
        .steps-indicator-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 1rem 0;
            transition: var(--transition);
        }
        
        .steps-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .steps-indicator {
            display: flex;
            justify-content: space-between;
            position: relative;
        }
        
        .steps-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e9ecef;
            z-index: 1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            cursor: pointer;
        }
        
        .step-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #6c757d;
            transition: var(--transition);
        }
        
        .step.active .step-icon {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.2);
        }
        
        .step.completed .step-icon {
            background-color: var(--primary);
            color: white;
        }
        
        .step-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .step.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }
        
        .step.completed .step-label {
            color: var(--primary);
        }
        
        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .floating-label .form-control {
            padding-top: 1.5rem;
            padding-bottom: 0.75rem;
        }
        
        .floating-label label {
            position: absolute;
            top: 0.75rem;
            left: 1rem;
            color: #6c757d;
            transition: var(--transition);
            pointer-events: none;
            font-size: 0.9rem;
        }
        
        .floating-label .form-control:focus ~ label,
        .floating-label .form-control:not(:placeholder-shown) ~ label {
            top: 0.4rem;
            left: 1rem;
            font-size: 0.75rem;
            color: var(--primary);
            font-weight: 600;
        }
        
        .form-section {
            margin-bottom: 3rem;
            padding: 2rem;
            border-radius: var(--border-radius);
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: var(--transition);
            display: none;
        }
        
        .form-section.active {
            border-left: 4px solid var(--primary);
            display: block;
        }
        
        .section-progress {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .section-progress-bar {
            height: 100%;
            background: var(--primary);
            width: 0%;
            transition: width 0.5s ease;
        }
        
        .priority-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .priority-badge {
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            flex: 1;
            min-width: 120px;
            text-align: center;
        }
        
        .priority-badge:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }
        
        .priority-badge.selected {
            border-color: var(--primary);
            background-color: rgba(67, 97, 238, 0.1);
        }
        
        .priority-badge.low .priority-indicator {
            background-color: #28a745;
        }
        
        .priority-badge.medium .priority-indicator {
            background-color: #ffc107;
        }
        
        .priority-badge.high .priority-indicator {
            background-color: #fd7e14;
        }
        
        .priority-badge.urgent .priority-indicator {
            background-color: #dc3545;
        }
        
        .priority-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }
            
            .steps-indicator {
                margin-bottom: 1.5rem;
            }
            
            .step-label {
                font-size: 0.75rem;
            }
            
            .form-section {
                padding: 1.5rem;
            }
            
            .priority-badges {
                flex-direction: column;
            }
            
            .priority-badge {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Steps Indicator Fixed -->
    <div class="steps-indicator-fixed">
        <div class="steps-container">
            <div class="steps-indicator">
                <div class="step active" data-step="1">
                    <div class="step-icon">1</div>
                    <div class="step-label">Thông tin</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-icon">2</div>
                    <div class="step-label">Phân loại</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-icon">3</div>
                    <div class="step-label">Nội dung</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-icon">4</div>
                    <div class="step-label">Xác nhận</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container py-4 support-container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">📨 Gửi yêu cầu hỗ trợ</h4>
                        <small class="opacity-75">Chúng tôi sẽ phản hồi trong thời gian sớm nhất</small>
                    </div>

                    <div class="card-body">
                        {{-- Hiển thị thông báo lỗi --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Vui lòng kiểm tra lại thông tin:</h6>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Hiển thị thông báo gửi thành công --}}
                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('support.submit') }}" method="POST" enctype="multipart/form-data" id="supportForm">
                            @csrf

                            <!-- Section 1: Thông tin khách hàng -->
                            <div class="form-section active" id="section-1">
                                <div class="section-progress">
                                    <div class="section-progress-bar" id="section-1-progress"></div>
                                </div>
                                <h6 class="section-title"><i class="fas fa-user-circle"></i> Thông tin liên hệ</h6>
                                @guest
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="floating-label">
                                                <input type="text" name="name" id="name" class="form-control" 
                                                       placeholder=" " value="{{ old('name') }}" required>
                                                <label for="name">Họ tên *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="floating-label">
                                                <input type="email" name="email" id="email" class="form-control" 
                                                       placeholder=" " value="{{ old('email') }}" required>
                                                <label for="email">Email *</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="floating-label">
                                                <input type="text" name="phone" id="phone" class="form-control" 
                                                       placeholder=" " value="{{ old('phone') }}">
                                                <label for="phone">Số điện thoại</label>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="floating-label">
                                                <input type="text" value="{{ Auth::user()->name }}" class="form-control" disabled>
                                                <label>Họ tên</label>
                                            </div>
                                            <small class="text-muted"><i class="fas fa-check-circle text-success me-1"></i>Tài khoản đã đăng nhập</small>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="floating-label">
                                                <input type="email" value="{{ Auth::user()->email }}" class="form-control" disabled>
                                                <label>Email</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            @if (!Auth::user()->phone)
                                                <div class="alert alert-warning d-flex justify-content-between align-items-center mb-0">
                                                    <span class="small"><i class="fas fa-exclamation-triangle me-1"></i>Bạn chưa cập nhật số điện thoại</span>
                                                    <a href="{{ route('hoso.index') }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Cập nhật
                                                    </a>
                                                </div>
                                            @else
                                                <div class="floating-label">
                                                    <input type="text" value="{{ Auth::user()->phone }}" class="form-control" disabled>
                                                    <label>Số điện thoại</label>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endguest
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-primary next-section" data-next="2">
                                        Tiếp theo <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Section 2: Loại yêu cầu -->
                            <div class="form-section" id="section-2">
                                <div class="section-progress">
                                    <div class="section-progress-bar" id="section-2-progress"></div>
                                </div>
                                <h6 class="section-title"><i class="fas fa-tags"></i> Phân loại yêu cầu</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="floating-label">
                                            <select name="type" id="type" class="form-select" required>
                                                <option value=""> </option>
                                                <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>Hỗ trợ chung</option>
                                                <option value="order" {{ old('type') == 'order' ? 'selected' : '' }}>Vấn đề đơn hàng</option>
                                                <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Thông tin sản phẩm</option>
                                                <option value="shipping" {{ old('type') == 'shipping' ? 'selected' : '' }}>Vận chuyển & Giao hàng</option>
                                                <option value="payment" {{ old('type') == 'payment' ? 'selected' : '' }}>Thanh toán & Hoàn tiền</option>
                                                <option value="technical" {{ old('type') == 'technical' ? 'selected' : '' }}>Vấn đề kỹ thuật</option>
                                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Khác</option>
                                            </select>
                                            <label for="type">Loại hỗ trợ *</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Thêm trường Priority -->
                                <h6 class="section-title mt-4"><i class="fas fa-exclamation-circle"></i> Độ ưu tiên</h6>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="priority-badges">
                                            <div class="priority-badge low" data-priority="low">
                                                <span class="priority-indicator"></span>
                                                <span>Thấp</span>
                                                <small class="d-block text-muted">Phản hồi trong 48h</small>
                                            </div>
                                            <div class="priority-badge medium selected" data-priority="medium">
                                                <span class="priority-indicator"></span>
                                                <span>Trung bình</span>
                                                <small class="d-block text-muted">Phản hồi trong 24h</small>
                                            </div>
                                            <div class="priority-badge high" data-priority="high">
                                                <span class="priority-indicator"></span>
                                                <span>Cao</span>
                                                <small class="d-block text-muted">Phản hồi trong 12h</small>
                                            </div>
                                            <div class="priority-badge urgent" data-priority="urgent">
                                                <span class="priority-indicator"></span>
                                                <span>Khẩn cấp</span>
                                                <small class="d-block text-muted">Phản hồi trong 4h</small>
                                            </div>
                                        </div>
                                        <input type="hidden" name="priority" id="priority" value="medium" required>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-section" data-prev="1">
                                        <i class="fas fa-arrow-left me-2"></i> Quay lại
                                    </button>
                                    <button type="button" class="btn btn-primary next-section" data-next="3">
                                        Tiếp theo <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Section 3: Nội dung yêu cầu -->
                            <div class="form-section" id="section-3">
                                <div class="section-progress">
                                    <div class="section-progress-bar" id="section-3-progress"></div>
                                </div>
                                <h6 class="section-title"><i class="fas fa-comment-dots"></i> Nội dung yêu cầu</h6>
                                <div class="mb-3">
                                    <div class="floating-label">
                                        <textarea name="message" id="message" class="form-control" 
                                                  placeholder=" " rows="6" required>{{ old('message') }}</textarea>
                                        <label for="message">Mô tả chi tiết *</label>
                                    </div>
                                    <div class="char-counter" id="charCounter">
                                        <span id="charCount">0</span>/1000 ký tự
                                    </div>
                                    <div class="progress">
                                        <div id="progressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>
                                
                                <!-- File đính kèm -->
                                <h6 class="section-title mt-4"><i class="fas fa-paperclip"></i> File đính kèm (Tùy chọn)</h6>
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="file" name="attachment" id="attachment" class="form-control" 
                                               accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                                        <button class="btn btn-outline-secondary" type="button" id="clearFileBtn" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle me-1"></i>Hỗ trợ: JPG, PNG, GIF, PDF, DOC (Tối đa 2MB)
                                    </div>
                                    <div id="filePreview" class="mt-3"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-section" data-prev="2">
                                        <i class="fas fa-arrow-left me-2"></i> Quay lại
                                    </button>
                                    <button type="button" class="btn btn-primary next-section" data-next="4">
                                        Tiếp theo <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Section 4: Xác nhận -->
                            <div class="form-section" id="section-4">
                                <div class="section-progress">
                                    <div class="section-progress-bar" id="section-4-progress"></div>
                                </div>
                                <h6 class="section-title"><i class="fas fa-check-circle"></i> Xác nhận thông tin</h6>
                                
                                <div class="alert alert-info">
                                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Vui lòng kiểm tra lại thông tin</h6>
                                    <p class="mb-0">Hãy đảm bảo tất cả thông tin bạn cung cấp là chính xác trước khi gửi yêu cầu hỗ trợ.</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">Thông tin liên hệ</h6>
                                        <div id="review-name" class="mb-2"></div>
                                        <div id="review-email" class="mb-2"></div>
                                        <div id="review-phone" class="mb-2"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">Thông tin yêu cầu</h6>
                                        <div id="review-type" class="mb-2"></div>
                                        <div id="review-priority" class="mb-2"></div>
                                        <div id="review-message" class="mb-2 text-truncate"></div>
                                        <div id="review-attachment" class="mb-2"></div>
                                    </div>
                                </div>
                                
                                {{-- Thông tin bổ sung --}}
                                @guest
                                <div class="alert alert-info mt-4">
                                    <h6 class="alert-heading"><i class="fas fa-lightbulb me-2"></i>Lưu ý quan trọng</h6>
                                    <p class="mb-2">Để theo dõi phản hồi, vui lòng lưu lại mã yêu cầu hoặc đăng ký tài khoản.</p>
                                    <small><i class="fas fa-envelope me-1"></i>Bạn cũng có thể tra cứu yêu cầu qua email đã đăng ký.</small>
                                </div>
                                @else
                                <div class="alert alert-info mt-4">
                                    <h6 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Đã đăng nhập</h6>
                                    <p class="mb-0">Bạn có thể theo dõi phản hồi trong mục <a href="{{ route('support.index') }}" class="alert-link fw-bold">Hỗ trợ của tôi</a>.</p>
                                </div>
                                @endguest
                                
                                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-outline-secondary prev-section" data-prev="3">
                                        <i class="fas fa-arrow-left me-2"></i> Quay lại
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg px-4" id="submitBtn">
                                        <i class="fas fa-paper-plane me-2"></i> Gửi yêu cầu hỗ trợ
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Hướng dẫn --}}
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="section-title"><i class="fas fa-info-circle"></i> Hướng dẫn gửi yêu cầu hiệu quả</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><small>Mô tả chi tiết vấn đề bạn gặp phải</small></li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><small>Cung cấp mã đơn hàng nếu có liên quan</small></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><small>Đính kèm hình ảnh minh họa nếu cần thiết</small></li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><small>Chọn đúng loại hỗ trợ để được xử lý nhanh nhất</small></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageTextarea = document.getElementById('message');
            const charCount = document.getElementById('charCount');
            const charCounter = document.getElementById('charCounter');
            const progressBar = document.getElementById('progressBar');
            const attachmentInput = document.getElementById('attachment');
            const filePreview = document.getElementById('filePreview');
            const clearFileBtn = document.getElementById('clearFileBtn');
            const submitBtn = document.getElementById('submitBtn');
            const supportForm = document.getElementById('supportForm');
            const steps = document.querySelectorAll('.step');
            const sections = document.querySelectorAll('.form-section');
            const nextButtons = document.querySelectorAll('.next-section');
            const prevButtons = document.querySelectorAll('.prev-section');
            const priorityBadges = document.querySelectorAll('.priority-badge');
            const priorityInput = document.getElementById('priority');
            
            let currentSection = 1;
            
            // Khởi tạo hiển thị section đầu tiên
            showSection(currentSection);
            updateStepsIndicator(currentSection);
            
            // Xử lý sự kiện scroll để cập nhật steps indicator
            window.addEventListener('scroll', function() {
                updateStepsIndicatorOnScroll();
            });
            
            // Xử lý click trên steps indicator
            steps.forEach(step => {
                step.addEventListener('click', function() {
                    const stepNumber = parseInt(this.getAttribute('data-step'));
                    if (stepNumber <= currentSection) {
                        showSection(stepNumber);
                        updateStepsIndicator(stepNumber);
                    }
                });
            });
            
            // Xử lý nút tiếp theo
            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const nextSection = parseInt(this.getAttribute('data-next'));
                    if (validateSection(currentSection)) {
                        showSection(nextSection);
                        updateStepsIndicator(nextSection);
                        currentSection = nextSection;
                    }
                });
            });
            
            // Xử lý nút quay lại
            prevButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const prevSection = parseInt(this.getAttribute('data-prev'));
                    showSection(prevSection);
                    updateStepsIndicator(prevSection);
                    currentSection = prevSection;
                });
            });
            
            // Xử lý chọn độ ưu tiên
            priorityBadges.forEach(badge => {
                badge.addEventListener('click', function() {
                    // Xóa selected class từ tất cả badges
                    priorityBadges.forEach(b => b.classList.remove('selected'));
                    // Thêm selected class cho badge được chọn
                    this.classList.add('selected');
                    // Cập nhật giá trị input
                    const priority = this.getAttribute('data-priority');
                    priorityInput.value = priority;
                });
            });
            
            // Hiển thị section cụ thể
            function showSection(sectionNumber) {
                sections.forEach(section => {
                    section.classList.remove('active');
                });
                
                const targetSection = document.getElementById(`section-${sectionNumber}`);
                if (targetSection) {
                    targetSection.classList.add('active');
                    
                    // Cuộn đến section với offset cho steps indicator cố định
                    const offsetTop = targetSection.offsetTop - 100;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                    
                    // Cập nhật thanh tiến trình của section
                    updateSectionProgress(sectionNumber);
                    
                    // Nếu là section xác nhận, cập nhật thông tin xem trước
                    if (sectionNumber === 4) {
                        updateReviewSection();
                    }
                }
            }
            
            // Cập nhật steps indicator
            function updateStepsIndicator(activeStep) {
                steps.forEach(step => {
                    const stepNumber = parseInt(step.getAttribute('data-step'));
                    step.classList.remove('active', 'completed');
                    
                    if (stepNumber === activeStep) {
                        step.classList.add('active');
                    } else if (stepNumber < activeStep) {
                        step.classList.add('completed');
                    }
                });
            }
            
            // Cập nhật steps indicator dựa trên scroll
            function updateStepsIndicatorOnScroll() {
                let currentSection = 1;
                
                sections.forEach((section, index) => {
                    const sectionTop = section.offsetTop - 150;
                    if (window.scrollY >= sectionTop) {
                        currentSection = index + 1;
                    }
                });
                
                updateStepsIndicator(currentSection);
            }
            
            // Cập nhật thanh tiến trình của section
            function updateSectionProgress(sectionNumber) {
                const progressBar = document.getElementById(`section-${sectionNumber}-progress`);
                if (progressBar) {
                    // Mô phỏng thanh tiến trình (trong thực tế, bạn có thể cập nhật dựa trên % hoàn thành)
                    progressBar.style.width = '100%';
                }
            }
            
            // Cập nhật section xem trước
            function updateReviewSection() {
                // Thông tin liên hệ
                document.getElementById('review-name').textContent = `Họ tên: ${document.getElementById('name')?.value || 'N/A'}`;
                document.getElementById('review-email').textContent = `Email: ${document.getElementById('email')?.value || 'N/A'}`;
                document.getElementById('review-phone').textContent = `Số điện thoại: ${document.getElementById('phone')?.value || 'N/A'}`;
                
                // Loại hỗ trợ
                const typeSelect = document.getElementById('type');
                const typeText = typeSelect.options[typeSelect.selectedIndex]?.text || 'Chưa chọn';
                document.getElementById('review-type').textContent = `Loại hỗ trợ: ${typeText}`;
                
                // Độ ưu tiên
                const priorityText = getPriorityText(priorityInput.value);
                document.getElementById('review-priority').textContent = `Độ ưu tiên: ${priorityText}`;
                
                // Nội dung (rút gọn)
                const message = document.getElementById('message').value;
                const shortMessage = message.length > 50 ? message.substring(0, 50) + '...' : message;
                document.getElementById('review-message').textContent = `Nội dung: ${shortMessage}`;
                
                // File đính kèm
                const file = attachmentInput.files[0];
                document.getElementById('review-attachment').textContent = `File đính kèm: ${file ? file.name : 'Không có'}`;
            }
            
            // Lấy text hiển thị cho độ ưu tiên
            function getPriorityText(priority) {
                const priorityMap = {
                    'low': 'Thấp',
                    'medium': 'Trung bình', 
                    'high': 'Cao',
                    'urgent': 'Khẩn cấp'
                };
                return priorityMap[priority] || 'Chưa chọn';
            }
            
            // Validate section
            function validateSection(sectionNumber) {
                let isValid = true;
                
                if (sectionNumber === 1) {
                    const name = document.getElementById('name');
                    const email = document.getElementById('email');
                    
                    if (name && !name.value.trim()) {
                        showAlert('Vui lòng nhập họ tên', 'danger');
                        name.focus();
                        isValid = false;
                    } else if (email && !email.value.trim()) {
                        showAlert('Vui lòng nhập email', 'danger');
                        email.focus();
                        isValid = false;
                    } else if (email && !isValidEmail(email.value)) {
                        showAlert('Vui lòng nhập email hợp lệ', 'danger');
                        email.focus();
                        isValid = false;
                    }
                } else if (sectionNumber === 2) {
                    const type = document.getElementById('type');
                    if (type && !type.value) {
                        showAlert('Vui lòng chọn loại hỗ trợ', 'danger');
                        type.focus();
                        isValid = false;
                    }
                    
                    // Kiểm tra priority đã được chọn chưa
                    if (!priorityInput.value) {
                        showAlert('Vui lòng chọn độ ưu tiên', 'danger');
                        isValid = false;
                    }
                } else if (sectionNumber === 3) {
                    const message = document.getElementById('message');
                    if (message && !message.value.trim()) {
                        showAlert('Vui lòng nhập nội dung yêu cầu', 'danger');
                        message.focus();
                        isValid = false;
                    } else if (message && message.value.length > 1000) {
                        showAlert('Nội dung không được vượt quá 1000 ký tự', 'danger');
                        message.focus();
                        isValid = false;
                    }
                }
                
                return isValid;
            }
            
            // Kiểm tra email hợp lệ
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            // Đếm ký tự và cập nhật thanh tiến trình
            messageTextarea.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = length;
                
                // Tính phần trăm
                const percentage = Math.min((length / 1000) * 100, 100);
                progressBar.style.width = `${percentage}%`;
                
                // Thay đổi màu sắc dựa trên độ dài
                if (length > 800) {
                    charCounter.classList.add('danger');
                    charCounter.classList.remove('warning');
                    progressBar.classList.remove('bg-warning');
                    progressBar.classList.add('bg-danger');
                } else if (length > 600) {
                    charCounter.classList.add('warning');
                    charCounter.classList.remove('danger');
                    progressBar.classList.remove('bg-danger');
                    progressBar.classList.add('bg-warning');
                } else {
                    charCounter.classList.remove('warning', 'danger');
                    progressBar.classList.remove('bg-warning', 'bg-danger');
                    progressBar.classList.add('bg-success');
                }
            });

            // Xử lý file đính kèm
            attachmentInput.addEventListener('change', function(e) {
                filePreview.innerHTML = '';
                clearFileBtn.style.display = 'block';
                
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                    
                    if (fileSize > 2) {
                        alert('File không được vượt quá 2MB');
                        this.value = '';
                        clearFileBtn.style.display = 'none';
                        return;
                    }
                    
                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'file-preview';
                    fileInfo.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas ${getFileIcon(file.type)} me-2"></i> 
                                <strong>${file.name}</strong>
                                <small class="text-muted">(${fileSize} MB)</small>
                            </div>
                            <button type="button" class="btn-close" onclick="clearFileInput()"></button>
                        </div>
                    `;
                    filePreview.appendChild(fileInfo);
                }
            });

            // Xóa file đã chọn
            clearFileBtn.addEventListener('click', function() {
                attachmentInput.value = '';
                filePreview.innerHTML = '';
                clearFileBtn.style.display = 'none';
            });

            // Xác nhận gửi form
            supportForm.addEventListener('submit', function(e) {
                if (!validateSection(3)) {
                    e.preventDefault();
                    showSection(3);
                    updateStepsIndicator(3);
                    currentSection = 3;
                    return;
                }
                
                // Hiển thị loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang gửi...';
                
                // Cập nhật bước cuối cùng
                steps[3].classList.add('active');
            });

            // Hàm hiển thị cảnh báo
            function showAlert(message, type) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
                alertDiv.innerHTML = `
                    <i class="fas ${type === 'danger' ? 'fa-exclamation-triangle' : 'fa-info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                const currentSection = document.getElementById(`section-${currentSection}`);
                currentSection.prepend(alertDiv);
                
                // Tự động ẩn sau 5 giây
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }

            // Hàm lấy icon cho loại file
            function getFileIcon(fileType) {
                if (fileType.startsWith('image/')) return 'fa-file-image';
                if (fileType === 'application/pdf') return 'fa-file-pdf';
                if (fileType.includes('document') || fileType.includes('msword') || fileType.includes('wordprocessing')) 
                    return 'fa-file-word';
                return 'fa-file';
            }

            // Hàm xóa file input (được gọi từ HTML)
            window.clearFileInput = function() {
                attachmentInput.value = '';
                filePreview.innerHTML = '';
                clearFileBtn.style.display = 'none';
            };

            // Khởi tạo char count
            charCount.textContent = messageTextarea.value.length;
            const initialLength = messageTextarea.value.length;
            const initialPercentage = Math.min((initialLength / 1000) * 100, 100);
            progressBar.style.width = `${initialPercentage}%`;
            
            if (initialLength > 800) {
                charCounter.classList.add('danger');
                progressBar.classList.add('bg-danger');
            } else if (initialLength > 600) {
                charCounter.classList.add('warning');
                progressBar.classList.add('bg-warning');
            }
        });
    </script>
</body>
</html>
@endsection