@extends('app')

@section('content')
<style>
    /* ========== MAIN STYLES ========== */
    .support-container {
        display: flex;
        height: calc(100vh - 100px);
        max-height: 800px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        background: #fff;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    /* ========== SIDEBAR ========== */
    .conversation-sidebar {
        width: 360px;
        background: #f8f9fa;
        color: #333;
        padding: 0;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #e9ecef;
    }

    .user-profile-card {
        padding: 20px;
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .user-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-right: 15px;
    }

    .conversation-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px 0;
    }

    .conversation-item {
        display: flex;
        align-items: center;
        padding: 14px 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f3f4;
        position: relative;
        background: #fff;
        margin: 0 10px;
        border-radius: 12px;
        margin-bottom: 8px;
    }

    .conversation-item:hover {
        background: #f5f7f9;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .conversation-item.active {
        background: linear-gradient(135deg, #a7e0e9 0%, #7cc6d6 100%);
        color: #1a1a1a;
        box-shadow: 0 4px 15px rgba(124, 198, 214, 0.2);
    }

    .conversation-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.3rem;
        color: white;
    }

    .support-chat .conversation-icon {
        background: linear-gradient(135deg, #a7e0e9 0%, #7cc6d6 100%);
    }

    .ai-chat .conversation-icon {
        background: linear-gradient(135deg, #b19cd9 0%, #8a63d2 100%);
    }

    .unread-badge {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        background: #ff6b6b;
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 12px;
        min-width: 22px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(255, 107, 107, 0.2);
    }

    /* ========== CHAT AREA ========== */
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .chat-header {
        padding: 18px 24px;
        background: white;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .chat-icon-small {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
    }

    .chat-icon {
        background: linear-gradient(135deg, #a7e0e9 0%, #7cc6d6 100%);
    }

    .ai-icon {
        background: linear-gradient(135deg, #b19cd9 0%, #8a63d2 100%);
    }

    .messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f0f8ff;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.828-1.415 1.415L51.8 0l.828.83-1.414 1.414L49.97 0l.83.828-1.415 1.415L48.143 0l.828.83-1.414 1.414L46.313 0l.83.828-1.415 1.415L44.486 0l.828.83-1.414 1.414L42.656 0l.83.828-1.415 1.415L40.83 0l.827.83-1.414 1.414L39 0l.83.828-1.415 1.415L37.173 0l.828.83-1.414 1.414L35.343 0l.83.828-1.415 1.415L33.516 0l.828.83-1.414 1.414L31.686 0l.83.828-1.415 1.415L29.86 0l.827.83-1.414 1.414L28.03 0l.83.828-1.415 1.415L26.203 0l.828.83-1.414 1.414L24.373 0l.83.828-1.415 1.415L22.546 0l.828.83-1.414 1.414L20.716 0l.83.828-1.415 1.415L18.89 0l.827.83-1.414 1.414L17.06 0l.83.828-1.415 1.415L15.233 0l.828.83-1.414 1.414L13.403 0l.83.828-1.415 1.415L11.576 0l.828.83-1.414 1.414L9.746 0l.83.828-1.415 1.415L7.92 0l.827.83-1.414 1.414L6.09 0l.83.828-1.415 1.415L4.263 0l.828.83-1.414 1.414L2.433 0l.83.828-1.415 1.415L.606 0l.827.83L0 2.244V60h60V0H54.627zM0 59.39v-1.415L1.414 60H0v-.61zm0-4.244v-1.414L1.414 55H0v.146zm0-4.244v-1.415L1.414 50H0v.902zm0-4.243v-1.415L1.414 45H0v.902zm0-4.244v-1.414L1.414 40H0v.902zm0-4.243v-1.415L1.414 35H0v.902zm0-4.244v-1.414L1.414 30H0v.902zm0-4.243v-1.415L1.414 25H0v.902zm0-4.244v-1.414L1.414 20H0v.902zm0-4.243v-1.415L1.414 15H0v.902zm0-4.244V8.78L1.414 10H0v.902zm0-4.243V4.122L1.414 5H0v.902zm0-4.244V.536L1.414 0H0v.61zM60 .61v1.414L58.586 0H60v.61zm0 4.244v1.414L58.586 5H60v.902zm0 4.243v1.415L58.586 10H60v.902zm0 4.244v1.414L58.586 15H60v.902zm0 4.243v1.415L58.586 20H60v.902zm0 4.244v1.414L58.586 25H60v.902zm0 4.243v1.415L58.586 30H60v.902zm0 4.244v1.414L58.586 35H60v.902zm0 4.243v1.415L58.586 40H60v.902zm0 4.244v1.414L58.586 45H60v.902zm0 4.243v1.415L58.586 50H60v.902zm0 4.244v1.414L58.586 55H60v.146zm0 4.244v1.415L58.586 60H60v-.61z' fill='%23a7e0e9' fill-opacity='0.15' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    /* ========== MESSAGES ========== */
    .message {
        margin-bottom: 20px;
        max-width: 75%;
        animation: fadeIn 0.25s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message.received {
        margin-right: auto;
    }

    .message.sent {
        margin-left: auto;
    }

    .message-bubble {
        padding: 12px 16px;
        border-radius: 18px;
        position: relative;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        line-height: 1.4;
        word-wrap: break-word;
    }

    .message.received .message-bubble {
        background: white;
        border-bottom-left-radius: 4px;
        border-top-left-radius: 18px;
        border-top-right-radius: 18px;
        border-bottom-right-radius: 18px;
    }

    .message.sent .message-bubble {
        background: linear-gradient(135deg, #a7e0e9 0%, #7cc6d6 100%);
        color: #1a1a1a;
        border-bottom-right-radius: 4px;
        border-top-left-radius: 18px;
        border-top-right-radius: 18px;
        border-bottom-left-radius: 18px;
    }

    .message-image {
        max-width: 240px;
        max-height: 240px;
        border-radius: 12px;
        cursor: pointer;
        transition: transform 0.2s;
        margin: 6px 0;
        display: block;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .message-image:hover {
        transform: scale(1.02);
    }

    /* ========== CHAT INPUT AREA ========== */
    .chat-input-area {
        padding: 20px;
        background: white;
        border-top: 1px solid #e9ecef;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.03);
        position: relative;
        z-index: 10;
    }

    .chat-input-wrapper {
        position: relative;
    }

    .message-input {
        border-radius: 20px;
        border: 1px solid #e1e5e9;
        padding: 12px 20px;
        resize: none;
        transition: all 0.3s;
        font-size: 0.95rem;
        background: #f8f9fa;
        width: 100%;
        min-height: 48px;
        max-height: 120px;
        overflow-y: auto;
    }

    .message-input:focus {
        border-color: #a7e0e9;
        box-shadow: 0 0 0 2px rgba(167, 224, 233, 0.2);
        background: white;
    }

    .send-button {
        position: absolute;
        right: 10px;
        bottom: 10px;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #a7e0e9 0%, #7cc6d6 100%);
        border: none;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        font-size: 1.1rem;
        z-index: 20;
        box-shadow: 0 2px 8px rgba(124, 198, 214, 0.3);
    }

    .send-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(124, 198, 214, 0.3);
    }

    .send-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* ========== ATTACHMENT BUTTONS ========== */
    .attachment-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
    }

    .attachment-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid #e1e5e9;
        background: white;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .attachment-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background: #a7e0e9;
        color: white;
    }

    /* ========== ATTACHMENT PREVIEW - FIXED POSITION ========== */
    .attachment-preview {
        position: absolute;
        bottom: 100%; /* Hiển thị PHÍA TRÊN input area */
        left: 0;
        right: 0;
        margin-top: 12px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px dashed #dee2e6;
        max-height: 300px;
        overflow-y: auto;
        z-index: 5;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
    }

    .preview-title {
        font-weight: 600;
        margin-bottom: 10px;
        color: #495057;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        position: sticky;
        top: 0;
        background: #f8f9fa;
        padding: 5px 0;
        z-index: 1;
    }

    .file-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
        margin-top: 12px;
    }

    .file-preview-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        background: white;
        border: 1px solid #dee2e6;
        transition: all 0.2s;
    }

    .file-preview-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .remove-file {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(124, 198, 214, 0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.7rem;
        transition: all 0.2s;
    }

    .remove-file:hover {
        background: #ff6b6b;
        transform: scale(1.1);
    }

    /* ========== IMAGE PREVIEW ========== */
    .image-preview-section {
        margin-bottom: 15px;
        padding: 15px;
        background: white;
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }

    .preview-card {
        border: 1px solid #dee2e6;
        transition: all 0.2s;
        height: 100%;
    }

    .preview-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .preview-image {
        cursor: pointer;
        transition: transform 0.2s;
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }

    .preview-image:hover {
        transform: scale(1.05);
    }

    .remove-preview-btn {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        position: absolute;
        top: 5px;
        right: 5px;
    }

    /* ========== DRAG & DROP ========== */
    .drop-zone {
        border: 2px dashed #a7e0e9;
        background: rgba(167, 224, 233, 0.05);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        margin-bottom: 16px;
        transition: all 0.3s;
    }

    .drop-zone.dragover {
        background: rgba(167, 224, 233, 0.1);
        border-color: #7cc6d6;
    }

    .drop-zone-icon {
        font-size: 2.2rem;
        color: #a7e0e9;
        margin-bottom: 12px;
        opacity: 0.8;
    }

    /* ========== MODALS ========== */
    .modal-image {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
    }

    #cameraFeed {
        width: 100%;
        max-height: 400px;
        background: #000;
        border-radius: 8px;
    }

    #capturedImage {
        max-height: 200px;
        border-radius: 8px;
    }

    /* ========== WELCOME SCREEN ========== */
    .welcome-screen {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px;
        background: #f0f8ff;
    }

    .welcome-icon {
        font-size: 4.5rem;
        margin-bottom: 20px;
        color: #a7e0e9;
        opacity: 0.9;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 992px) {
        .support-container {
            flex-direction: column;
            height: auto;
            min-height: calc(100vh - 100px);
        }
        
        .conversation-sidebar {
            width: 100%;
            height: auto;
            max-height: 300px;
            border-right: none;
            border-bottom: 1px solid #e9ecef;
        }
        
        .message {
            max-width: 85%;
        }
        
        .attachment-preview {
            max-height: 250px;
        }
    }

    @media (max-width: 576px) {
        .support-container {
            border-radius: 0;
            box-shadow: none;
            height: calc(100vh - 60px);
        }
        
        .chat-header {
            padding: 12px 16px;
        }
        
        .messages-container {
            padding: 12px;
        }
        
        .chat-input-area {
            padding: 12px;
        }
        
        .message {
            max-width: 90%;
        }
        
        .preview-image {
            height: 120px;
        }
        
        .attachment-preview {
            max-height: 200px;
        }
        
        .send-button {
            right: 5px;
            bottom: 5px;
            width: 42px;
            height: 42px;
        }
    }

    /* ========== NOTIFICATION ========== */
    .support-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* ========== SCROLLBAR STYLING ========== */
    .messages-container::-webkit-scrollbar,
    .attachment-preview::-webkit-scrollbar {
        width: 6px;
    }

    .messages-container::-webkit-scrollbar-track,
    .attachment-preview::-webkit-scrollbar-track {
        background: transparent;
    }

    .messages-container::-webkit-scrollbar-thumb,
    .attachment-preview::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .messages-container::-webkit-scrollbar-thumb:hover,
    .attachment-preview::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

<div class="container py-4">
    <div class="support-container">
        <!-- Left Sidebar -->
        <div class="conversation-sidebar">
            <!-- User Profile -->
            <div class="user-profile-card">
                <div class="d-flex align-items-center">
                    <img src="{{ Auth::check() && Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('default-avatar.png') }}"
                        alt="Avatar" class="user-avatar">
                    <div class="user-info">
                        <h5>{{ Auth::check() ? Auth::user()->name : 'Khách' }}</h5>
                        <p>{{ Auth::check() ? Auth::user()->email : 'Đăng nhập để lưu lịch sử' }}</p>
                    </div>
                </div>
            </div>

            <!-- Conversation List -->
            <div class="conversation-list">
                <!-- Chat với hỗ trợ -->
                <div class="conversation-item support-chat active" id="supportChatBtn">
                    <div class="conversation-icon chat-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="conversation-info">
                        <h6>Chat với Hỗ trợ</h6>
                        <p>Phản hồi trong vài phút</p>
                    </div>
                   @if($supportRequest && $unreadCount > 0)
                        <span class="unread-badge">{{ $unreadCount }}</span>
                    @endif
                </div>

                <!-- Chat với AI -->
                <div class="conversation-item ai-chat" id="aiChatBtn">
                    <div class="conversation-icon ai-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="conversation-info">
                        <h6>Trợ lý AI</h6>
                        <p>Hỗ trợ 24/7</p>
                    </div>
                </div>

                <!-- Conversation History -->
                @if($supportRequest)
                <div class="mt-3 px-3">
                    <small class="text-muted">LỊCH SỬ TRÒ CHUYỆN</small>
                </div>
                <div class="conversation-item" id="historyBtn">
                    <div class="conversation-icon" style="background: linear-gradient(135deg, #a7e0e9 0%, #7cc6d6 100%);">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="conversation-info">
                        <h6>Yêu cầu #{{ $supportRequest->id }}</h6>
                        <p>{{ $supportRequest->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-area">
            <!-- Support Chat Header -->
            <div class="chat-header" id="supportHeader">
                <div class="chat-title">
                    <div class="chat-icon-small chat-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h4>Chat với Hỗ trợ</h4>
                        <div class="chat-status">
                            @if($supportRequest)
                                @if($supportRequest->status == 'resolved')
                                    <span class="text-success">✅ Đã giải quyết</span>
                                @elseif($supportRequest->status == 'processing')
                                    <span class="text-warning">🔄 Đang xử lý</span>
                                @else
                                    <span class="text-secondary">⏳ Chờ phản hồi</span>
                                @endif
                            @else
                                <span class="text-success">🟢 Trực tuyến</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    @if($supportRequest)
                        <button class="btn btn-sm btn-outline-danger" id="deleteRequestBtn">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    @endif
                </div>
            </div>

            <!-- AI Chat Header -->
            <div class="chat-header" id="aiHeader" style="display: none;">
                <div class="chat-title">
                    <div class="chat-icon-small ai-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div>
                        <h4>Trợ lý AI</h4>
                        <div class="chat-status">
                            <span class="text-success">🟢 Trực tuyến</span>
                        </div>
                    </div>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" id="clearAiChatBtn">
                        <i class="fas fa-eraser"></i> Xóa chat
                    </button>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="messages-container" id="messagesContainer">
                <!-- Welcome Screen -->
                <div class="welcome-screen" id="welcomeScreen">
                    <div class="welcome-content">
                        <div class="welcome-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3 class="mb-3">Chào mừng đến với Hỗ trợ</h3>
                        <p class="text-muted mb-4">Chọn một cuộc trò chuyện từ danh sách bên trái để bắt đầu</p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="chat-icon-small chat-icon mb-3 mx-auto">
                                            <i class="fas fa-headset"></i>
                                        </div>
                                        <h5>Hỗ trợ trực tiếp</h5>
                                        <p class="text-muted small">Đội ngũ hỗ trợ sẽ phản hồi trong vài phút</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="chat-icon-small ai-icon mb-3 mx-auto">
                                            <i class="fas fa-robot"></i>
                                        </div>
                                        <h5>Trợ lý AI</h5>
                                        <p class="text-muted small">Hỏi đáp tự động 24/7</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support Messages -->
                <div id="supportMessages" style="display: none;">
                    @if($supportRequest)
                        <!-- Original request -->
                        <div class="message sent">
                            <div class="message-sender">
                                <i class="fas fa-user"></i> Bạn
                                <span class="badge bg-light text-dark ms-2">Yêu cầu ban đầu</span>
                            </div>
                            <div class="message-bubble">
                                {{ $supportRequest->message }}
                                @if($supportRequest->attachment)
                                <div class="mt-2">
                                    @php
                                        $ext = pathinfo($supportRequest->attachment, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp
                                    @if($isImage)
                                        <div class="d-inline-block">
                                            <img src="{{ Storage::url($supportRequest->attachment) }}" 
                                                alt="Attachment" 
                                                class="message-image"
                                                onclick="openImageViewModal('{{ Storage::url($supportRequest->attachment) }}')">
                                        </div>
                                    @else
                                        <a href="{{ Storage::url($supportRequest->attachment) }}" 
                                        target="_blank" 
                                        class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-file"></i> {{ basename($supportRequest->attachment) }}
                                        </a>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="message-time">{{ $supportRequest->created_at->format('H:i d/m/Y') }}</div>
                        </div>

                        <!-- Replies -->
                        @foreach($supportRequest->replies->sortBy('created_at') as $reply)
                            <div class="message {{ $reply->is_admin ? 'received' : 'sent' }}">
                                <div class="message-sender">
                                    <i class="fas {{ $reply->is_admin ? 'fa-headset' : 'fa-user' }}"></i>
                                    {{ $reply->is_admin ? 'Hỗ trợ viên' : 'Bạn' }}
                                </div>
                                <div class="message-bubble">
                                    {{ $reply->reply }}
                                    @if($reply->attachment)
                                    <div class="mt-2">
                                        @php
                                            $ext = pathinfo($reply->attachment, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp
                                        @if($isImage)
                                            <div class="d-inline-block">
                                                <img src="{{ Storage::url($reply->attachment) }}" 
                                                    alt="Attachment" 
                                                    class="message-image"
                                                    onclick="openImageViewModal('{{ Storage::url($reply->attachment) }}')">
                                            </div>
                                        @else
                                            <a href="{{ Storage::url($reply->attachment) }}" 
                                            target="_blank" 
                                            class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-file"></i> {{ basename($reply->attachment) }}
                                            </a>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                <div class="message-time">{{ $reply->created_at->format('H:i d/m/Y') }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- AI Messages -->
                <div id="aiMessages" style="display: none;">
                    <!-- AI messages will be loaded here -->
                </div>
            </div>

            <!-- Chat Input Area - FIXED STRUCTURE -->
            <div class="chat-input-area">
                <!-- Drag & Drop Zone -->
                <div class="drop-zone" id="dropZone">
                    <div class="drop-zone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <p class="mb-0">Kéo thả file vào đây hoặc nhấp để chọn</p>
                    <small class="text-muted">Hỗ trợ: ảnh, PDF, Word, Excel</small>
                </div>

                <!-- Attachment Preview - Now positioned ABOVE the input -->
                <div class="attachment-preview" id="attachmentPreview" style="display: none;">
                    <div class="preview-title">
                        <i class="fas fa-paperclip"></i> File đính kèm:
                        <span class="badge bg-primary ms-2" id="fileCount">0 file</span>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-auto" id="clearAllFilesBtn">
                            <i class="fas fa-trash"></i> Xóa tất cả
                        </button>
                    </div>
                    <div id="imagePreviewContainer"></div>
                    <div class="file-preview-grid" id="filePreviewGrid"></div>
                </div>

                <!-- Support Input Form -->
                <div id="supportInputForm" style="display: none;">
                    @if($supportRequest)
                        <form id="replyForm" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="attachment[]" id="supportAttachment" 
                                style="display: none;" 
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt"
                                multiple>
                        
                            

                            <!-- Input and Send Button Container -->
                            <div class="chat-input-wrapper">
                                <textarea class="form-control message-input" name="reply" rows="1" 
                                        placeholder="Nhập tin nhắn..." id="supportMessageInput"></textarea>
                                <button type="submit" class="btn send-button" id="supportSendButton">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center">
                            <a href="{{ route('support.form') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tạo yêu cầu hỗ trợ mới
                            </a>
                        </div>
                    @endif
                </div>

                <!-- AI Input Form -->
                <div id="aiInputForm" style="display: none;">
                    <!-- Câu hỏi gợi ý -->
                    <div class="suggested-questions mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-lightbulb mr-1"></i>Câu hỏi thường gặp:
                        </h6>   
                        <div class="row" id="ai-suggested-questions">
                            <div class="col-12 text-center py-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span class="text-muted ml-2">Đang tải câu hỏi...</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chat Input -->
                    <div class="chat-input-wrapper">
                        <textarea class="form-control message-input" rows="1" 
                                placeholder="Nhập câu hỏi cho AI..." id="aiMessageInput"></textarea>
                        <button class="btn send-button" id="aiSendButton">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // ========== GLOBAL VARIABLES ==========
    let aiSessionId = 'ai_chat_' + Date.now();
    let aiHistory = [];
    let currentChatType = 'support';
    let selectedFiles = [];
    let cameraStream = null;

    // ========== INITIALIZATION ==========
    initChatSystem();
    
    function initChatSystem() {
        setupEventListeners();
        selectConversation('support');
        setupDragAndDrop();
        adjustTextareaHeight();
    }

    // ========== CONVERSATION MANAGEMENT ==========
    function selectConversation(type) {
        currentChatType = type;
        
        // Update UI
        $('.conversation-item').removeClass('active');
        if (type === 'support') {
            $('#supportChatBtn').addClass('active');
            showSupportChat();
        } else {
            $('#aiChatBtn').addClass('active');
            showAIChat();
        }
    }

    function showSupportChat() {
        $('#supportHeader').show();
        $('#aiHeader').hide();
        $('#welcomeScreen').hide();
        $('#supportMessages').show();
        $('#aiMessages').hide();
        $('#supportInputForm').show();
        $('#aiInputForm').hide();
        $('#dropZone').show();
        scrollToBottom();
    }

    function showAIChat() {
        $('#supportHeader').hide();
        $('#aiHeader').show();
        $('#welcomeScreen').hide();
        $('#supportMessages').hide();
        $('#aiMessages').show();
        $('#supportInputForm').hide();
        $('#aiInputForm').show();
        $('#dropZone').hide();
        
        loadSuggestedQuestions();
        scrollToBottom();
    }

    // ========== FILE PREVIEW SYSTEM ==========
    function displayFilePreview(files) {
        const imageContainer = $('#imagePreviewContainer');
        const fileGrid = $('#filePreviewGrid');
        
        // Clear existing previews
        imageContainer.empty();
        fileGrid.empty();
        
        // Update file count
        $('#fileCount').text(`${files.length} file${files.length > 1 ? 's' : ''}`);
        
        // Show preview container
        $('#attachmentPreview').show();
        
        // Separate image and other files
        const imageFiles = files.filter(f => f.type.startsWith('image/'));
        const otherFiles = files.filter(f => !f.type.startsWith('image/'));
        
        // Display images
        if (imageFiles.length > 0) {
            let imageHtml = `
                <div class="image-preview-section">
                    <h6 class="preview-title">
                        <i class="fas fa-image"></i> Ảnh (${imageFiles.length})
                    </h6>
                    <div class="row">
            `;
            
            imageFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imageHtml += `
                        <div class="col-md-4 col-6 mb-3">
                            <div class="card preview-card">
                                <div class="card-body p-2 text-center">
                                    <div class="position-relative">
                                        <img src="${e.target.result}" 
                                             class="preview-image" 
                                             alt="${file.name}"
                                             onclick="openImageViewModal('${e.target.result}')">
                                        <button type="button" 
                                                class="btn btn-sm btn-danger remove-preview-btn"
                                                data-index="${index}"
                                                title="Xóa ảnh">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small class="d-block text-truncate">${file.name}</small>
                                        <small class="text-muted">${formatFileSize(file.size)}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    if (index === imageFiles.length - 1) {
                        imageHtml += '</div></div>';
                        imageContainer.html(imageHtml);
                        
                        // Add remove event listeners
                        $(`.remove-preview-btn`).off('click').on('click', function() {
                            const idx = parseInt($(this).data('index'));
                            removeFile(idx);
                        });
                    }
                };
                reader.readAsDataURL(file);
            });
        }
        
        // Display other files
        if (otherFiles.length > 0) {
            otherFiles.forEach((file, index) => {
                const fileIndex = imageFiles.length + index;
                const icon = getFileIcon(file);
                
                fileGrid.append(`
                    <div class="file-preview-item">
                        <div class="file-info p-2">
                            <div class="d-flex align-items-center">
                                <i class="${icon} fa-lg me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="file-name">${file.name}</div>
                                    <div class="file-size">${formatFileSize(file.size)}</div>
                                </div>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger remove-file"
                                        data-index="${fileIndex}"
                                        title="Xóa file">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
            });
            
            // Add remove event listeners for other files
            $('.remove-file').off('click').on('click', function() {
                const idx = parseInt($(this).data('index'));
                removeFile(idx);
            });
        }
        
        // Scroll preview to top
        $('#attachmentPreview').scrollTop(0);
    }

    function removeFile(index) {
        if (index >= 0 && index < selectedFiles.length) {
            selectedFiles.splice(index, 1);
            updateFileInput();
            
            if (selectedFiles.length > 0) {
                displayFilePreview(selectedFiles);
            } else {
                $('#attachmentPreview').hide();
            }
        }
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        
        $('#supportAttachment')[0].files = dataTransfer.files;
    }

    // ========== FILE UTILITIES ==========
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function getFileIcon(file) {
        const type = file.type;
        if (type.includes('pdf')) return 'fas fa-file-pdf text-danger';
        if (type.includes('word') || type.includes('doc')) return 'fas fa-file-word text-primary';
        if (type.includes('excel') || type.includes('xls')) return 'fas fa-file-excel text-success';
        if (type.includes('text') || type.includes('txt')) return 'fas fa-file-alt';
        return 'fas fa-file';
    }

    // ========== DRAG & DROP ==========
    function setupDragAndDrop() {
        const dropZone = $('#dropZone');
        
        dropZone.on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });
        
        dropZone.on('dragleave', function() {
            $(this).removeClass('dragover');
        });
        
        dropZone.on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            
            const files = Array.from(e.originalEvent.dataTransfer.files);
            if (files.length > 0) {
                selectedFiles = [...selectedFiles, ...files];
                updateFileInput();
                displayFilePreview(selectedFiles);
                showNotification(`Đã thêm ${files.length} file`, 'success');
            }
        });
        
        dropZone.on('click', function() {
            $('#supportAttachment').click();
        });
    }

    // ========== CAMERA FUNCTION ==========
    function capturePhoto() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showNotification('Trình duyệt của bạn không hỗ trợ camera', 'danger');
            return;
        }

        // Create camera modal
        const cameraModal = `
            <div class="modal fade" id="cameraModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">📷 Chụp ảnh</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <video id="cameraFeed" autoplay playsinline></video>
                            <div class="mt-3">
                                <button id="captureBtn" class="btn btn-primary">
                                    <i class="fas fa-camera"></i> Chụp ảnh
                                </button>
                                <button id="retakeBtn" class="btn btn-secondary" style="display: none;">
                                    <i class="fas fa-redo"></i> Chụp lại
                                </button>
                            </div>
                            <canvas id="captureCanvas" style="display: none;"></canvas>
                            <div id="capturePreview" class="mt-3" style="display: none;">
                                <img id="capturedImage" class="img-fluid rounded">
                                <div class="mt-2">
                                    <button id="usePhotoBtn" class="btn btn-success">
                                        <i class="fas fa-check"></i> Sử dụng ảnh này
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add modal to body
        $('body').append(cameraModal);
        const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
        modal.show();
        
        // Start camera
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        })
        .then(function(stream) {
            cameraStream = stream;
            const video = document.getElementById('cameraFeed');
            video.srcObject = stream;
            
            // Handle capture
            $('#captureBtn').off('click').on('click', function() {
                const canvas = document.getElementById('captureCanvas');
                const context = canvas.getContext('2d');
                
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0);
                
                // Show preview
                const dataURL = canvas.toDataURL('image/jpeg', 0.8);
                $('#capturedImage').attr('src', dataURL);
                $('#capturePreview').show();
                $('#captureBtn').hide();
                $('#retakeBtn').show();
                
                // Stop stream
                stream.getTracks().forEach(track => track.stop());
            });
            
            // Handle retake
            $('#retakeBtn').off('click').on('click', function() {
                $('#capturePreview').hide();
                $('#captureBtn').show();
                $('#retakeBtn').hide();
                
                // Restart camera
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(newStream) {
                        cameraStream = newStream;
                        video.srcObject = newStream;
                    });
            });
            
            // Handle use photo
            $('#usePhotoBtn').off('click').on('click', function() {
                const canvas = document.getElementById('captureCanvas');
                canvas.toBlob(function(blob) {
                    const file = new File([blob], `camera_${Date.now()}.jpg`, {
                        type: 'image/jpeg'
                    });
                    
                    // Add file to list
                    selectedFiles.push(file);
                    updateFileInput();
                    displayFilePreview(selectedFiles);
                    
                    // Close modal
                    modal.hide();
                    $('#cameraModal').remove();
                    showNotification('Đã thêm ảnh vào danh sách đính kèm', 'success');
                }, 'image/jpeg', 0.8);
            });
            
            // Handle modal close
            $('#cameraModal').on('hidden.bs.modal', function() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                }
                $(this).remove();
            });
        })
        .catch(function(err) {
            console.error('Camera error:', err);
            showNotification('Không thể truy cập camera. Vui lòng kiểm tra quyền.', 'danger');
            $('#cameraModal').remove();
        });
    }

    // ========== IMAGE VIEW MODAL ==========
    function openImageViewModal(imageSrc) {
        if (!$('#imageViewModal').length) {
            $('body').append(`
                <div class="modal fade" id="imageViewModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Xem ảnh</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img id="modalImageView" class="modal-image">
                            </div>
                            <div class="modal-footer">
                                <a id="downloadImageBtn" class="btn btn-primary" download>
                                    <i class="fas fa-download"></i> Tải xuống
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
        
        $('#modalImageView').attr('src', imageSrc);
        $('#downloadImageBtn').attr('href', imageSrc).attr('download', `image_${Date.now()}.jpg`);
        new bootstrap.Modal(document.getElementById('imageViewModal')).show();
    }

    // ========== NOTIFICATION SYSTEM ==========
    function showNotification(message, type = 'success') {
        // Remove existing notifications
        $('.support-notification').remove();
        
        const icon = type === 'success' ? 'fa-check-circle' :
                    type === 'danger' ? 'fa-exclamation-circle' :
                    type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
        
        const notification = $(`
            <div class="alert alert-${type} alert-dismissible fade show support-notification">
                <i class="fas ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.alert('close');
        }, 5000);
    }

    // ========== TEXTAREA AUTO RESIZE ==========
    function adjustTextareaHeight() {
        $('.message-input').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    // ========== FORM SUBMISSION ==========
    $('#replyForm').on('submit', function(e) {
        e.preventDefault();
        
        @if($supportRequest)
            const formData = new FormData(this);
            const message = $('#supportMessageInput').val().trim();
            
            if (!message && selectedFiles.length === 0) {
                showNotification('Vui lòng nhập tin nhắn hoặc chọn file đính kèm', 'warning');
                return;
            }
            
            // Add selected files to FormData
            selectedFiles.forEach(file => {
                formData.append('attachment[]', file);
            });
            
            $.ajax({
                url: '/support/{{ $supportRequest->id }}/reply',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#supportSendButton').prop('disabled', true)
                        .html('<i class="fas fa-spinner fa-spin"></i>');
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('Đã gửi tin nhắn thành công!', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(response.message || 'Có lỗi xảy ra', 'danger');
                        $('#supportSendButton').prop('disabled', false)
                            .html('<i class="fas fa-paper-plane"></i>');
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'Có lỗi xảy ra khi gửi tin nhắn';
                    showNotification(error, 'danger');
                    $('#supportSendButton').prop('disabled', false)
                        .html('<i class="fas fa-paper-plane"></i>');
                }
            });
        @else
            showNotification('Không có yêu cầu hỗ trợ để gửi tin nhắn', 'warning');
        @endif
    });

    // ========== AI CHAT ==========
    function loadSuggestedQuestions() {
        $('#ai-suggested-questions').html(`
            <div class="col-12 text-center py-2">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <span class="text-muted ml-2">Đang tải câu hỏi...</span>
            </div>
        `);
        
        $.ajax({
            url: '/api/ai-training/suggested-questions',
            method: 'GET',
            data: { limit: 4 },
            success: function(response) {
                if (response.success && response.questions.length > 0) {
                    displaySuggestedQuestions(response.questions);
                }
            },
            error: function() {
                $('#ai-suggested-questions').html(`
                    <div class="col-12">
                        <p class="text-muted text-center small">
                            <i class="fas fa-info-circle mr-1"></i>
                            Chưa có câu hỏi gợi ý
                        </p>
                    </div>
                `);
            }
        });
    }

    function displaySuggestedQuestions(questions) {
        $('#ai-suggested-questions').empty();
        
        questions.forEach(function(question, index) {
            if (index < 4) {
                $('#ai-suggested-questions').append(`
                    <div class="col-md-6 col-12 mb-2">
                        <button class="btn btn-outline-primary btn-block text-left suggested-question-btn" 
                                data-question="${question.question}"
                                data-answer="${question.answer}">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-truncate">${question.question}</span>
                            </div>
                        </button>
                    </div>
                `);
            }
        });
    }

    // ========== EVENT LISTENERS ==========
    function setupEventListeners() {
        // Conversation selection
        $('#supportChatBtn').on('click', () => selectConversation('support'));
        $('#aiChatBtn').on('click', () => selectConversation('ai'));
        
        // File operations
        $('#fileUploadBtn').on('click', () => $('#supportAttachment').click());
        $('#cameraBtn').on('click', capturePhoto);
        
        // File input change
        $('#supportAttachment').on('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                selectedFiles = [...selectedFiles, ...files];
                updateFileInput();
                displayFilePreview(selectedFiles);
                showNotification(`Đã thêm ${files.length} file`, 'success');
            }
        });
        
        // Clear all files
        $('#clearAllFilesBtn').on('click', function() {
            if (selectedFiles.length > 0) {
                if (confirm(`Bạn có chắc muốn xóa tất cả (${selectedFiles.length}) file?`)) {
                    selectedFiles = [];
                    updateFileInput();
                    $('#attachmentPreview').hide();
                    showNotification('Đã xóa tất cả file', 'success');
                }
            }
        });
        
        // AI chat
        $('#aiSendButton').on('click', sendAIMessage);
        $('#aiMessageInput').on('keypress', function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                sendAIMessage();
            }
        });
        
        // Suggested questions
        $(document).on('click', '.suggested-question-btn', function() {
            const question = $(this).data('question');
            const answer = $(this).data('answer');
            addAIMessage(question, true);
            
            setTimeout(() => {
                addAIMessage(answer, false);
            }, 500);
        });
        
        // Clear AI chat
        $('#clearAiChatBtn').on('click', function() {
            if (confirm('Bạn có chắc muốn xóa toàn bộ cuộc trò chuyện với AI?')) {
                $('#aiMessages').empty();
                aiHistory = [];
                aiSessionId = 'ai_chat_' + Date.now();
                showNotification('Đã xóa cuộc trò chuyện AI', 'success');
            }
        });
        
        // Delete support request
        $('#deleteRequestBtn').on('click', function() {
            @if($supportRequest)
                if (confirm('Bạn có chắc muốn xóa yêu cầu hỗ trợ này?')) {
                    $.ajax({
                        url: '/support-requests/{{ $supportRequest->id }}',
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                showNotification('Đã xóa yêu cầu hỗ trợ', 'success');
                                setTimeout(() => location.reload(), 1000);
                            }
                        },
                        error: function() {
                            showNotification('Có lỗi xảy ra khi xóa', 'danger');
                        }
                    });
                }
            @endif
        });
        
        // Click outside to hide preview
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.attachment-preview').length && 
                !$(e.target).closest('.attachment-btn').length &&
                !$(e.target).is('#supportAttachment')) {
                // Keep preview visible when files exist
            }
        });
    }

    // ========== UTILITY FUNCTIONS ==========
    function scrollToBottom() {
        setTimeout(() => {
            const container = $('#messagesContainer')[0];
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    }

    function addAIMessage(message, isUser = true) {
        const timestamp = new Date().toLocaleTimeString('vi-VN', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const html = `
            <div class="message ${isUser ? 'sent' : 'received'}">
                <div class="message-sender">
                    <i class="fas ${isUser ? 'fa-user' : 'fa-robot'}"></i>
                    ${isUser ? 'Bạn' : 'Trợ lý AI'}
                </div>
                <div class="message-bubble">
                    ${message}
                </div>
                <div class="message-time">${timestamp}</div>
            </div>
        `;
        
        $('#aiMessages').append(html);
        scrollToBottom();
    }

    function sendAIMessage() {
        const message = $('#aiMessageInput').val().trim();
        if (!message) return;
        
        addAIMessage(message, true);
        $('#aiMessageInput').val('');
        
        // Simulate AI response
        setTimeout(() => {
            const responses = [
                "Tôi đã nhận được câu hỏi của bạn. Để tôi giúp bạn với vấn đề này.",
                "Cảm ơn bạn đã liên hệ. Tôi sẽ cố gắng giải đáp thắc mắc của bạn.",
                "Đây là câu trả lời từ hệ thống AI. Nếu bạn cần hỗ trợ thêm, vui lòng liên hệ nhân viên.",
                "Tôi hiểu vấn đề của bạn. Dưới đây là một số giải pháp đề xuất..."
            ];
            const randomResponse = responses[Math.floor(Math.random() * responses.length)];
            addAIMessage(randomResponse, false);
        }, 1000);
    }

    // ========== INITIAL SCROLL ==========
    scrollToBottom();
});
</script>
@endsection