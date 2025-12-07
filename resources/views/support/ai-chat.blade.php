{{-- @extends('app')

@section('content')
<style>
.ai-chat-container {
    max-width: 800px;
    margin: 0 auto;
    height: 80vh;
    display: flex;
    flex-direction: column;
}

.ai-chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
    border-radius: 15px 15px 0 0;
}

.ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
}

.message {
    margin-bottom: 15px;
    padding: 12px 16px;
    border-radius: 18px;
    max-width: 80%;
    animation: fadeIn 0.3s ease-in;
}

.message.user {
    background: #007bff;
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 5px;
}

.message.ai {
    background: white;
    border: 1px solid #e9ecef;
    margin-right: auto;
    border-bottom-left-radius: 5px;
}

.quick-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.quick-btn {
    padding: 8px 12px;
    border: 1px solid #007bff;
    background: white;
    color: #007bff;
    border-radius: 20px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s;
}

.quick-btn:hover {
    background: #007bff;
    color: white;
}

.typing-indicator {
    display: inline-flex;
    align-items: center;
    color: #6c757d;
    font-style: italic;
}

.typing-dots {
    display: inline-flex;
    margin-left: 5px;
}

.typing-dots span {
    animation: typing 1.4s infinite;
    margin: 0 1px;
}

.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { opacity: 0.3; }
    30% { opacity: 1; }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message-time {
    font-size: 0.7rem;
    opacity: 0.7;
    margin-top: 5px;
}

.ai-response {
    line-height: 1.5;
}

.welcome-message {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border-radius: 15px;
    margin-bottom: 20px;
}

