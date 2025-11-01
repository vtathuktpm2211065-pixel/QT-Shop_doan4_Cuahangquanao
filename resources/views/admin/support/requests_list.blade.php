@extends('layouts.admin')

@section('content')
<style>
    .request-resolved {
        background-color: #f8f9fa !important;
        opacity: 0.7;
    }
    .request-resolved:hover {
        background-color: #e9ecef !important;
        opacity: 0.9;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 12px;
    }
    .priority-badge {
        font-size: 0.7rem;
        padding: 3px 6px;
        border-radius: 10px;
    }
    .type-badge {
        font-size: 0.7rem;
        padding: 3px 6px;
        border-radius: 10px;
        background-color: #6c757d;
        color: white;
    }
    .customer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .chat-preview {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .header-stats {
        margin-top: 10px;
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">📬 Quản lý hỗ trợ khách hàng</h4>
                        
                    </div>
                    

                </div>

                <div class="card-body">
                    <!-- Bộ lọc -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <input type="text" id="customerFilter" class="form-control" placeholder="Tìm theo tên/email...">
                        </div>
                        <div class="col-md-2">
                            <select id="statusFilter" class="form-control">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending">⏳ Chờ xử lý</option>
                                <option value="processing">🔄 Đang xử lý</option>
                                <option value="resolved">✅ Đã giải quyết</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="typeFilter" class="form-control">
                                <option value="">Tất cả loại</option>
                                <option value="general">💬 Chung</option>
                                <option value="order">📦 Đơn hàng</option>
                                <option value="product">👕 Sản phẩm</option>
                                <option value="shipping">🚚 Vận chuyển</option>
                                <option value="payment">💳 Thanh toán</option>
                            </select>
                        </div>
                        {{-- <div class="col-md-2">
                            <select id="priorityFilter" class="form-control">
                                <option value="">Tất cả ưu tiên</option>
                                <option value="high">🔴 Cao</option>
                                <option value="medium">🟡 Trung bình</option>
                                <option value="low">🟢 Thấp</option>
                            </select>
                        </div> --}}
                        <div class="col-md-2">
                            <button id="resetFilter" class="btn btn-outline-secondary w-100">Đặt lại</button>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($requests->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Không có yêu cầu hỗ trợ nào.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="180px">Khách hàng</th>
                                        {{-- <th width="120px">Loại & Ưu tiên</th> --}}
                                        <th width="130px">Trạng thái</th>
                                        <th width="140px">Tin nhắn cuối</th>
                                        <th width="120px" class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $req)
                                    @php
                                        $unreadCount = $req->replies->where('is_read', false)->where('user_id', '!=', Auth::id())->count();
                                        $isResolved = $req->status == 'resolved';
                                        $lastReply = $req->replies->sortByDesc('created_at')->first();
                                    @endphp
                                    <tr class="{{ $isResolved ? 'request-resolved' : '' }} {{ $unreadCount > 0 ? 'table-warning' : '' }}">
                                        <!-- Thông tin khách hàng -->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="customer-avatar me-3">
                                                    {{ strtoupper(substr($req->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $req->name }}</strong>
                                                    <br><small class="text-muted">{{ $req->email }}</small>
                                                    @if($req->phone)
                                                        <br><small class="text-muted">{{ $req->phone }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>


                                        <!-- Loại & Ưu tiên -->
                                        {{-- <td>
                                            <span class="type-badge d-block mb-1 text-center">
                                                {{ $req->type ?? 'general' }}
                                            </span>
                                            <span class="priority-badge d-block text-center 
                                                {{ $req->priority == 'high' ? 'bg-danger' : ($req->priority == 'medium' ? 'bg-warning' : 'bg-success') }} text-white">
                                                {{ $req->priority == 'high' ? '🔴 Cao' : ($req->priority == 'medium' ? '🟡 TB' : '🟢 Thấp') }}
                                            </span>
                                        </td> --}}

                                        <!-- Trạng thái -->
  <td>
                                            <span class="status-badge 
                                                {{ $req->status == 'resolved' ? 'bg-success' : ($req->status == 'processing' ? 'bg-warning' : 'bg-secondary') }} text-white">
                                                {{ $req->status == 'resolved' ? '✅ Đã giải quyết' : ($req->status == 'processing' ? '🔄 Đang xử lý' : '⏳ Chờ xử lý') }}
                                            </span>
                                        </td>

                                        <!-- Tin nhắn cuối -->
                                        <td>
                                            @if($lastReply)
                                                <small>{{ \Illuminate\Support\Str::limit($lastReply->reply, 30) }}</small>
                                                <br><small class="text-muted">{{ $lastReply->created_at->diffForHumans() }}</small>
                                            @else
                                                <small class="text-muted">Chưa có phản hồi</small>
                                            @endif
                                        </td>

                                        <!-- Hành động -->
                                       <td class="text-center">
                                            <div class="action-buttons d-flex gap-1 justify-content-center">
                                                <!-- Nút Chat -->
                                                <a href="{{ route('admin.support.chat', $req->id) }}" 
                                                   class="btn btn-sm btn-primary position-relative"
                                                   title="Chat với {{ $req->name }}">
                                                    <i class="fas fa-comments"></i>
                                                    @if($unreadCount > 0)
                                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                            {{ $unreadCount }}
                                                        </span>
                                                    @endif
                                                </a>

                                                <!-- Nút Đánh dấu đã đọc -->
                                                @if($unreadCount > 0)
                                                <button class="btn btn-sm btn-success mark-read-btn" data-id="{{ $req->id }}" title="Đánh dấu đã đọc">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @endif

                                                <!-- Nút Xóa -->
                                                <form action="{{ route('admin.support.delete', $req->id) }}" method="POST" class="d-inline">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Bạn có chắc muốn xóa yêu cầu hỗ trợ này? Tất cả tin nhắn liên quan cũng sẽ bị xóa.')"
                                                            title="Xóa yêu cầu">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Thống kê nhanh -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Hiển thị <strong>{{ $requests->count() }}</strong> yêu cầu
                                    </small>
                                    <div class="d-flex gap-3">
                                        @php
                                            $pendingCount = $requests->where('status', 'pending')->count();
                                            $processingCount = $requests->where('status', 'processing')->count();
                                            $resolvedCount = $requests->where('status', 'resolved')->count();
                                        @endphp
                                        <small class="text-muted">
                                            ⏳ Chờ: <strong>{{ $pendingCount }}</strong>
                                        </small>
                                        <small class="text-muted">
                                            🔄 Đang xử lý: <strong>{{ $processingCount }}</strong>
                                        </small>
                                        <small class="text-muted">
                                            ✅ Đã giải quyết: <strong>{{ $resolvedCount }}</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Trả lời nhanh -->
<div class="modal fade" id="quickReplyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">⚡ Trả lời nhanh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickReplyForm">
                    @csrf
                    <input type="hidden" name="support_request_id" id="quickReplyRequestId">
                    <div class="mb-3">
                        <label>Mẫu trả lời nhanh:</label>
                        <select class="form-select" id="quickReplyTemplates">
                            <option value="">Chọn mẫu có sẵn</option>
                            <option value="Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất!">Phản hồi chung</option>
                            <option value="Chúng tôi đã nhận được yêu cầu và đang xử lý.">Đã nhận yêu cầu</option>
                            <option value="Vui lòng cung cấp thêm thông tin để chúng tôi hỗ trợ tốt hơn.">Yêu cầu thêm thông tin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea name="reply" class="form-control" rows="4" placeholder="Nhập nội dung trả lời..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi ngay</button>
                </form>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerFilter = document.getElementById('customerFilter');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const resetFilter = document.getElementById('resetFilter');

    // Lọc danh sách - GIỐNG NHƯ MẪU
    function filterRequests() {
        const customer = customerFilter.value.toLowerCase();
        const status = statusFilter.value;
        const type = typeFilter.value;
        const priority = priorityFilter.value;
        
        const rows = document.querySelectorAll('tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            const rowStatus = row.querySelector('.status-select').value;
            const rowType = row.cells[2].textContent.toLowerCase();
            
            // Lọc theo customer (tên/email)
            const customerMatch = name.includes(customer);
            // Lọc theo status
            const statusMatch = !status || rowStatus === status;
            // Lọc theo type - SỬA LẠI PHẦN NÀY
            const typeMatch = !type || rowType.includes(type);
            // Lọc theo priority
            const priorityMatch = !priority || row.cells[2].textContent.toLowerCase().includes(priority);
            
            if (customerMatch && statusMatch && typeMatch && priorityMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Cập nhật số lượng hiển thị
        const countElement = document.querySelector('.text-muted strong');
        if (countElement) {
            countElement.textContent = visibleCount;
        }
    }

    // Gắn sự kiện filter - GIỐNG NHƯ MẪU
    customerFilter.addEventListener('input', filterRequests);
    statusFilter.addEventListener('change', filterRequests);
    typeFilter.addEventListener('change', filterRequests);
    priorityFilter.addEventListener('change', filterRequests);
    
    resetFilter.addEventListener('click', function() {
        customerFilter.value = '';
        statusFilter.value = '';
        typeFilter.value = '';
        priorityFilter.value = '';
        filterRequests();
    });

    // Cập nhật trạng thái - GIỐNG NHƯ MẪU
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const requestId = this.dataset.id;
            const status = this.value;
            
            fetch(`/admin/support/${requestId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            });
        });
    });

    // Đánh dấu đã đọc - GIỐNG NHƯ MẪU
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const requestId = this.dataset.id;
            
            fetch(`/admin/support/${requestId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                location.reload();
            });
        });
    });

    // Trả lời nhanh - GIỐNG NHƯ MẪU
    document.querySelectorAll('.quick-reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('quickReplyRequestId').value = this.dataset.id;
        });
    });

    document.getElementById('quickReplyTemplates').addEventListener('change', function() {
        if (this.value) {
            document.querySelector('#quickReplyForm textarea').value = this.value;
        }
    });

    document.getElementById('quickReplyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/admin/support/quick-reply', {
            method: 'POST',
            body: formData
        }).then(() => {
            $('#quickReplyModal').modal('hide');
            location.reload();
        });
    });

    // Khởi tạo filter
    filterRequests();
});
</script>
@endsection

