@extends('layouts.admin')

@section('content')
<style>
  .chat-container {
    height: 70vh;
    display: flex;
    flex-direction: column;
  }

  .chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
    border-radius: 10px 10px 0 0;
  }

  .chat-box {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background-color: #f8f9fa;
    background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23e9ecef' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
  }

  .message {
    padding: 12px 16px;
    border-radius: 18px;
    margin-bottom: 15px;
    max-width: 70%;
    word-wrap: break-word;
    position: relative;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    animation: fadeIn 0.3s ease-in;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Tin nhắn khách hàng (bên trái) */
  .message.user {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    color: #333;
    border: 1px solid #e9ecef;
    margin-right: auto;
    border-bottom-left-radius: 5px;
  }

  /* Tin nhắn admin hoặc staff (bên phải) */
  .message.admin {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: #fff;
    margin-left: auto;
    border-bottom-right-radius: 5px;
  }

  .sender-name {
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 0.85rem;
  }

  .message.admin .sender-name {
    color: rgba(255,255,255,0.9);
  }

  .message.user .sender-name {
    color: #6c757d;
  }

  .message-time {
    font-size: 0.7rem;
    margin-top: 8px;
    opacity: 0.8;
  }

  .message-content {
    line-height: 1.4;
  }

  .attachment-preview {
    margin-top: 8px;
    padding: 8px;
    background: rgba(255,255,255,0.2);
    border-radius: 8px;
  }

  .attachment-preview img {
    max-width: 200px;
    border-radius: 8px;
    cursor: pointer;
  }

  .file-attachment {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background: rgba(255,255,255,0.9);
    border-radius: 6px;
    color: #333;
    text-decoration: none;
    margin-top: 5px;
  }

  .file-attachment:hover {
    background: white;
  }

  .status-badge {
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 10px;
    margin-left: 10px;
  }

  .chat-info {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
  }

  .typing-indicator {
    display: none;
    padding: 10px 16px;
    color: #6c757d;
    font-style: italic;
  }

  .scroll-to-bottom {
    position: fixed;
    bottom: 100px;
    right: 30px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    z-index: 1000;
  }
</style>

<div class="container-fluid py-4">
  <div class="row">
    <div class="col-md-9">
      <div class="card shadow">
        <!-- Header -->
        <div class="chat-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-1">💬 Trò chuyện với {{ $request->name }}</h5>
              <div class="d-flex align-items-center gap-3">
                <small>
                  <i class="fas fa-envelope"></i> {{ $request->email }}
                  @if($request->phone)
                    | <i class="fas fa-phone"></i> {{ $request->phone }}
                  @endif
                </small>
                <span class="badge bg-{{ $request->priority == 'high' ? 'danger' : ($request->priority == 'medium' ? 'warning' : 'success') }}">
                  {{ $request->priority == 'high' ? '🔴 Cao' : ($request->priority == 'medium' ? '🟡 Trung bình' : '🟢 Thấp') }}
                </span>
                <span class="badge bg-info">{{ ucfirst($request->type) }}</span>
              </div>
            </div>
            <div class="text-end">
              <span class="badge bg-{{ $request->status == 'resolved' ? 'success' : ($request->status == 'processing' ? 'warning' : 'secondary') }}">
                {{ $request->status == 'resolved' ? '✅ Đã giải quyết' : ($request->status == 'processing' ? '🔄 Đang xử lý' : '⏳ Chờ xử lý') }}
              </span>
              <div class="mt-2">
                <button class="btn btn-sm btn-outline-light me-2" onclick="updateStatus('processing')">
                  <i class="fas fa-play"></i> Bắt đầu xử lý
                </button>
                <button class="btn btn-sm btn-outline-light" onclick="updateStatus('resolved')">
                  <i class="fas fa-check"></i> Đánh dấu đã giải quyết
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Chat Info -->
        <div class="chat-info">
          <div class="row">
            <div class="col-md-6">
              <small><strong>Ngày gửi:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</small>
            </div>
            <div class="col-md-6">
              <small><strong>Tin nhắn cuối:</strong> 
                @if($request->replies->count() > 0)
                  {{ $request->replies->last()->created_at->diffForHumans() }}
                @else
                  Chưa có phản hồi
                @endif
              </small>
            </div>
          </div>
        </div>

        <!-- Chat Messages -->
        <div class="chat-container">
          <div class="chat-box" id="chatBox">
            <!-- Tin nhắn gốc từ người dùng -->
            <div class="message user">
              <div class="sender-name">
                {{ $request->name }}
                <span class="badge bg-primary status-badge">Yêu cầu ban đầu</span>
              </div>
              <div class="message-content">{{ $request->message }}</div>
              @if($request->attachment)
              <div class="attachment-preview">
                <strong><i class="fas fa-paperclip"></i> File đính kèm:</strong>
                @if(in_array(pathinfo($request->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                  <img src="{{ Storage::url($request->attachment) }}" 
                       alt="Attachment" 
                       onclick="openImageModal('{{ Storage::url($request->attachment) }}')"
                       class="mt-2">
                @else
                  <a href="{{ Storage::url($request->attachment) }}" 
                     target="_blank" 
                     class="file-attachment">
                    <i class="fas fa-download me-2"></i>
                    {{ basename($request->attachment) }}
                  </a>
                @endif
              </div>
              @endif
              <div class="message-time">{{ $request->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <!-- Các phản hồi -->
@foreach($request->replies->sortBy('created_at') as $reply)
    @php
        $isAdminMessage = $reply->is_admin; // ✅ Sử dụng trường is_admin để phân biệt
        $senderName = $isAdminMessage ? ($reply->user ? $reply->user->name : 'Admin') : $reply->name;
        $isCurrentUser = Auth::id() === $reply->user_id;
    @endphp

    <div class="message {{ $isAdminMessage ? 'admin' : 'user' }}">
        <div class="sender-name">
            {{ $senderName }}
            @if($isAdminMessage)
                <span class="badge bg-success status-badge">Nhân viên</span>
            @else
                <span class="badge bg-primary status-badge">Khách hàng</span>
            @endif
            @if($isCurrentUser)
                <span class="badge bg-info status-badge">Bạn</span>
            @endif
        </div>
        <div class="message-content">{!! nl2br(e($reply->reply)) !!}</div>
        
        @if($reply->attachment)
        <div class="attachment-preview">
            <strong><i class="fas fa-paperclip"></i> File đính kèm:</strong>
            @if(in_array(pathinfo($reply->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                <img src="{{ Storage::url($reply->attachment) }}" 
                     alt="Attachment" 
                     onclick="openImageModal('{{ Storage::url($reply->attachment) }}')"
                     class="mt-2">
            @else
                <a href="{{ Storage::url($reply->attachment) }}" 
                   target="_blank" 
                   class="file-attachment">
                    <i class="fas fa-download me-2"></i>
                    {{ basename($reply->attachment) }}
                </a>
            @endif
        </div>
        @endif
        
        <div class="message-time">
            {{ $reply->created_at->format('d/m/Y H:i') }}
            @if($reply->is_read && !$isAdminMessage)
                <span class="ms-2"><i class="fas fa-check-double text-success"></i> Đã xem</span>
            @endif
        </div>
    </div>
@endforeach

            <!-- Typing indicator -->
            <div class="typing-indicator" id="typingIndicator">
              <i class="fas fa-ellipsis-h"></i> Đang nhập...
            </div>
          </div>

          <!-- Form trả lời -->
          <div class="card-footer">
            <form action="{{ route('admin.support.reply', $request->id) }}" method="POST" enctype="multipart/form-data" id="replyForm">
              @csrf
              <div class="mb-3">
                <textarea name="reply" class="form-control" placeholder="Nhập tin nhắn của bạn..." 
                         rows="2" required id="messageInput"></textarea>
                <div class="form-text">
                  <span id="charCount">0</span>/1000 ký tự
                </div>
              </div>
              
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <input type="file" name="attachment" id="attachment" 
                         class="form-control form-control-sm" 
                         accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
                         style="display: none;">
                  <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('attachment').click()">
                    <i class="fas fa-paperclip"></i> Đính kèm file
                  </button>
                  <span id="fileName" class="ms-2 small text-muted"></span>
                </div>
                
                <div>
                  <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="insertQuickReply()">
                    <i class="fas fa-bolt"></i> Trả lời nhanh
                  </button>
                  <button type="submit" class="btn btn-primary" id="sendButton">
                    <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-3">
      <div class="card shadow">
        <div class="card-header bg-light">
          <h6 class="mb-0">📋 Thông tin yêu cầu</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <strong>Mã yêu cầu:</strong><br>
            <code>#{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</code>
          </div>
          
          <div class="mb-3">
            <strong>Trạng thái:</strong><br>
            <select class="form-select form-select-sm" id="statusSelect" onchange="updateStatus(this.value)">
              <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>⏳ Chờ xử lý</option>
              <option value="processing" {{ $request->status == 'processing' ? 'selected' : '' }}>🔄 Đang xử lý</option>
              <option value="resolved" {{ $request->status == 'resolved' ? 'selected' : '' }}>✅ Đã giải quyết</option>
            </select>
          </div>

          <div class="mb-3">
            <strong>Mức độ ưu tiên:</strong><br>
            <select class="form-select form-select-sm" id="prioritySelect" onchange="updatePriority(this.value)">
              <option value="low" {{ $request->priority == 'low' ? 'selected' : '' }}>🟢 Thấp</option>
              <option value="medium" {{ $request->priority == 'medium' ? 'selected' : '' }}>🟡 Trung bình</option>
              <option value="high" {{ $request->priority == 'high' ? 'selected' : '' }}>🔴 Cao</option>
            </select>
          </div>

          <div class="mb-3">
            <strong>Thống kê:</strong>
            <ul class="list-unstyled small mt-2">
              <li><i class="fas fa-comment me-2"></i> {{ $request->replies->count() }} phản hồi</li>
              <li><i class="fas fa-clock me-2"></i> 
                @if($request->replies->count() > 0)
                  {{ $request->created_at->diffInMinutes($request->replies->first()->created_at) }} phút phản hồi đầu
                @else
                  Chưa phản hồi
                @endif
              </li>
              <li><i class="fas fa-history me-2"></i> 
                {{ $request->created_at->diffForHumans() }}
              </li>
            </ul>
          </div>

          <hr>
          <div class="d-grid gap-2">
            <a href="mailto:{{ $request->email }}" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-envelope"></i> Gửi email
            </a>
            <a href="{{ route('admin.support.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
          </div>
        </div>
      </div>

      <!-- Trả lời nhanh -->
      <div class="card shadow mt-3">
        <div class="card-header bg-light">
          <h6 class="mb-0">⚡ Trả lời nhanh</h6>
        </div>
        <div class="card-body">
          @foreach([
            'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi trong thời gian sớm nhất!' => 'Phản hồi chung',
            'Chúng tôi đã nhận được yêu cầu của bạn và đang xử lý.' => 'Đã nhận yêu cầu',
            'Vui lòng cung cấp thêm thông tin để chúng tôi hỗ trợ tốt hơn.' => 'Yêu cầu thêm thông tin',
            'Vấn đề của bạn đã được ghi nhận và sẽ được giải quyết trong vòng 24h.' => 'Đã ghi nhận',
            'Cảm ơn bạn đã kiên nhẫn chờ đợi. Chúng tôi đang tích cực xử lý yêu cầu của bạn.' => 'Đang xử lý'
          ] as $reply => $label)
            <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-2 text-start" 
                    onclick="document.getElementById('messageInput').value = '{{ $reply }}'; document.getElementById('charCount').textContent = {{ strlen($reply) }};">
              <small>{{ $label }}</small>
            </button>
          @endforeach
        </div>
      </div>

      <!-- Gửi vị trí nhanh -->
      <div class="card shadow mt-3">
        <div class="card-header bg-light">
          <h6 class="mb-0">📍 Gửi vị trí nhanh</h6>
        </div>
        <div class="card-body">
          @foreach($shopLocations as $shop)
          <div class="mb-3 p-2 border rounded">
            <strong class="d-block">{{ $shop->name }}</strong>
            <small class="text-muted">{{ $shop->address }}</small>
            <div class="mt-2">
              <button class="btn btn-sm btn-outline-primary w-100 send-location-btn" 
                      data-lat="{{ $shop->latitude }}" 
                      data-lng="{{ $shop->longitude }}"
                      data-name="{{ $shop->name }}"
                      data-address="{{ $shop->address }}">
                <i class="fas fa-map-marker-alt"></i> Gửi vị trí
              </button>
              <a href="https://www.google.com/maps/dir/?api=1&destination={{ $shop->latitude }},{{ $shop->longitude }}" 
                 target="_blank" class="btn btn-sm btn-outline-success w-100 mt-1">
                <i class="fas fa-route"></i> Chỉ đường
              </a>
            </div>
          </div>
          @endforeach

          @if($request->latitude && $request->longitude)
          <div class="mb-3 p-2 border rounded bg-light">
            <strong class="d-block">📍 Vị trí khách hàng</strong>
            <small class="text-muted">{{ $request->address ?? 'Đã chia sẻ vị trí' }}</small>
            <div class="mt-2">
              <a href="https://www.google.com/maps/dir/{{ $request->latitude }},{{ $request->longitude }}/{{ $shopLocations->first()->latitude }},{{ $shopLocations->first()->longitude }}" 
                 target="_blank" class="btn btn-sm btn-outline-info w-100">
                <i class="fas fa-walking"></i> Từ khách → Shop
              </a>
            </div>
          </div>
          @endif
        </div>
      </div>

<!-- Scroll to bottom button -->
<button class="scroll-to-bottom" id="scrollToBottom" onclick="scrollToBottom()">
  <i class="fas fa-arrow-down"></i>
</button>

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

<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places&callback=initMiniMap" async defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const chatBox = document.getElementById('chatBox');
  const messageInput = document.getElementById('messageInput');
  const charCount = document.getElementById('charCount');
  const attachmentInput = document.getElementById('attachment');
  const fileName = document.getElementById('fileName');
  const scrollToBottomBtn = document.getElementById('scrollToBottom');

  // Tự động cuộn xuống dưới cùng
  scrollToBottom();

  // Đếm ký tự
  messageInput.addEventListener('input', function() {
    const length = this.value.length;
    charCount.textContent = length;
    charCount.className = length > 1000 ? 'text-danger' : 'text-muted';
  });

  // Hiển thị tên file
  attachmentInput.addEventListener('change', function() {
    if (this.files.length > 0) {
      fileName.textContent = this.files[0].name;
      fileName.className = 'ms-2 small text-success';
    } else {
      fileName.textContent = '';
    }
  });

  // Hiển thị nút scroll to bottom khi cuộn lên
  chatBox.addEventListener('scroll', function() {
    const isAtBottom = this.scrollHeight - this.scrollTop - this.clientHeight < 100;
    scrollToBottomBtn.style.display = isAtBottom ? 'none' : 'flex';
  });

  // Khởi tạo char count
  charCount.textContent = messageInput.value.length;
});

function scrollToBottom() {
  const chatBox = document.getElementById('chatBox');
  chatBox.scrollTop = chatBox.scrollHeight;
}

function openImageModal(src) {
  document.getElementById('modalImage').src = src;
  new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function updateStatus(status) {
  fetch(`/admin/support/{{ $request->id }}/status`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ status: status })
  }).then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      }
    });
}

function updatePriority(priority) {
  // Tương tự như updateStatus, bạn có thể thêm API endpoint cho priority
  alert('Tính năng cập nhật mức độ ưu tiên đang được phát triển');
}

function insertQuickReply() {
  const quickReplies = [
    "Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi trong thời gian sớm nhất!",
    "Chúng tôi đã nhận được yêu cầu của bạn và đang xử lý.",
    "Vui lòng cung cấp thêm thông tin để chúng tôi hỗ trợ tốt hơn."
  ];
  
  const randomReply = quickReplies[Math.floor(Math.random() * quickReplies.length)];
  document.getElementById('messageInput').value = randomReply;
  document.getElementById('charCount').textContent = randomReply.length;
  document.getElementById('messageInput').focus();
}

// Auto-scroll khi có tin nhắn mới
const observer = new MutationObserver(function(mutations) {
  mutations.forEach(function(mutation) {
    if (mutation.addedNodes.length) {
      scrollToBottom();
    }
  });
});

observer.observe(document.getElementById('chatBox'), {
  childList: true,
  subtree: true
});

document.querySelectorAll('.send-location-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const lat = this.dataset.lat;
        const lng = this.dataset.lng;
        const name = this.dataset.name;
        const address = this.dataset.address;F📋 Thông tin yêu cầu
        
        fetch(`/admin/support/{{ $request->id }}/send-location`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                name: name,
                address: address
            })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  location.reload();
              }
          });
    });
});

