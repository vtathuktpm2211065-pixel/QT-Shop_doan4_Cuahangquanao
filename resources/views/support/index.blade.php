@extends('app')

@section('content')

<style>
.chat-modal-container {
    height: 500px;
    display: flex;
    flex-direction: column;
}

.chat-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0;
}

.chat-modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background-color: #f8f9fa;
}

.message-modal {
    padding: 12px 16px;
    border-radius: 18px;
    margin-bottom: 15px;
    max-width: 80%;
    word-wrap: break-word;
    position: relative;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    animation: fadeInMessage 0.3s ease-in;
}

@keyframes fadeInMessage {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Tin nhắn user (bên phải) */
.message-modal.user {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: #fff;
    margin-left: auto;
    border-bottom-right-radius: 5px;
}

/* Tin nhắn admin (bên trái) */
.message-modal.admin {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    color: #333;
    border: 1px solid #e9ecef;
    margin-right: auto;
    border-bottom-left-radius: 5px;
}

.sender-name-modal {
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.message-modal.user .sender-name-modal {
    color: rgba(255,255,255,0.9);
}

.message-modal.admin .sender-name-modal {
    color: #6c757d;
}

.message-time-modal {
    font-size: 0.7rem;
    margin-top: 8px;
    opacity: 0.8;
}

.message-content-modal {
    line-height: 1.4;
}

.status-badge-modal {
    font-size: 0.65rem;
    padding: 2px 6px;
    border-radius: 8px;
}

.attachment-preview-modal {
    margin-top: 8px;
    padding: 8px;
    background: rgba(255,255,255,0.2);
    border-radius: 8px;
    font-size: 0.8rem;
}

.attachment-preview-modal img {
    max-width: 200px;
    border-radius: 8px;
    cursor: pointer;
}

.file-attachment-modal {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    background: rgba(255,255,255,0.9);
    border-radius: 4px;
    color: #333;
    text-decoration: none;
    margin-top: 4px;
    font-size: 0.8rem;
}

.file-attachment-modal:hover {
    background: white;
}

.modal-support-alert {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.request-info-modal {
    background: #e9ecef;
    padding: 12px 20px;
    border-bottom: 1px solid #dee2e6;
}

.info-item-modal {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
}

.scroll-to-bottom-modal {
    position: absolute;
    bottom: 80px;
    right: 30px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    z-index: 1000;
}
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @if($supportRequest)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📩 Yêu cầu hỗ trợ của bạn</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>👤 Họ tên:</strong> {{ $supportRequest->name }}</p>
                        <p><strong>📧 Email:</strong> {{ $supportRequest->email }}</p>
                        <p><strong>📱 Số điện thoại:</strong> {{ $supportRequest->phone ?? 'Không có' }}</p>
                        <p><strong>📝 Nội dung:</strong> {{ $supportRequest->message }}</p>
                        <p class="text-muted mb-3"><strong>🕒 Gửi lúc:</strong> {{ $supportRequest->created_at->format('H:i d/m/Y') }}</p>

                        {{-- Nút xem phản hồi --}}
                        @if($supportRequest->replies->count() > 0)
                            <button onclick="openChat()" class="btn btn-outline-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#chatModal">
                                <i class="fas fa-comments me-1"></i> Xem phản hồi ({{ $supportRequest->replies->count() }})
                            </button>
                        @else
                            <div class="alert alert-secondary">
                                <i class="fas fa-clock me-2"></i>Chưa có phản hồi từ cửa hàng.
                            </div>
                        @endif

                        {{-- Nút xoá --}}
                        <form action="{{ route('support.delete', $supportRequest->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xoá yêu cầu này không? Tất cả tin nhắn liên quan sẽ bị xóa.')" class="mb-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash me-1"></i> Xoá yêu cầu
                            </button>
                        </form>

                        {{-- Nút gửi mới --}}
                        <a href="{{ route('support.form') }}" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i> Gửi yêu cầu mới
                        </a>
                    </div>
                </div>

                {{-- Modal phản hồi được cải tiến --}}
                <div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <!-- Header -->
                            <div class="modal-header chat-modal-header">
                                <div class="w-100">
                                    <h5 class="modal-title mb-2" id="chatModalLabel">
                                        <i class="fas fa-comments me-2"></i>Chat Hỗ Trợ
                                    </h5>
                                    <div class="d-flex flex-wrap align-items-center gap-3">
                                        <div class="customer-info">
                                            <small class="d-flex align-items-center">
                                                <i class="fas fa-user-circle me-2"></i>
                                                <strong>{{ $supportRequest->name }}</strong>
                                            </small>
                                            <small class="d-flex align-items-center mt-1">
                                                <i class="fas fa-envelope me-2"></i>
                                                {{ $supportRequest->email }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $supportRequest->status == 'resolved' ? 'success' : ($supportRequest->status == 'processing' ? 'warning' : 'secondary') }}">
                                            {{ $supportRequest->status == 'resolved' ? '✅ Đã giải quyết' : ($supportRequest->status == 'processing' ? '🔄 Đang xử lý' : '⏳ Chờ xử lý') }}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>

                            <!-- Thông tin yêu cầu -->
                            <div class="request-info-modal">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item-modal">
                                            <i class="fas fa-calendar text-primary"></i>
                                            <span>Ngày gửi: {{ $supportRequest->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item-modal">
                                            <i class="fas fa-comment-dots text-primary"></i>
                                            <span>{{ $supportRequest->replies->count() }} phản hồi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Chat Body -->
                            <div class="modal-body p-0">
                                <div class="chat-modal-container">
                                    <div class="chat-modal-body" id="chatModalBody">
                                        <!-- Tin nhắn gốc từ user -->
                                        <div class="message-modal user">
                                            <div class="sender-name-modal">
                                                {{ $supportRequest->name }}
                                                <span class="badge bg-light text-dark status-badge-modal">Yêu cầu ban đầu</span>
                                            </div>
                                            <div class="message-content-modal">{{ $supportRequest->message }}</div>
                                            @if($supportRequest->attachment)
                                            <div class="attachment-preview-modal">
                                                <strong><i class="fas fa-paperclip"></i> File đính kèm:</strong>
                                                @if(in_array(pathinfo($supportRequest->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                                    <img src="{{ Storage::url($supportRequest->attachment) }}" 
                                                         alt="Attachment" 
                                                         onclick="openImageModal('{{ Storage::url($supportRequest->attachment) }}')">
                                                @else
                                                    <a href="{{ Storage::url($supportRequest->attachment) }}" 
                                                       target="_blank" 
                                                       class="file-attachment-modal">
                                                        <i class="fas fa-download me-2"></i>
                                                        {{ basename($supportRequest->attachment) }}
                                                    </a>
                                                @endif
                                            </div>
                                            @endif
                                            <div class="message-time-modal">{{ $supportRequest->created_at->format('H:i d/m/Y') }}</div>
                                        </div>

                                        <!-- Các phản hồi -->
                                        @foreach($supportRequest->replies->sortBy('created_at') as $reply)
                                            @php
                                                $isAdminMessage = $reply->is_admin;
                                                $senderName = $isAdminMessage ? ($reply->user ? $reply->user->name : 'Hỗ trợ viên') : $reply->name;
                                            @endphp

                                            <div class="message-modal {{ $isAdminMessage ? 'admin' : 'user' }}" data-message-id="{{ $reply->id }}">
                                                <div class="sender-name-modal">
                                                    {{ $senderName }}
                                                    @if($isAdminMessage)
                                                        <span class="badge bg-secondary status-badge-modal">Hỗ trợ viên</span>
                                                    @else
                                                        <span class="badge bg-success status-badge-modal">Bạn</span>
                                                    @endif
                                                </div>
                                                <div class="message-content-modal">{!! nl2br(e($reply->reply)) !!}</div>
                                                
                                                @if($reply->attachment)
                                                <div class="attachment-preview-modal">
                                                    <strong><i class="fas fa-paperclip"></i> File đính kèm:</strong>
                                                    @if(in_array(pathinfo($reply->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                                        <img src="{{ Storage::url($reply->attachment) }}" 
                                                             alt="Attachment" 
                                                             onclick="openImageModal('{{ Storage::url($reply->attachment) }}')">
                                                    @else
                                                        <a href="{{ Storage::url($reply->attachment) }}" 
                                                           target="_blank" 
                                                           class="file-attachment-modal">
                                                            <i class="fas fa-download me-2"></i>
                                                            {{ basename($reply->attachment) }}
                                                        </a>
                                                    @endif
                                                </div>
                                                @endif
                                                
                                                <div class="message-time-modal">
                                                    {{ $reply->created_at->format('H:i d/m/Y') }}
                                                    @if($reply->is_read && !$isAdminMessage)
                                                        <span class="ms-2"><i class="fas fa-check-double text-success"></i> Đã xem</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Scroll to bottom button -->
                            <button class="scroll-to-bottom-modal" id="scrollToBottomModal" onclick="scrollToBottomModal()">
                                <i class="fas fa-arrow-down"></i>
                            </button>

                            <!-- Form phản hồi -->
                            <div class="modal-footer bg-light">
                                <form action="{{ route('support.reply', $supportRequest->id) }}" method="POST" enctype="multipart/form-data" id="modalReplyForm" class="w-100">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="reply" class="form-control" rows="2" placeholder="Nhập tin nhắn của bạn..." id="modalMessageInput" required></textarea>
                                        <div class="form-text">
                                            <span id="modalCharCount">0</span>/1000 ký tự
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <input type="file" name="attachment" id="modalAttachment" 
                                                   class="form-control form-control-sm" 
                                                   accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
                                                   style="display: none;">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('modalAttachment').click()">
                                                <i class="fas fa-paperclip"></i> Đính kèm
                                            </button>
                                            <span id="modalFileName" class="ms-2 small text-muted"></span>
                                        </div>
                                        
                                        <div>
                                            <button type="submit" class="btn btn-primary" id="modalSendButton">
                                                <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Modal -->
                <div class="modal fade" id="imageModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Xem hình ảnh</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="" id="modalImage" style="max-width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
<!-- Thêm highlight.js trước khi gọi hljs -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>



                <script>
                class ModalSupportChat {
                    constructor(supportRequestId) {
                        this.supportRequestId = supportRequestId;
                        this.lastMessageId = {{ $supportRequest->replies->max('id') ?? 0 }};
                        this.isSubmitting = false;
                        this.pollingInterval = null;
                        this.init();
                    }

                    init() {
                        this.setupFormSubmit();
                        this.startPolling();
                        this.scrollToBottom();
                        this.setupCharCounter();
                        this.setupFileInput();
                        this.setupScrollListener();
                    }

                    setupFormSubmit() {
                        const form = document.getElementById('modalReplyForm');
                        const messageInput = document.getElementById('modalMessageInput');
                        const sendButton = document.getElementById('modalSendButton');
                        
                        if (!form) return;
                        
                        form.addEventListener('submit', async (e) => {
                            e.preventDefault();
                            
                            if (this.isSubmitting) return;
                            
                            const message = messageInput.value.trim();
                            if (!message) {
                                this.showAlert('Vui lòng nhập tin nhắn', 'warning');
                                return;
                            }
                            
                            if (message.length > 1000) {
                                this.showAlert('Tin nhắn không được vượt quá 1000 ký tự', 'warning');
                                return;
                            }
                            
                            this.isSubmitting = true;
                            sendButton.disabled = true;
                            sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
                            
                            try {
                                const formData = new FormData(form);
                                
                                const response = await fetch(form.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                                
                                const result = await response.json();
                                
                                if (result.success) {
                                    // Thêm tin nhắn mới vào chat
                                    this.addMessageToChat(result.reply);
                                    
                                    // Reset form
                                    messageInput.value = '';
                                    document.getElementById('modalCharCount').textContent = '0';
                                    document.getElementById('modalFileName').textContent = '';
                                    
                                    // Cập nhật last message id
                                    this.lastMessageId = result.reply.id;
                                    
                                    // Hiển thị thông báo
                                    this.showAlert(result.message || 'Đã gửi tin nhắn thành công!', 'success');
                                    
                                    // Cuộn xuống dưới
                                    this.scrollToBottom();
                                } else {
                                    throw new Error(result.message || 'Có lỗi xảy ra');
                                }
                            } catch (error) {
                                console.error('Error:', error);
                                this.showAlert('Có lỗi khi gửi tin nhắn', 'danger');
                            } finally {
                                this.isSubmitting = false;
                                sendButton.disabled = false;
                                sendButton.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi tin nhắn';
                            }
                        });
                    }

                    addMessageToChat(message) {
                        const chatContainer = document.getElementById('chatModalBody');
                        if (!chatContainer) return;

                        const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
                        if (existingMessage) return;
                        
                        const isAdminMessage = message.is_admin;
                        const senderName = isAdminMessage ? 'Hỗ trợ viên' : (message.name || 'Bạn');
                        
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `message-modal ${isAdminMessage ? 'admin' : 'user'}`;
                        messageDiv.setAttribute('data-message-id', message.id);
                        
                        let attachmentHtml = '';
                        if (message.attachment) {
                            const fileExt = message.attachment.split('.').pop().toLowerCase();
                            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                                attachmentHtml = `
                                    <div class="attachment-preview-modal">
                                        <strong><i class="fas fa-paperclip"></i> File đính kèm:</strong>
                                        <img src="/storage/${message.attachment}" 
                                             alt="Attachment" 
                                             onclick="openImageModal('/storage/${message.attachment}')">
                                    </div>
                                `;
                            } else {
                                attachmentHtml = `
                                    <div class="attachment-preview-modal">
                                        <strong><i class="fas fa-paperclip"></i> File đính kèm:</strong>
                                        <a href="/storage/${message.attachment}" 
                                           target="_blank" 
                                           class="file-attachment-modal">
                                            <i class="fas fa-download me-2"></i>
                                            ${message.attachment.split('/').pop()}
                                        </a>
                                    </div>
                                `;
                            }
                        }
                        
                        messageDiv.innerHTML = `
                            <div class="sender-name-modal">
                                ${this.escapeHtml(senderName)}
                                ${isAdminMessage ? '<span class="badge bg-secondary status-badge-modal">Hỗ trợ viên</span>' : '<span class="badge bg-success status-badge-modal">Bạn</span>'}
                            </div>
                            <div class="message-content-modal">${this.escapeHtml(message.reply)}</div>
                            ${attachmentHtml}
                            <div class="message-time-modal">
                                ${new Date(message.created_at).toLocaleString('vi-VN')}
                            </div>
                        `;
                        
                        chatContainer.appendChild(messageDiv);
                    }

                    startPolling() {
                        this.pollingInterval = setInterval(() => {
                            this.checkNewMessages();
                        }, 3000);
                    }

                    async checkNewMessages() {
                        try {
                            const response = await fetch(`/support/${this.supportRequestId}/messages?last_message_id=${this.lastMessageId}`);
                            const data = await response.json();
                            
                            if (data.messages && data.messages.length > 0) {
                                data.messages.forEach(message => {
                                    this.addMessageToChat(message);
                                });
                                this.lastMessageId = data.last_message_id;
                                this.scrollToBottom();
                                
                                if (data.messages.length > 0) {
                                    this.showNewMessageNotification(data.messages.length);
                                }
                            }
                        } catch (error) {
                            console.error('Error checking messages:', error);
                        }
                    }

                    scrollToBottom() {
                        const chatContainer = document.getElementById('chatModalBody');
                        if (chatContainer) {
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                            this.hideScrollButton();
                        }
                    }

                    setupCharCounter() {
                        const messageInput = document.getElementById('modalMessageInput');
                        const charCount = document.getElementById('modalCharCount');
                        
                        if (messageInput && charCount) {
                            messageInput.addEventListener('input', function() {
                                const length = this.value.length;
                                charCount.textContent = length;
                                charCount.className = length > 1000 ? 'text-danger' : 'text-muted';
                            });
                            charCount.textContent = messageInput.value.length;
                        }
                    }

                    setupFileInput() {
                        const attachmentInput = document.getElementById('modalAttachment');
                        const fileName = document.getElementById('modalFileName');
                        
                        if (attachmentInput && fileName) {
                            attachmentInput.addEventListener('change', function() {
                                if (this.files.length > 0) {
                                    fileName.textContent = this.files[0].name;
                                    fileName.className = 'ms-2 small text-success';
                                } else {
                                    fileName.textContent = '';
                                }
                            });
                        }
                    }

                    setupScrollListener() {
                        const chatContainer = document.getElementById('chatModalBody');
                        const scrollButton = document.getElementById('scrollToBottomModal');
                        
                        if (chatContainer && scrollButton) {
                            chatContainer.addEventListener('scroll', () => {
                                const isAtBottom = chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight < 100;
                                scrollButton.style.display = isAtBottom ? 'none' : 'flex';
                            });
                        }
                    }

                    hideScrollButton() {
                        const scrollButton = document.getElementById('scrollToBottomModal');
                        if (scrollButton) {
                            scrollButton.style.display = 'none';
                        }
                    }

                    showNewMessageNotification(count) {
                        if (count > 0) {
                            this.showAlert(`Có ${count} tin nhắn mới từ hỗ trợ viên`, 'info', 2000);
                        }
                    }

                    showAlert(message, type = 'success', duration = 3000) {
                        const existingAlert = document.querySelector('.modal-support-alert');
                        if (existingAlert) {
                            existingAlert.remove();
                        }
                        
                        const alertDiv = document.createElement('div');
                        alertDiv.className = `alert alert-${type} alert-dismissible fade show modal-support-alert position-fixed`;
                        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                        
                        const icon = type === 'success' ? 'fa-check-circle' : 
                                    type === 'danger' ? 'fa-exclamation-circle' : 
                                    type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
                        
                        alertDiv.innerHTML = `
                            <i class="fas ${icon}"></i> 
                            ${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        
                        document.body.appendChild(alertDiv);
                        
                        setTimeout(() => {
                            if (alertDiv.parentElement) {
                                alertDiv.remove();
                            }
                        }, duration);
                    }

                    escapeHtml(unsafe) {
                        if (!unsafe) return '';
                        return unsafe
                            .replace(/&/g, "&amp;")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;")
                            .replace(/"/g, "&quot;")
                            .replace(/'/g, "&#039;");
                    }

                    destroy() {
                        if (this.pollingInterval) {
                            clearInterval(this.pollingInterval);
                        }
                    }
                }

                // Utility functions
                function scrollToBottomModal() {
                    const chatContainer = document.getElementById('chatModalBody');
                    if (chatContainer) {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                }

                function openImageModal(src) {
                    document.getElementById('modalImage').src = src;
                    new bootstrap.Modal(document.getElementById('imageModal')).show();
                }

                // Khởi tạo khi modal được mở
                document.addEventListener('DOMContentLoaded', function() {
                    let modalChat = null;
                    
                    const chatModal = document.getElementById('chatModal');
                    if (chatModal) {
                        chatModal.addEventListener('show.bs.modal', function () {
                            @if(isset($supportRequest) && $supportRequest)
                                modalChat = new ModalSupportChat({{ $supportRequest->id }});
                                
                                // Focus vào input khi modal mở
                                const messageInput = document.getElementById('modalMessageInput');
                                if (messageInput) {
                                    setTimeout(() => {
                                        messageInput.focus();
                                    }, 500);
                                }
                            @endif
                        });
                        
                        chatModal.addEventListener('hide.bs.modal', function () {
                            if (modalChat) {
                                modalChat.destroy();
                                modalChat = null;
                            }
                        });
                    }
                    
                    // Thêm sự kiện Enter để gửi tin nhắn (Shift+Enter để xuống dòng)
                    document.addEventListener('keydown', function(e) {
                        const messageInput = document.getElementById('modalMessageInput');
                        if (e.key === 'Enter' && messageInput && document.activeElement === messageInput) {
                            if (!e.shiftKey) {
                                e.preventDefault();
                                document.getElementById('modalReplyForm').dispatchEvent(new Event('submit'));
                            }
                        }
                    });
                });
                </script>

            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>Bạn chưa gửi yêu cầu hỗ trợ nào.
                </div>
                <div class="text-center">
                    <a href="{{ route('support.form') }}" class="btn btn-success mt-3">
                        <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu hỗ trợ mới
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
function checkUnreadSupport() {
    fetch('{{ route("support.unread.check") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('unread-indicator').style.display = data.has_unread ? 'inline' : 'none';
        });
}

setInterval(checkUnreadSupport, 10000);
window.onload = checkUnreadSupport;

function openChat() {
    const requestId = {{ $supportRequest->id ?? 'null' }};
    if (requestId) {
        fetch(`/support/${requestId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('unread-indicator').style.display = 'none';
            }
        });
    }
}
</script>
@endsection
