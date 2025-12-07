        @extends('app')

        @section('content')
        <style>
            .chat-container {
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px;
    background-color: #f8f9fa;
    height: 400px;
    overflow-y: auto;
}

.user-message {
    text-align: right;
}

.bot-message {
    text-align: left;
}

.message-content {
    max-width: 80%;
    display: inline-block;
    text-align: left;
    word-wrap: break-word;
}

.user-message .message-content {
    float: right;
    background: linear-gradient(135deg, #a7e0e9 0%, #7cc6d6 100%);
    color: #1a1a1a;
}

.bot-message .message-content {
    float: left;
    background: white;
    border: 1px solid #dee2e6;
}

.faq-btn {
    transition: all 0.3s;
    margin: 2px;
}

.faq-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

#aiChatModal .modal-header {
    background: linear-gradient(135deg, #b19cd9 0%, #8a63d2 100%);
}

#aiChatModal .close {
    opacity: 1;
}

#aiChatModal .close:hover {
    opacity: 0.8;
}
            /* Main container */
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

            /* Left sidebar */
            .conversation-sidebar {
                width: 360px;
                background: #f8f9fa;
                color: #333;
                padding: 0;
                display: flex;
                flex-direction: column;
                border-right: 1px solid #e9ecef;
            }

            /* User profile */
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

            .user-info h5 {
                margin: 0;
                font-weight: 600;
                color: #1a1a1a;
                font-size: 1.1rem;
            }

            .user-info p {
                margin: 4px 0 0 0;
                color: #666;
                font-size: 0.85rem;
            }

            /* Conversation list */
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
                color: #1a1a1a;
                box-shadow: 0 4px 15px rgba(124, 198, 214, 0.2);
            }

            .conversation-item.active .conversation-info h6,
            .conversation-item.active .conversation-info p {
                color: #1a1a1a;
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

            /* Màu xanh mạt cho Chat với Hỗ trợ */
            .support-chat .conversation-item.active {
                background: linear-gradient(135deg, #a7e0e9 0%, #7cc6d6 100%);
            }

            .chat-icon {
                background: linear-gradient(135deg, #a7e0e9 0%, #7cc6d6 100%);
            }

            /* Màu tím nhạt cho Trợ lý AI */
            .ai-chat .conversation-item.active {
                background: linear-gradient(135deg, #b19cd9 0%, #8a63d2 100%);
            }

            .ai-icon {
                background: linear-gradient(135deg, #b19cd9 0%, #8a63d2 100%);
            }

            .conversation-info {
                flex: 1;
            }

            .conversation-info h6 {
                margin: 0;
                font-weight: 600;
                color: #1a1a1a;
                font-size: 0.95rem;
            }

            .conversation-info p {
                margin: 4px 0 0 0;
                color: #666;
                font-size: 0.82rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 220px;
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

            /* Chat area */
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

            .chat-title {
                display: flex;
                align-items: center;
                gap: 15px;
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

            .chat-title h4 {
                margin: 0;
                font-weight: 600;
                color: #1a1a1a;
                font-size: 1.2rem;
            }

            .chat-status {
                font-size: 0.85rem;
                color: #666;
                margin-top: 4px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            /* Messages container */
            .messages-container {
                flex: 1;
                overflow-y: auto;
                padding: 20px;
                background: #f0f8ff;
                background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.828-1.415 1.415L51.8 0l.828.83-1.414 1.414L49.97 0l.83.828-1.415 1.415L48.143 0l.828.83-1.414 1.414L46.313 0l.83.828-1.415 1.415L44.486 0l.828.83-1.414 1.414L42.656 0l.83.828-1.415 1.415L40.83 0l.827.83-1.414 1.414L39 0l.83.828-1.415 1.415L37.173 0l.828.83-1.414 1.414L35.343 0l.83.828-1.415 1.415L33.516 0l.828.83-1.414 1.414L31.686 0l.83.828-1.415 1.415L29.86 0l.827.83-1.414 1.414L28.03 0l.83.828-1.415 1.415L26.203 0l.828.83-1.414 1.414L24.373 0l.83.828-1.415 1.415L22.546 0l.828.83-1.414 1.414L20.716 0l.83.828-1.415 1.415L18.89 0l.827.83-1.414 1.414L17.06 0l.83.828-1.415 1.415L15.233 0l.828.83-1.414 1.414L13.403 0l.83.828-1.415 1.415L11.576 0l.828.83-1.414 1.414L9.746 0l.83.828-1.415 1.415L7.92 0l.827.83-1.414 1.414L6.09 0l.83.828-1.415 1.415L4.263 0l.828.83-1.414 1.414L2.433 0l.83.828-1.415 1.415L.606 0l.827.83L0 2.244V60h60V0H54.627zM0 59.39v-1.415L1.414 60H0v-.61zm0-4.244v-1.414L1.414 55H0v.146zm0-4.244v-1.415L1.414 50H0v.902zm0-4.243v-1.415L1.414 45H0v.902zm0-4.244v-1.414L1.414 40H0v.902zm0-4.243v-1.415L1.414 35H0v.902zm0-4.244v-1.414L1.414 30H0v.902zm0-4.243v-1.415L1.414 25H0v.902zm0-4.244v-1.414L1.414 20H0v.902zm0-4.243v-1.415L1.414 15H0v.902zm0-4.244V8.78L1.414 10H0v.902zm0-4.243V4.122L1.414 5H0v.902zm0-4.244V.536L1.414 0H0v.61zM60 .61v1.414L58.586 0H60v.61zm0 4.244v1.414L58.586 5H60v.902zm0 4.243v1.415L58.586 10H60v.902zm0 4.244v1.414L58.586 15H60v.902zm0 4.243v1.415L58.586 20H60v.902zm0 4.244v1.414L58.586 25H60v.902zm0 4.243v1.415L58.586 30H60v.902zm0 4.244v1.414L58.586 35H60v.902zm0 4.243v1.415L58.586 40H60v.902zm0 4.244v1.414L58.586 45H60v.902zm0 4.243v1.415L58.586 50H60v.902zm0 4.244v1.414L58.586 55H60v.146zm0 4.244v1.415L58.586 60H60v-.61z' fill='%23a7e0e9' fill-opacity='0.15' fill-rule='evenodd'/%3E%3C/svg%3E");
            }

            /* Message bubbles */
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

            .message-sender {
                font-weight: 600;
                font-size: 0.85rem;
                margin-bottom: 6px;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #1a1a1a !important;
            }

            .message.received .message-sender {
                color: #1a1a1a !important;
            }

            .message.sent .message-sender {
                color: #1a1a1a !important;
                justify-content: flex-end;
            }

            .message-sender i {
                color: #7cc6d6;
                font-size: 0.85rem;
            }

            .message-time {
                font-size: 0.7rem;
                opacity: 0.7;
                margin-top: 6px;
                text-align: right;
            }

            .message.sent .message-time {
                color: rgba(26, 26, 26, 0.7);
            }

            /* Image in messages */
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

            .image-preview-container {
                position: relative;
                display: inline-block;
            }

            .image-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.3s;
                border-radius: 12px;
            }

            .image-preview-container:hover .image-overlay {
                opacity: 1;
            }

            .image-overlay-btn {
                background: rgba(255, 255, 255, 0.95);
                border: none;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 5px;
                cursor: pointer;
                transition: all 0.3s;
                color: #333;
            }

            .image-overlay-btn:hover {
                background: white;
                transform: scale(1.1);
                color: #7cc6d6;
            }

            /* File attachment */
            .file-attachment {
                display: inline-flex;
                align-items: center;
                padding: 10px 14px;
                background: rgba(255, 255, 255, 0.95);
                border-radius: 10px;
                margin-top: 8px;
                text-decoration: none;
                color: #333;
                gap: 10px;
                border: 1px solid #e9ecef;
                transition: all 0.2s;
            }

            .file-attachment:hover {
                background: white;
                text-decoration: none;
                color: #333;
                border-color: #a7e0e9;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(167, 224, 233, 0.15);
            }

            /* Input area */
            .chat-input-area {
                padding: 20px;
                background: white;
                border-top: 1px solid #e9ecef;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.03);
            }

            .input-group {
                gap: 10px;
            }

            .message-input {
                border-radius: 20px;
                border: 1px solid #e1e5e9;
                padding: 12px 20px;
                resize: none;
                transition: all 0.3s;
                font-size: 0.95rem;
                background: #f8f9fa;
            }

            .message-input:focus {
                border-color: #a7e0e9;
                box-shadow: 0 0 0 2px rgba(167, 224, 233, 0.2);
                background: white;
            }

            .send-button {
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
            }

            .send-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(124, 198, 214, 0.3);
            }

            .send-button:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                transform: none !important;
                box-shadow: none !important;
            }

            /* Attachment buttons với icon đại diện */
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
                position: relative;
            }

            /* Icon đại diện cho các nút đính kèm */
            .attachment-btn[title="Gửi file cho AI"] i.fa-paperclip,
            .attachment-btn[title="Gửi ảnh/file cho AI"] i.fa-paperclip {
                color: #a7e0e9;
                transition: color 0.2s;
            }

            .attachment-btn[title="Chụp ảnh"] i.fa-camera,
            .attachment-btn[title="Chụp ảnh"] i.fa-camera {
                color: #7cc6d6;
                transition: color 0.2s;
            }

            .attachment-btn[title="Thư viện ảnh"] i.fa-images,
            .attachment-btn[title="Chọn từ thư viện"] i.fa-images {
                color: #b19cd9;
                transition: color 0.2s;
            }

            /* Tooltip cho attachment buttons */
            .attachment-btn::after {
                content: attr(title);
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 0.75rem;
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.2s, visibility 0.2s;
                z-index: 10;
            }

            .attachment-btn:hover::after {
                opacity: 1;
                visibility: visible;
            }

            .attachment-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            /* Màu nền khi hover cho từng nút */
            .attachment-btn[title="Gửi file cho AI"]:hover,
            .attachment-btn[title="Gửi ảnh/file cho AI"]:hover {
                background: #a7e0e9;
                border-color: #a7e0e9;
            }

            .attachment-btn[title="Chụp ảnh"]:hover,
            .attachment-btn[title="Chụp ảnh"]:hover {
                background: #7cc6d6;
                border-color: #7cc6d6;
            }

            .attachment-btn[title="Thư viện ảnh"]:hover,
            .attachment-btn[title="Chọn từ thư viện"]:hover {
                background: #b19cd9;
                border-color: #b19cd9;
            }

            /* Đổi màu icon khi hover */
            .attachment-btn:hover i {
                color: white !important;
            }

            /* Attachment preview */
            .attachment-preview {
                margin-top: 12px;
                padding: 16px;
                background: #f8f9fa;
                border-radius: 12px;
                border: 2px dashed #dee2e6;
            }

            .preview-title {
                font-weight: 600;
                margin-bottom: 10px;
                color: #495057;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                gap: 8px;
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

            .image-preview {
                width: 100%;
                height: 100px;
                object-fit: cover;
            }

            .file-info {
                padding: 8px;
                background: white;
            }

            .file-name {
                font-size: 0.78rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                margin-bottom: 4px;
                color: #333;
            }

            .file-size {
                font-size: 0.7rem;
                color: #666;
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

            /* Drag and drop area */
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

            /* Typing indicator */
            .typing-indicator {
                display: inline-flex;
                align-items: center;
                padding: 10px 16px;
                background: white;
                border-radius: 18px;
                border-bottom-left-radius: 4px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }

            .typing-dots {
                display: inline-flex;
                margin-left: 8px;
            }

            .typing-dots span {
                width: 6px;
                height: 6px;
                background: #a7e0e9;
                border-radius: 50%;
                margin: 0 2px;
                animation: typing 1.4s infinite;
            }

            .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
            .typing-dots span:nth-child(3) { animation-delay: 0.4s; }

            @keyframes typing {
                0%, 60%, 100% { opacity: 0.3; transform: translateY(0); }
                30% { opacity: 1; transform: translateY(-3px); }
            }

            /* Thêm vào phần style để đảm bảo icon hiển thị đúng */
        .fas, .fa, .far, .fab {
            font-family: 'Font Awesome 6 Free' !important;
            font-weight: 900;
        }

        /* Đảm bảo icon không bị ẩn */
        i[class^="fa-"], i[class*=" fa-"] {
            display: inline-block;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Fix cho icon trong attachment buttons */
        .attachment-btn i {
            font-size: 18px !important;
            line-height: 1;
        }

        /* Nếu vẫn không hiển thị, thêm fallback cho icon */
        .fa-file-image::before {
            content: "\f1c5"; /* Unicode của icon file-image */
        }
        .fa-camera-retro::before {
            content: "\f083"; /* Unicode của icon camera-retro */
        }
        .fa-image::before {
            content: "\f03e"; /* Unicode của icon image */
        }
            /* Welcome screen */
            .welcome-screen {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 40px;
                background: #f0f8ff;
            }

            .welcome-content {
                max-width: 500px;
            }

            .welcome-icon {
                font-size: 4.5rem;
                margin-bottom: 20px;
                color: #a7e0e9;
                opacity: 0.9;
            }

            .welcome-content h3 {
                margin-bottom: 16px;
                color: #1a1a1a;
                font-weight: 600;
            }

            .welcome-content p {
                color: #666;
                margin-bottom: 30px;
                font-size: 1rem;
            }

            /* Cards in welcome screen */
            .card {
                border: none;
                border-radius: 16px;
                overflow: hidden;
                transition: all 0.3s;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                background: white;
            }

            .card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 24px rgba(167, 224, 233, 0.2);
            }

            .card-body {
                padding: 20px;
            }

            /* Quick action buttons */
            .quick-buttons {
                display: flex;
                gap: 8px;
                margin-top: 16px;
                flex-wrap: wrap;
            }

            .quick-buttons .btn {
                border-radius: 20px;
                padding: 8px 16px;
                font-size: 0.85rem;
                border: 1px solid #e1e5e9;
                transition: all 0.2s;
                background: white;
                color: #333;
            }

            .quick-buttons .btn:hover {
                border-color: #a7e0e9;
                background: #a7e0e9;
                color: #1a1a1a;
                transform: translateY(-1px);
            }

            /* Image modal */
            .image-modal .modal-dialog {
                max-width: 90vw;
                max-height: 90vh;
            }

            .image-modal .modal-content {
                border-radius: 16px;
                overflow: hidden;
                border: none;
            }

            .image-modal .modal-header {
                background: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
            }

            .image-modal .modal-body {
                padding: 0;
                text-align: center;
                background: #f8f9fa;
            }

            .modal-image {
                max-width: 100%;
                max-height: 70vh;
                object-fit: contain;
            }

            .image-modal .modal-footer {
                background: #f8f9fa;
                border-top: 1px solid #e9ecef;
            }

            /* Camera modal */
            .camera-modal .modal-dialog {
                max-width: 500px;
            }

            .camera-modal .modal-content {
                border-radius: 16px;
                overflow: hidden;
            }

            #cameraPreview {
                width: 100%;
                height: 400px;
                object-fit: cover;
                background: #000;
            }

            /* Responsive */
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
                
                .conversation-list {
                    max-height: 200px;
                }
                
                .message {
                    max-width: 85%;
                }
                
                .message-image {
                    max-width: 200px;
                }
                
                .file-preview-grid {
                    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
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
                    padding: 16px;
                }
                
                .message {
                    max-width: 90%;
                }
                
                .message-image {
                    max-width: 180px;
                }
                
                .quick-buttons {
                    justify-content: center;
                }
                
                .quick-buttons .btn {
                    flex: 1;
                    min-width: 120px;
                }
            }
.message-bubble {
    position: relative;
    word-wrap: break-word;
}

.ai-message .message-bubble {
    border-top-left-radius: 0 !important;
}

.user-message .message-bubble {
    border-top-right-radius: 0 !important;
}

.typing-indicator {
    display: flex;
    align-items: center;
    height: 20px;
}

.typing-indicator span {
    height: 8px;
    width: 8px;
    background: #6c757d;
    border-radius: 50%;
    margin: 0 2px;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-indicator span:nth-child(1) { animation-delay: 0s; }
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-5px); }
}

.suggested-question-btn {
    font-size: 0.875rem;
    text-align: left;
    white-space: normal;
    height: auto;
    padding: 8px 12px;
}

.suggested-question-btn:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}
            /* Scrollbar styling */
            .conversation-list::-webkit-scrollbar,
            .messages-container::-webkit-scrollbar {
                width: 6px;
            }

            .conversation-list::-webkit-scrollbar-track,
            .messages-container::-webkit-scrollbar-track {
                background: transparent;
            }

            .conversation-list::-webkit-scrollbar-thumb,
            .messages-container::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 10px;
            }

            .conversation-list::-webkit-scrollbar-thumb:hover,
            .messages-container::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }

            /* Badge styling */
            .badge {
                padding: 4px 8px;
                font-weight: 500;
                font-size: 0.75rem;
            }

            /* Button styling */
            .btn {
                border-radius: 10px;
                font-weight: 500;
                transition: all 0.2s;
            }

            .btn-outline-danger {
                border-color: #ff6b6b;
                color: #ff6b6b;
            }

            .btn-outline-danger:hover {
                background: #ff6b6b;
                border-color: #ff6b6b;
                color: white;
            }

            .btn-outline-secondary {
                border-color: #a7e0e9;
                color: #7cc6d6;
            }

            .btn-outline-secondary:hover {
                background: #a7e0e9;
                border-color: #a7e0e9;
                color: #1a1a1a;
            }

            .btn-primary {
                background: linear-gradient(135deg, #a7e0e9 0%, #7cc6d6 100%);
                border: none;
                color: #1a1a1a;
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #7cc6d6 0%, #a7e0e9 100%);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(124, 198, 214, 0.3);
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
                        <!-- Chat với hỗ trợ (màu xanh mạt) -->
                        <div class="conversation-item support-chat active" onclick="selectConversation('support')" id="supportChatBtn">
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

                        <!-- Chat với AI (màu tím nhạt) -->
                        <div class="conversation-item ai-chat" onclick="selectConversation('ai')" id="aiChatBtn">
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
                        <div class="conversation-item" onclick="loadSupportHistory({{ $supportRequest->id }})">
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
                    <div class="chat-header" id="supportHeader" style="display: none;">
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
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSupportRequest({{ $supportRequest->id }})">
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
                            <button class="btn btn-sm btn-outline-secondary" onclick="clearAIChat()">
                                <i class="fas fa-eraser"></i> Xóa cuộc trò chuyện
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
                                                <div class="image-preview-container">
                                                    <img src="{{ Storage::url($supportRequest->attachment) }}" 
                                                        alt="File đính kèm" 
                                                        class="message-image"
                                                        onclick="viewImage('{{ Storage::url($supportRequest->attachment) }}')">
                                                    <div class="image-overlay">
                                                        <button class="image-overlay-btn" onclick="viewImage('{{ Storage::url($supportRequest->attachment) }}')">
                                                            <i class="fas fa-search-plus"></i>
                                                        </button>
                                                        <a href="{{ Storage::url($supportRequest->attachment) }}" 
                                                        target="_blank" 
                                                        class="image-overlay-btn">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <a href="{{ Storage::url($supportRequest->attachment) }}" 
                                                target="_blank" 
                                                class="file-attachment">
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
                                                    <div class="image-preview-container">
                                                        <img src="{{ Storage::url($reply->attachment) }}" 
                                                            alt="File đính kèm" 
                                                            class="message-image"
                                                            onclick="viewImage('{{ Storage::url($reply->attachment) }}')">
                                                        <div class="image-overlay">
                                                            <button class="image-overlay-btn" onclick="viewImage('{{ Storage::url($reply->attachment) }}')">
                                                                <i class="fas fa-search-plus"></i>
                                                            </button>
                                                            <a href="{{ Storage::url($reply->attachment) }}" 
                                                            target="_blank" 
                                                            class="image-overlay-btn">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <a href="{{ Storage::url($reply->attachment) }}" 
                                                    target="_blank" 
                                                    class="file-attachment">
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

                    <!-- Chat Input Area -->
                    <div class="chat-input-area">
                        <!-- Attachment Preview -->
                        <div class="attachment-preview" id="attachmentPreview" style="display: none;">
                            <div class="preview-title">📎 File đính kèm:</div>
                            <div class="file-preview-grid" id="filePreviewGrid"></div>
                        </div>

                        <!-- Support Input Form -->
                        <div id="supportInputForm" style="display: none;">
                            @if($supportRequest)
                                <form id="replyForm" enctype="multipart/form-data" onsubmit="sendSupportReply(event)">
                                    @csrf
                                    <input type="file" name="attachment" id="supportAttachment" 
                                        style="display: none;" 
                                        accept="image/*,.pdf,.doc,.docx,.txt"
                                        multiple>
                                    
                                <!-- Trong phần AI Input Form, thay đổi attachment-buttons -->
        <!-- Support Attachment Buttons -->
                            <div class="attachment-buttons">
                                <div class="attachment-btn upload-btn" 
                                    onclick="document.getElementById('supportAttachment').click()" 
                                    title="Gửi file/ảnh">
                                    <i class="fas fa-paperclip"></i>
                                </div>
                                <div class="attachment-btn camera-btn" 
                                    onclick="takePhoto()" 
                                    title="Chụp ảnh">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div class="attachment-btn gallery-btn" 
                                    onclick="openGallery()" 
                                    title="Chọn từ thư viện">
                                    <i class="fas fa-images"></i>
                                </div>
                            </div>

                                    <div class="row mt-2">
                                        <div class="col-10">
                                            <textarea class="form-control message-input" name="reply" rows="1" 
                                                    placeholder="Nhập tin nhắn..." id="supportMessageInput"></textarea>
                                        </div>
                                        <div class="col-2">
                                        <button type="submit" class="btn send-button" id="supportSendButton">
                                                <i class="fas fa-paper-plane me-1"></i> Gửi
                                            </button>

                                        </div>
                                    </div>
                                </form>
                            @else
                                <div class="text-center">
                                    <button class="btn btn-primary btn-lg" onclick="createSupportRequest()">
                                        <i class="fas fa-plus me-2"></i> Tạo yêu cầu hỗ trợ mới
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- AI Input Form -->
                        <div id="aiInputForm" style="display: none;">
                    <div class="text-center py-4">
                        <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#aiChatModal">
                            <i class="fas fa-robot me-2"></i> Mở Trợ lý AI Chat
                        </button>
                        <p class="text-muted mt-2">Nhấn để mở cửa sổ chat với AI</p>
                        
                        <!-- Quick Actions -->
                        <div class="quick-buttons mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickAIQuestion('tra_cuu_don_hang')">
                                <i class="fas fa-box"></i> Tra cứu đơn hàng
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickAIQuestion('tu_van_san_pham')">
                                <i class="fas fa-tshirt"></i> Tư vấn sản phẩm
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickAIQuestion('chinh_sach_doi_tra')">
                                <i class="fas fa-exchange-alt"></i> Chính sách đổi trả
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal AI Chat - Cập nhật phần này -->
<div class="modal fade" id="aiChatModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-robot mr-2"></i>Trợ lý AI - Hỗ trợ khách hàng
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Câu hỏi gợi ý từ AI Training -->
                <div class="suggested-questions mb-4">
                    <h6 class="text-muted mb-2">
                        <i class="fas fa-lightbulb mr-1"></i>Câu hỏi thường gặp:
                    </h6>
                    <div class="row" id="ai-suggested-questions">
                        <!-- Câu hỏi sẽ được load bằng AJAX -->
                        <div class="col-12 text-center py-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <span class="text-muted ml-2">Đang tải câu hỏi...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Chat Container -->
                <div class="chat-container" style="height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 5px; padding: 15px;" 
                     id="aiChatMessages">
                    <!-- Tin nhắn sẽ được thêm vào đây -->
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-robot fa-2x mb-2"></i>
                        <p>Xin chào! Tôi là trợ lý AI của cửa hàng. Tôi có thể giúp gì cho bạn?</p>
                    </div>
                </div>
                
                <!-- Input area -->
                <div class="input-group mt-3">
                    <input type="text" class="form-control" id="aiMessageInput" 
                           placeholder="Nhập câu hỏi của bạn...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" id="sendAiMessage">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Tìm kiếm câu hỏi từ database -->
                <div class="mt-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchAiQuestion" 
                               placeholder="Tìm kiếm câu trả lời nhanh...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="searchAiBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div id="searchResults" class="mt-2" style="display: none;">
                        <!-- Kết quả tìm kiếm sẽ hiển thị ở đây -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="clearAiChat">
                    <i class="fas fa-broom mr-1"></i>Xóa chat
                </button>
                <button type="button" class="btn btn-outline-info" id="exportAiChat">
                    <i class="fas fa-download mr-1"></i>Xuất chat
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    let aiSessionId = 'ai_chat_' + Date.now();
    let aiHistory = [];
    
    // Load câu hỏi gợi ý khi mở modal
    $('#aiChatModal').on('show.bs.modal', function() {
        loadSuggestedQuestions();
    });
    
    // Hàm load câu hỏi gợi ý từ AI Training
    function loadSuggestedQuestions() {
        $.ajax({
            url: '/api/ai-training/suggested-questions',
            method: 'GET',
            success: function(response) {
                $('#ai-suggested-questions').empty();
                
                if (response.success && response.questions.length > 0) {
                    response.questions.forEach(function(question) {
                        let categoryBadge = question.category ? 
                            `<span class="badge badge-info badge-sm">${question.category}</span>` : '';
                        
                        let html = `
                            <div class="col-md-6 col-12 mb-2">
                                <button class="btn btn-outline-primary btn-block text-left suggested-question-btn" 
                                        data-question="${question.question}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-truncate">${question.question}</span>
                                        ${categoryBadge}
                                    </div>
                                </button>
                            </div>
                        `;
                        $('#ai-suggested-questions').append(html);
                    });
                    
                    // Thêm event click cho các câu hỏi gợi ý
                    $('.suggested-question-btn').on('click', function() {
                        let question = $(this).data('question');
                        $('#aiMessageInput').val(question);
                        $('#sendAiMessage').click();
                    });
                } else {
                    $('#ai-suggested-questions').html(`
                        <div class="col-12">
                            <p class="text-muted text-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                Chưa có câu hỏi gợi ý
                            </p>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#ai-suggested-questions').html(`
                    <div class="col-12">
                        <p class="text-danger text-center">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Lỗi khi tải câu hỏi
                        </p>
                    </div>
                `);
            }
        });
    }
    
    // Hàm thêm message vào chat
    function addAIMessage(message, isUser = true) {
        let timestamp = new Date().toLocaleTimeString('vi-VN', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        let messageClass = isUser ? 'user-message' : 'ai-message';
        let icon = isUser ? 'fas fa-user' : 'fas fa-robot';
        let align = isUser ? 'text-right' : 'text-left';
        
        let html = `
            <div class="message ${messageClass} mb-2 ${align}">
                <div class="d-flex ${isUser ? 'justify-content-end' : 'justify-content-start'}">
                    ${!isUser ? `<div class="mr-2"><i class="${icon}"></i></div>` : ''}
                    <div class="message-bubble ${isUser ? 'bg-primary text-white' : 'bg-light'} p-3 rounded" 
                         style="max-width: 70%;">
                        <div class="message-content">${message}</div>
                        <div class="message-time text-${isUser ? 'white-50' : 'muted'} mt-1" 
                             style="font-size: 0.75rem;">
                            <i class="far fa-clock mr-1"></i>${timestamp}
                        </div>
                    </div>
                    ${isUser ? `<div class="ml-2"><i class="${icon}"></i></div>` : ''}
                </div>
            </div>
        `;
        
        $('#aiChatMessages').append(html);
        $('#aiChatMessages').scrollTop($('#aiChatMessages')[0].scrollHeight);
        
        // Lưu vào history
        aiHistory.push({
            message: message,
            isUser: isUser,
            timestamp: new Date().toISOString()
        });
    }
    
    // Gửi message AI
    $('#sendAiMessage').on('click', function() {
        let message = $('#aiMessageInput').val().trim();
        if (!message) return;
        
        // Thêm message của user
        addAIMessage(message, true);
        $('#aiMessageInput').val('');
        
        // Hiển thị typing indicator
        let typingHtml = `
            <div class="message ai-message mb-2 text-left">
                <div class="d-flex justify-content-start">
                    <div class="mr-2"><i class="fas fa-robot"></i></div>
                    <div class="message-bubble bg-light p-3 rounded">
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#aiChatMessages').append(typingHtml);
        $('#aiChatMessages').scrollTop($('#aiChatMessages')[0].scrollHeight);
        
        // Gọi API AI
        $.ajax({
            url: '/api/ai/chat',
            method: 'POST',
            data: {
                message: message,
                session_id: aiSessionId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Xóa typing indicator
                $('.typing-indicator').parent().parent().parent().remove();
                
                if (response.success) {
                    // Hiển thị câu trả lời
                    addAIMessage(response.answer, false);
                    
                    // Nếu có gợi ý follow-up
                    if (response.follow_up_questions && response.follow_up_questions.length > 0) {
                        let followUpHtml = `
                            <div class="message ai-message mb-2 text-left">
                                <div class="d-flex justify-content-start">
                                    <div class="mr-2"><i class="fas fa-robot"></i></div>
                                    <div class="message-bubble bg-light p-3 rounded">
                                        <small class="text-muted d-block mb-2">
                                            <i class="fas fa-lightbulb mr-1"></i>Bạn có thể hỏi tiếp:
                                        </small>
                                        <div class="follow-up-questions">
                        `;
                        
                        response.follow_up_questions.forEach(function(followUp) {
                            followUpHtml += `
                                <button class="btn btn-sm btn-outline-primary mr-2 mb-2 follow-up-btn"
                                        data-question="${followUp}">
                                    ${followUp}
                                </button>
                            `;
                        });
                        
                        followUpHtml += `
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        $('#aiChatMessages').append(followUpHtml);
                        $('#aiChatMessages').scrollTop($('#aiChatMessages')[0].scrollHeight);
                        
                        // Thêm event cho follow-up buttons
                        $('.follow-up-btn').on('click', function() {
                            let question = $(this).data('question');
                            $('#aiMessageInput').val(question);
                            $('#sendAiMessage').click();
                        });
                    }
                } else {
                    addAIMessage('Xin lỗi, tôi không thể trả lời câu hỏi này ngay lúc này. Vui lòng liên hệ nhân viên hỗ trợ.', false);
                }
            },
            error: function() {
                $('.typing-indicator').parent().parent().parent().remove();
                addAIMessage('Đã xảy ra lỗi. Vui lòng thử lại sau.', false);
            }
        });
    });
    
    // Tìm kiếm câu hỏi từ database
    $('#searchAiBtn').on('click', function() {
        let keyword = $('#searchAiQuestion').val().trim();
        if (!keyword) return;
        
        $.ajax({
            url: '/api/ai-training/search',
            method: 'GET',
            data: {
                keyword: keyword,
                limit: 5
            },
            success: function(response) {
                $('#searchResults').empty().show();
                
                if (response.success && response.questions.length > 0) {
                    let html = `
                        <div class="card">
                            <div class="card-header py-2">
                                <h6 class="mb-0"><i class="fas fa-search mr-1"></i>Kết quả tìm kiếm</h6>
                            </div>
                            <div class="card-body p-2">
                    `;
                    
                    response.questions.forEach(function(question) {
                        html += `
                            <div class="search-result-item mb-2 p-2 border rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="d-block">${question.question}</strong>
                                        <small class="text-muted">${question.answer.substring(0, 100)}...</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary use-answer-btn"
                                            data-answer="${question.answer}">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                ${question.category ? 
                                    `<span class="badge badge-sm badge-info mt-1">${question.category}</span>` : ''}
                            </div>
                        `;
                    });
                    
                    html += `
                            </div>
                        </div>
                    `;
                    
                    $('#searchResults').html(html);
                    
                    // Thêm event cho nút sử dụng câu trả lời
                    $('.use-answer-btn').on('click', function() {
                        let answer = $(this).data('answer');
                        $('#aiMessageInput').val(answer);
                        $('#sendAiMessage').click();
                    });
                } else {
                    $('#searchResults').html(`
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="fas fa-info-circle mr-1"></i>
                            Không tìm thấy kết quả phù hợp
                        </div>
                    `);
                }
            }
        });
    });
    
    // Enter để gửi tin nhắn
    $('#aiMessageInput').on('keypress', function(e) {
        if (e.which === 13) {
            $('#sendAiMessage').click();
        }
    });
    
    // Xóa chat
    $('#clearAiChat').on('click', function() {
        if (confirm('Bạn có chắc muốn xóa toàn bộ cuộc trò chuyện?')) {
            $('#aiChatMessages').html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-robot fa-2x mb-2"></i>
                    <p>Xin chào! Tôi là trợ lý AI của cửa hàng. Tôi có thể giúp gì cho bạn?</p>
                </div>
            `);
            aiHistory = [];
            aiSessionId = 'ai_chat_' + Date.now();
        }
    });
    
    // Xuất chat
    $('#exportAiChat').on('click', function() {
        if (aiHistory.length === 0) {
            alert('Không có nội dung chat để xuất');
            return;
        }
        
        let chatText = "=== LỊCH SỬ CHAT TRỢ LÝ AI ===\n\n";
        aiHistory.forEach(function(msg) {
            let sender = msg.isUser ? "Bạn" : "Trợ lý AI";
            let time = new Date(msg.timestamp).toLocaleString('vi-VN');
            chatText += `[${time}] ${sender}: ${msg.message}\n`;
        });
        
        let blob = new Blob([chatText], { type: 'text/plain' });
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = `ai_chat_${new Date().toISOString().split('T')[0]}.txt`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    });
});
</script>


@endsection