// Mini Map cho vị trí khách hàng
function initMiniMap() {
    @if($request->latitude && $request->longitude)
    const customerLocation = { 
        lat: {{ $request->latitude }}, 
        lng: {{ $request->longitude }} 
    };
    
    const map = new google.maps.Map(document.getElementById('miniMap'), {
        zoom: 15,
        center: customerLocation,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        styles: [
            {
                "featureType": "all",
                "elementType": "geometry",
                "stylers": [{"color": "#f5f5f5"}]
            }
        ]
    });

    new google.maps.Marker({
        position: customerLocation,
        map: map,
        title: 'Vị trí khách hàng',
        icon: {
            url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
            scaledSize: new google.maps.Size(32, 32)
        }
    });

    // Thêm marker cho các cửa hàng gần đó
    @foreach($shopLocations as $shop)
    new google.maps.Marker({
        position: { lat: {{ $shop->latitude }}, lng: {{ $shop->longitude }} },
        map: map,
        title: '{{ $shop->name }}',
        icon: {
            url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
            scaledSize: new google.maps.Size(32, 32)
        }
    });
    @endforeach
    @endif
}

// Quick Reply với vị trí
function addLocationQuickReplies() {
    const locationReplies = {
        'Chào bạn! Dưới đây là địa chỉ cửa hàng chúng tôi:': '📍 Gửi địa chỉ cửa hàng',
        'Bạn có thể xem đường đi đến cửa hàng tại đây:': '🚗 Gửi chỉ đường',
        'Đây là chi nhánh gần bạn nhất:': '🏪 Gợi ý chi nhánh gần nhất'
    };
    
    // Thêm vào quick reply templates
}
</script>