.empty-chat {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.order-result {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    border: 1px solid #bbdefb;
    border-radius: 10px;
    padding: 15px;
    margin: 10px 0;
}

.order-header {
    background: linear-gradient(135deg, #2196f3 0%, #673ab7 100%);
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.order-detail {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 8px;
}

.order-detail .label {
    font-weight: bold;
    color: #555;
}

.order-detail .value {
    color: #333;
}

.order-status {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-confirmed { background: #d1ecf1; color: #0c5460; }
.status-processing { background: #cce7ff; color: #004085; }
.status-shipped { background: #d4edda; color: #155724; }
.status-delivered { background: #d1edff; color: #0c5460; }
.status-cancelled { background: #f8d7da; color: #721c24; }

.file-attachment-container {
    border: 2px dashed #007bff;
    border-radius: 10px;
    padding: 10px;
    margin: 10px 0;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    display: none; /* Ẩn mặc định */
}

.file-attachment-container.dragover {
    border-color: #28a745;
    background: #e8f5e8;
}

.file-attachment-container.has-files {
    border-color: #28a745;
    background: #e8f5e8;
    padding: 10px;
    display: block; /* Hiển thị khi có file */
}

.file-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.file-preview-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    max-width: 200px;
}

.file-preview-item img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
}

.file-preview-info {
    flex: 1;
    min-width: 0;
}

.file-preview-name {
    font-size: 0.8rem;
    font-weight: bold;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-preview-size {
    font-size: 0.7rem;
    color: #6c757d;
}

.file-preview-remove {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    padding: 2px;
}

.attachment-preview {
    margin-top: 10px;
}

.attachment-item {
    display: inline-block;
    margin: 5px;
    vertical-align: top;
}

.attachment-image {
    max-width: 200px;
    max-height: 150px;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
}

.attachment-image:hover {
    transform: scale(1.05);
}

.attachment-file {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    background: #e9ecef;
    border-radius: 6px;
    text-decoration: none;
    color: #495057;
    gap: 8px;
}

.attachment-file:hover {
    background: #dee2e6;
    color: #495057;
    text-decoration: none;
}

.file-icon {
    font-size: 1.2rem;
}

.file-input-wrapper {
    position: relative;
    display: inline-block;
}

.file-input-wrapper input[type="file"] {
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.chat-message img { 
    max-width: 250px; 
    border-radius: 8px; 
}
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-robot"></i> Trợ lý AI hỗ trợ khách hàng</h5>
                    <a href="{{ route('support.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>

                <!-- Chat Messages Container -->
                <div class="card-body p-0">
                    <div class="ai-chat-messages" id="chatMessages">
                        @if(isset($conversations) && count($conversations) > 0)
                            @foreach($conversations as $chat)
                                <div class="message {{ $chat->is_user ? 'user' : 'ai' }}">
                                    <div class="ai-response">
                                        {!! nl2br(e($chat->message)) !!}
                                        @if($chat->attachments)
                                            <div class="attachment-preview mt-2">
                                                @foreach(json_decode($chat->attachments, true) as $file)
                                                    @if(str_contains($file['type'], 'image'))
                                                        <div class="attachment-item">
                                                            <img src="{{ asset('storage/' . $file['path']) }}" 
                                                                 alt="{{ $file['name'] }}" 
                                                                 class="attachment-image"
                                                                 onclick="openImageModal('{{ asset('storage/' . $file['path']) }}')">
                                                        </div>
                                                    @else
                                                        <div class="attachment-item">
                                                            <a href="{{ asset('storage/' . $file['path']) }}" 
                                                               target="_blank" 
                                                               class="attachment-file">
                                                                <span class="file-icon">
                                                                    @if(str_contains($file['type'], 'pdf'))
                                                                        <i class="fas fa-file-pdf text-danger"></i>
                                                                    @elseif(str_contains($file['type'], 'word') || str_contains($file['type'], 'document'))
                                                                        <i class="fas fa-file-word text-primary"></i>
                                                                    @elseif(str_contains($file['type'], 'video'))
                                                                        <i class="fas fa-video text-danger"></i>
                                                                    @else
                                                                        <i class="fas fa-file text-secondary"></i>
                                                                    @endif
                                                                </span>
                                                                <span>{{ $file['name'] }}</span>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="message-time">
                                        {{ $chat->created_at->format('H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="welcome-message">
                                <h4>Xin chào! 👋</h4>
                                <p>Tôi là trợ lý AI, có thể giúp gì cho bạn?</p>
                            </div>
                            <div class="empty-chat">
                                <p>Bắt đầu cuộc trò chuyện với Trợ lý AI</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Chat Input Form -->
                <div class="card-footer">
                    <form id="aiChatForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="session_id" id="sessionId" value="{{ session()->getId() }}">
                        
                        <!-- File Attachment Container (Ẩn mặc định) -->
                        <div class="file-attachment-container mb-3" id="fileDropZone">
                            <input type="file" name="attachments[]" id="fileInput" 
                                multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt" 
                                class="d-none">
                            
                            <!-- File Preview sẽ hiển thị ngay trong container này -->
                            <div class="file-preview" id="filePreview"></div>
                        </div>
                        
                        <!-- Message Input -->
                        <div class="mb-2">
                            <textarea name="message" class="form-control" rows="2" 
                                    placeholder="Nhập câu hỏi của bạn..." id="messageInput" required></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <!-- Đã xóa nút "Đính kèm file" -->
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="sendButton">
                                <i class="fas fa-paper-plane"></i> Gửi
                            </button>
                        </div>
                    </form>

                    <!-- Quick Action Buttons -->
                    <div class="quick-buttons mt-3">
                        <button type="button" class="quick-btn" onclick="quickAction('tra_cuu_don_hang')">
                            📦 Tra cứu đơn hàng
                        </button>
                        <button type="button" class="quick-btn" onclick="quickAction('tu_van_san_pham')">
                            🛍️ Tư vấn sản phẩm
                        </button>
                        <button type="button" class="quick-btn" onclick="quickAction('chinh_sach_doi_tra')">
                            🔄 Chính sách đổi trả
                        </button>
                        <button type="button" class="quick-btn" onclick="quickAction('gui_anh')">
                            📸 Gửi ảnh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem ảnh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
// ===================== FILE ATTACHMENT HANDLER =====================
class FileAttachmentHandler {
    constructor() {
        this.files = [];
        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        const fileInput = document.getElementById('fileInput');
        const fileDropZone = document.getElementById('fileDropZone');

        fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        
        fileDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileDropZone.classList.add('dragover');
        });

        fileDropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            fileDropZone.classList.remove('dragover');
        });

        fileDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            fileDropZone.classList.remove('dragover');
            this.handleFileSelect({ target: { files: e.dataTransfer.files } });
        });
    }

    handleFileSelect(event) {
        const newFiles = Array.from(event.target.files);
        for (const file of newFiles) {
            if (file.size > 5 * 1024 * 1024) {
                alert(`File "${file.name}" vượt quá 5MB.`);
                return;
            }
        }
        this.files = [...this.files, ...newFiles];
        this.updateFilePreview();
        event.target.value = '';
    }

    updateFilePreview() {
        const previewContainer = document.getElementById('filePreview');
        const fileDropZone = document.getElementById('fileDropZone');

        previewContainer.innerHTML = '';
        
        if (this.files.length > 0) {
            // Hiển thị container và file preview
            fileDropZone.classList.add('has-files');
            
            this.files.forEach((file, index) => {
                const previewItem = this.createFilePreview(file, index);
                previewContainer.appendChild(previewItem);
            });
        } else {
            // Ẩn container khi không có file
            fileDropZone.classList.remove('has-files');
        }
    }

    createFilePreview(file, index) {
        const previewItem = document.createElement('div');
        previewItem.className = 'file-preview-item';
        const icon = this.getFileIcon(file.type);
        const fileSize = this.formatFileSize(file.size);

        previewItem.innerHTML = `
            ${icon}
            <div class="file-preview-info">
                <div class="file-preview-name" title="${file.name}">${file.name}</div>
                <div class="file-preview-size">${fileSize}</div>
            </div>
            <button type="button" class="file-preview-remove" onclick="fileHandler.removeFile(${index})">
                <i class="fas fa-times"></i>
            </button>
        `;

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                previewItem.insertBefore(img, previewItem.firstChild);
            };
            reader.readAsDataURL(file);
        }
        return previewItem;
    }

    getFileIcon(fileType) {
        if (fileType.startsWith('image/')) return '<i class="fas fa-image text-primary"></i>';
        if (fileType.startsWith('video/')) return '<i class="fas fa-video text-danger"></i>';
        if (fileType === 'application/pdf') return '<i class="fas fa-file-pdf text-danger"></i>';
        if (fileType.includes('word') || fileType.includes('document')) return '<i class="fas fa-file-word text-primary"></i>';
        return '<i class="fas fa-file text-secondary"></i>';
    }

    formatFileSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return bytes + ' bytes';
    }

    removeFile(index) {
        this.files.splice(index, 1);
        this.updateFilePreview();
    }

    getFormData() {
        const formData = new FormData();
        this.files.forEach(file => formData.append('attachments[]', file));
        return formData;
    }

    clearFiles() {
        this.files = [];
        this.updateFilePreview();
    }

    getFilesCount() {
        return this.files.length;
    }
}

// ===================== AI CHAT HANDLER =====================
class AIChat {
    constructor() {
        this.sessionId = document.getElementById('sessionId').value;
        this.fileHandler = new FileAttachmentHandler();
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.scrollToBottom();
    }

    setupEventListeners() {
        const form = document.getElementById('aiChatForm');
        const messageInput = document.getElementById('messageInput');

        form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Enter để gửi tin nhắn, Shift+Enter để xuống dòng
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.handleSubmit(e);
            }
        });
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const message = formData.get('message').trim();
        
        if (!message && this.fileHandler.files.length === 0) {
            alert('Vui lòng nhập tin nhắn hoặc chọn file đính kèm');
            return;
        }

        // Add user message to chat
        this.addMessage(message, 'user');
        
        // Add file preview to chat if any
        if (this.fileHandler.files.length > 0) {
            this.addFilePreview(this.fileHandler.files);
        }

        form.reset();
        this.fileHandler.clearFiles();

        // Show typing indicator
        this.showTypingIndicator();

        try {
            // Combine form data with files
            const submitFormData = new FormData();
            submitFormData.append('message', message);
            submitFormData.append('session_id', this.sessionId);
            submitFormData.append('_token', '{{ csrf_token() }}');
            
            // Add files
            this.fileHandler.files.forEach(file => {
                submitFormData.append('attachments[]', file);
            });

            const response = await fetch('{{ route("ai.chat.send") }}', {
                method: 'POST',
                body: submitFormData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            this.hideTypingIndicator();

            if (data.success) {
                this.addAIMessage(data.response);
                if (data.session_id) {
                    this.sessionId = data.session_id;
                    document.getElementById('sessionId').value = data.session_id;
                }
            } else {
                this.addMessage('Có lỗi xảy ra: ' + (data.message || 'Vui lòng thử lại.'), 'ai', true);
            }
        } catch (error) {
            console.error('Chat error:', error);
            this.hideTypingIndicator();
            this.addMessage('Kết nối thất bại. Vui lòng kiểm tra internet và thử lại.', 'ai', true);
        }
    }

    addMessage(message, type, isError = false) {
        const messagesContainer = document.getElementById('chatMessages');
        
        // Xóa welcome message nếu đây là tin nhắn đầu tiên
        const welcomeMessage = messagesContainer.querySelector('.welcome-message');
        const emptyChat = messagesContainer.querySelector('.empty-chat');
        if (welcomeMessage) welcomeMessage.remove();
        if (emptyChat) emptyChat.remove();

        const messageDiv = document.createElement('div');
        
        messageDiv.className = `message ${type}`;
        if (isError) {
            messageDiv.style.background = '#f8d7da';
            messageDiv.style.borderColor = '#f5c6cb';
        }
        
        const time = new Date().toLocaleTimeString('vi-VN', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });

        messageDiv.innerHTML = `
            <div class="ai-response">${this.formatMessage(message)}</div>
            <div class="message-time">${time}</div>
        `;
        
        messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
    }

    addFilePreview(files) {
        const messagesContainer = document.getElementById('chatMessages');
        const filePreviewDiv = document.createElement('div');
        filePreviewDiv.className = 'message user attachment-preview';
        
        let previewContent = '<strong>📎 File đính kèm:</strong><br>';
        
        files.forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'attachment-image';
                    img.style.maxWidth = '150px';
                    img.style.margin = '5px';
                    filePreviewDiv.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else {
                const fileIcon = this.fileHandler.getFileIcon(file.type);
                previewContent += `<div class="attachment-item">${fileIcon} ${file.name}</div>`;
            }
        });
        
        filePreviewDiv.innerHTML += previewContent;
        messagesContainer.appendChild(filePreviewDiv);
        this.scrollToBottom();
    }

    addAIMessage(response) {
        if (typeof response === 'string') {
            this.addMessage(response, 'ai');
            return;
        }

        const messagesContainer = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        
        messageDiv.className = 'message ai';
        
        const time = new Date().toLocaleTimeString('vi-VN', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });

        let content = `<div class="ai-response">${this.formatResponse(response.message || response)}</div>`;
        
        // Hiển thị file đính kèm từ AI nếu có
        if (response.attachments && response.attachments.length > 0) {
            content += this.formatAIAttachments(response.attachments);
        }
        
        // Hiển thị chi tiết đơn hàng nếu có
        if (response.type === 'order_found' && response.data) {
            content += this.formatOrderDetails(response.data);
        }
        
        // Add buttons if available
        if (response.buttons && response.buttons.length > 0) {
            content += '<div class="quick-buttons mt-2">';
            response.buttons.forEach(button => {
                content += `<button class="quick-btn" onclick="handleAIAction('${button.type}', '${this.escapeHtml(button.text)}')">${button.text}</button>`;
            });
            content += '</div>';
        }
        
        content += `<div class="message-time">${time}</div>`;
        
        messageDiv.innerHTML = content;
        messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
    }

    formatAIAttachments(attachments) {
        let attachmentsHtml = '<div class="attachment-preview mt-2">';
        attachmentsHtml += '<strong>📎 File đã tải lên:</strong><br>';
        
        attachments.forEach(attachment => {
            if (attachment.type === 'image') {
                attachmentsHtml += `
                    <div class="attachment-item">
                        <img src="${attachment.url}" alt="${attachment.original_name}" 
                             class="attachment-image" onclick="openImageModal('${attachment.url}')">
                    </div>
                `;
            } else {
                const icon = attachment.type === 'video' ? '🎥' : 
                           attachment.type === 'document' ? '📄' : '📁';
                attachmentsHtml += `
                    <div class="attachment-item">
                        <a href="${attachment.url}" target="_blank" class="attachment-file">
                            <span class="file-icon">${icon}</span>
                            <span>${attachment.original_name}</span>
                        </a>
                    </div>
                `;
            }
        });
        
        attachmentsHtml += '</div>';
        return attachmentsHtml;
    }

    formatOrderDetails(orderData) {
        return `
            <div class="order-result mt-2">
                <div class="order-header">
                    <h6 class="mb-0">📦 Thông tin đơn hàng</h6>
                </div>
                <div class="order-detail">
                    <div class="label">Mã đơn hàng:</div>
                    <div class="value">${orderData.order_code || 'N/A'}</div>
                    
                    <div class="label">Trạng thái:</div>
                    <div class="value">
                        <span class="order-status status-${orderData.status || 'pending'}">
                            ${this.getStatusText(orderData.status)}
                        </span>
                    </div>
                    
                    <div class="label">Ngày đặt:</div>
                    <div class="value">${orderData.order_date || 'N/A'}</div>
                    
                    <div class="label">Tổng tiền:</div>
                    <div class="value">${orderData.total_amount ? this.formatCurrency(orderData.total_amount) : 'N/A'}</div>
                </div>
            </div>
        `;
    }

    getStatusText(status) {
        const statusMap = {
            'pending': 'Chờ xác nhận',
            'confirmed': 'Đã xác nhận',
            'processing': 'Đang xử lý',
            'shipped': 'Đang giao hàng',
            'delivered': 'Đã giao',
            'cancelled': 'Đã hủy'
        };
        return statusMap[status] || 'Chờ xác nhận';
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    formatResponse(text) {
        // Format bold text and line breaks
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    formatMessage(text) {
        // Basic formatting for user messages
        return text.replace(/\n/g, '<br>');
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showTypingIndicator() {
        const messagesContainer = document.getElementById('chatMessages');
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message ai typing-indicator';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="ai-response">
                Đang trả lời...
                <div class="typing-dots">
                    <span>.</span><span>.</span><span>.</span>
                </div>
            </div>
        `;
        messagesContainer.appendChild(typingDiv);
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById('chatMessages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

// Global instances
const fileHandler = new FileAttachmentHandler();
const aiChat = new AIChat();

// Quick action function
function quickAction(action) {
    const messages = {
        'tra_cuu_don_hang': 'Tôi muốn tra cứu đơn hàng',
        'tu_van_san_pham': 'Tôi cần tư vấn sản phẩm',
        'chinh_sach_doi_tra': 'Chính sách đổi trả như thế nào?',
        'gui_anh': 'Tôi muốn gửi ảnh để được tư vấn'
    };
    
    if (action === 'gui_anh') {
        document.getElementById('fileInput').click();
        document.getElementById('messageInput').placeholder = 'Nhập mô tả về ảnh (tùy chọn)...';
        document.getElementById('messageInput').focus();
    } else {
        document.getElementById('messageInput').value = messages[action] || action;
        // Auto submit the form
        document.getElementById('aiChatForm').dispatchEvent(new Event('submit'));
    }
}

// Handle AI action buttons
function handleAIAction(type, text) {
    document.getElementById('messageInput').value = text;
    document.getElementById('aiChatForm').dispatchEvent(new Event('submit'));
}

// Open image modal
function openImageModal(imageUrl) {
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageUrl;
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Any additional initialization code
});
</script>
@endsection --}}