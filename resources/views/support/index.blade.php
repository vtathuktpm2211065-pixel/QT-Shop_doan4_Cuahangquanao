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
                            <div class="modal-body">
                                @foreach($supportRequest->replies as $reply)
                                    <div class="mb-3 p-2 rounded {{ $reply->user_id ? 'bg-light text-end' : 'bg-secondary text-white' }}">
                                        <div><strong>{{ $reply->user_id ? ($reply->user->name ?? 'Người dùng') : 'Admin' }}</strong></div>
                                        <div>{{ $reply->reply }}</div>
                                        <small class="text-muted">🕒 {{ $reply->created_at->format('H:i d/m/Y') }}</small>
                                    </div>
                                @endforeach

                                {{-- Form phản hồi --}}
                                <form action="{{ route('support.reply', $supportRequest->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="reply" class="form-control" rows="2" placeholder="Nhập nội dung phản hồi..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">📤 Gửi phản hồi</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

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