<script>
class SupportChatRealTime {
    constructor(supportRequestId) {
        this.supportRequestId = supportRequestId;
        this.lastMessageId = {{ $request->replies->max('id') ?? 0 }};
        this.isPolling = false;
        this.pollingInterval = null;
        this.init();
    }

    init() {
        this.startPolling();
        this.setupFormSubmit();
        this.setupFirebase();
    }

    // Polling để lấy tin nhắn mới
    startPolling() {
        this.pollingInterval = setInterval(() => {
            this.checkNewMessages();
        }, 3000); // Kiểm tra mỗi 3 giây
    }

    async checkNewMessages() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        try {
            const response = await fetch(`/admin/support/${this.supportRequestId}/messages?last_message_id=${this.lastMessageId}`);
            const data = await response.json();
            
            if (data.messages && data.messages.length > 0) {
                this.appendNewMessages(data.messages);
                this.lastMessageId = data.last_message_id;
                this.scrollToBottom();
                this.updateUnreadBadges();
            }
        } catch (error) {
            console.error('Error fetching new messages:', error);
        }
        this.isPolling = false;
    }

appendNewMessages(messages) {
    console.log('Appending messages:', messages);
    const chatBox = document.getElementById('chatBox');
    console.log('Chat box found:', chatBox);
    
    messages.forEach(message => {
        console.log('Processing message:', message);
        const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
        if (existingMessage) {
            console.log('Message already exists:', message.id);
            return;
        }
        
        const messageElement = this.createMessageElement(message);
        console.log('Created message element:', messageElement);
        
        if (messageElement && chatBox) {
            chatBox.appendChild(messageElement);
            console.log('Message appended successfully');
        } else {
            console.error('Failed to append message:', { messageElement, chatBox });
        }
    });
}

