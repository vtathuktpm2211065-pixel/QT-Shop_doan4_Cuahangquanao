@extends('app')

@section('content')
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
                                📨 Xem phản hồi của admin
                            </button>
                        @else
                            <div class="alert alert-secondary">⏳ Chưa có phản hồi từ cửa hàng.</div>
                        @endif

                        {{-- Nút xoá --}}
                        <form action="{{ route('support.delete', $supportRequest->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xoá yêu cầu này không?')" class="mb-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">🗑️ Xoá yêu cầu</button>
                        </form>

                        {{-- Nút gửi mới --}}
                        <a href="{{ route('support.form') }}" class="btn btn-success">
                            📨 Gửi yêu cầu hỗ trợ mới
                        </a>
                    </div>
                </div>

                {{-- Modal phản hồi --}}
                <div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="chatModalLabel">💬 Phản hồi hỗ trợ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body" id="chatModalBody">
                                @foreach($supportRequest->replies as $reply)
                                    <div class="mb-3 p-2 rounded {{ $reply->user_id ? 'bg-light text-end' : 'bg-secondary text-white' }}" data-message-id="{{ $reply->id }}">
                                        <div><strong>{{ $reply->user_id ? ($reply->user->name ?? 'Người dùng') : 'Admin' }}</strong></div>
                                        <div>{{ $reply->reply }}</div>
                                        <small class="text-muted">🕒 {{ $reply->created_at->format('H:i d/m/Y') }}</small>
                                    </div>
                                @endforeach

                                {{-- Form phản hồi --}}
                                <form action="{{ route('support.reply', $supportRequest->id) }}" method="POST" id="modalReplyForm">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="reply" class="form-control" rows="2" placeholder="Nhập nội dung phản hồi..." id="modalMessageInput" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="modalSendButton">📤 Gửi phản hồi</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                .message-animation {
                    animation: messageSlideIn 0.3s ease-out;
                }

                @keyframes messageSlideIn {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .alert-position {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    min-width: 300px;
                }
                </style>

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
                            if (!message) return;
                            
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
                                    
                                    // Cập nhật last message id
                                    this.lastMessageId = result.reply.id;
                                    
                                    // Hiển thị thông báo
                                    this.showSuccess(result.message || 'Đã gửi phản hồi thành công!');
                                    
                                    // Cuộn xuống dưới
                                    this.scrollToBottom();
                                    
                                    // Cập nhật badge unread
                                    this.updateUnreadBadge();
                                }
                            } catch (error) {
                                console.error('Error:', error);
                                this.showError('Có lỗi khi gửi phản hồi');
                            } finally {
                                this.isSubmitting = false;
                                sendButton.disabled = false;
                                sendButton.innerHTML = '📤 Gửi phản hồi';
                            }
                        });
                    }

                  // Trong file support/index.blade.php - sửa hàm addMessageToChat
                    addMessageToChat(message) {
                        const chatContainer = document.getElementById('chatModalBody');
                        if (!chatContainer) {
                            console.error('Chat container not found');
                            return;
                        }

                        const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
                        if (existingMessage) return;
                        
                        const isAdminMessage = message.is_admin;
                        const isCurrentUser = {{ Auth::check() ? 'true' : 'false' }} && message.user_id === {{ Auth::id() ?? 'null' }};
                        
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `mb-3 p-2 rounded ${isAdminMessage ? 'bg-secondary text-white' : 'bg-light text-end'} message-animation`;
                        messageDiv.setAttribute('data-message-id', message.id);
                        
                        const senderName = isAdminMessage ? 'Admin' : (message.name || 'Bạn');
                        
                        messageDiv.innerHTML = `
                            <div><strong>${this.escapeHtml(senderName)}</strong></div>
                            <div>${this.escapeHtml(message.reply)}</div>
                            <small class="text-muted">🕒 ${new Date(message.created_at).toLocaleString('vi-VN')}</small>
                        `;
                        
                        // Tìm form reply
                        const form = document.getElementById('modalReplyForm');
                        if (form) {
                            chatContainer.insertBefore(messageDiv, form);
                        } else {
                            // Nếu không tìm thấy form, thêm vào cuối container
                            chatContainer.appendChild(messageDiv);
                        }
                        
                        console.log('Message added to chat:', message.id);
                    }

                    // Thêm hàm escape HTML
                    escapeHtml(unsafe) {
                        if (!unsafe) return '';
                        return unsafe
                            .replace(/&/g, "&amp;")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;")
                            .replace(/"/g, "&quot;")
                            .replace(/'/g, "&#039;");
                    }

                    startPolling() {
                        // Kiểm tra tin nhắn mới mỗi 3 giây
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
                                
                                // Hiển thị thông báo có tin nhắn mới
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
                        }
                    }

                    updateUnreadBadge() {
                        // Ẩn badge unread khi user đã gửi tin nhắn
                        const unreadIndicator = document.getElementById('unread-indicator');
                        if (unreadIndicator) {
                            unreadIndicator.style.display = 'none';
                        }
                    }

                    showSuccess(message) {
                        this.showAlert(message, 'success');
                    }

                    showError(message) {
                        this.showAlert(message, 'danger');
                    }

                    showNewMessageNotification(count) {
                        if (count > 0) {
                            this.showAlert(`Có ${count} tin nhắn mới từ admin`, 'info', 2000);
                        }
                    }

                    showAlert(message, type = 'success', duration = 3000) {
                        // Kiểm tra nếu đã có alert cùng loại thì xóa
                        const existingAlert = document.querySelector('.alert-position');
                        if (existingAlert) {
                            existingAlert.remove();
                        }
                        
                        const alertDiv = document.createElement('div');
                        alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-position`;
                        alertDiv.innerHTML = `
                            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'danger' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i> 
                            ${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        
                        document.body.appendChild(alertDiv);
                        
                        // Tự động ẩn sau duration
                        setTimeout(() => {
                            if (alertDiv.parentElement) {
                                alertDiv.remove();
                            }
                        }, duration);
                    }

                    destroy() {
                        // Dọn dẹp khi modal đóng
                        if (this.pollingInterval) {
                            clearInterval(this.pollingInterval);
                        }
                    }
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
                    
                    // Thêm sự kiện Enter để gửi tin nhắn
                    document.addEventListener('keypress', function(e) {
                        const messageInput = document.getElementById('modalMessageInput');
                        if (e.key === 'Enter' && messageInput && document.activeElement === messageInput) {
                            e.preventDefault();
                            document.getElementById('modalReplyForm').dispatchEvent(new Event('submit'));
                        }
                    });
                });
                </script>
            @else
                <div class="alert alert-info text-center">
                    Bạn chưa gửi yêu cầu hỗ trợ nào.
                </div>
                <div class="text-center">
                    <a href="{{ route('support.form') }}" class="btn btn-success mt-3">
                        📨 Gửi yêu cầu hỗ trợ mới
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Scripts --}}
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