createMessageElement(message) {
    const isAdmin = message.is_admin; // Sử dụng trường is_admin
    const messageClass = isAdmin ? 'message admin' : 'message user';
    const senderName = isAdmin ? (message.user ? message.user.name : 'Admin') : (message.name || 'Khách hàng');
    
    // Tạo div element thay vì return string
    const messageDiv = document.createElement('div');
    messageDiv.className = messageClass;
    messageDiv.setAttribute('data-message-id', message.id);
    
    messageDiv.innerHTML = `
        <div class="sender-name">
            ${this.escapeHtml(senderName)}
            ${isAdmin ? '<span class="badge bg-success status-badge">Nhân viên</span>' : '<span class="badge bg-primary status-badge">Khách hàng</span>'}
        </div>
        <div class="message-content">${this.escapeHtml(message.reply)}</div>
        <div class="message-time">
            ${new Date(message.created_at).toLocaleString('vi-VN')}
        </div>
    `;
    
    return messageDiv;
}

escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

    setupFormSubmit() {
    const form = document.getElementById('replyForm');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    
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
                this.appendNewMessages([result.reply]);
                this.lastMessageId = result.reply.id;
                this.scrollToBottom();
                
                // Reset form
                messageInput.value = '';
                document.getElementById('charCount').textContent = '0';
                document.getElementById('fileName').textContent = '';
                
                // Hiển thị thông báo thành công
                this.showSuccessMessage(result.message);
            } else {
                throw new Error(result.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.showErrorMessage(error.message || 'Có lỗi xảy ra khi gửi tin nhắn');
        } finally {
            this.isSubmitting = false;
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi tin nhắn';
        }
    });
}


// Thêm method hiển thị lỗi
showErrorMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-exclamation-circle"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

    setupFirebase() {
        // Khởi tạo Firebase Messaging
        const messaging = firebase.messaging();
        
        // Yêu cầu quyền thông báo
        this.requestNotificationPermission();
        
        // Xử lý tin nhắn background
        messaging.onBackgroundMessage((payload) => {
            console.log('Received background message:', payload);
            
            // Hiển thị thông báo
            this.showNotification(payload);
        });
    }

    async requestNotificationPermission() {
        try {
            const messaging = firebase.messaging();
            await messaging.requestPermission();
            const token = await messaging.getToken();
            
            // Lưu token vào database
            await this.saveFCMToken(token);
            
            console.log('FCM Token:', token);
        } catch (error) {
            console.log('Notification permission denied');
        }
    }

    async saveFCMToken(token) {
        try {
            await fetch('/api/save-fcm-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ token })
            });
        } catch (error) {
            console.error('Error saving FCM token:', error);
        }
    }

    showNotification(payload) {
        if (!("Notification" in window)) {
            console.log("This browser does not support notifications");
            return;
        }

        if (Notification.permission === "granted") {
            const notification = new Notification(payload.notification.title, {
                body: payload.notification.body,
                icon: '/images/logo.png',
                data: payload.data
            });

            notification.onclick = () => {
                window.focus();
                notification.close();
                
                // Xử lý khi click vào notification
                if (payload.data.support_request_id) {
                    window.location.href = `/admin/support/chat/${payload.data.support_request_id}`;
                }
            };
        }
    }

    scrollToBottom() {
        const chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    updateUnreadBadges() {
        // Cập nhật badge unread count
        fetch('/admin/support/unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.unread-badge');
                if (badge) {
                    badge.textContent = data.unread_count;
                    badge.style.display = data.unread_count > 0 ? 'inline' : 'none';
                }
            });
    }

    destroy() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
    }
}

// Khởi tạo real-time chat khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const supportRequestId = {{ $request->id }};
    window.supportChat = new SupportChatRealTime(supportRequestId);
});
</script>
@endsection
