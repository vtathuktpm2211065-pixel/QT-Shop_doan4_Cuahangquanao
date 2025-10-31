@extends('app') 
@section('content')

<div class="container py-4">
    <h4 class="mb-4">Hỗ trợ từ cửa hàng</h4>

    <div class="card">
        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
            @forelse($supportRequest->replies->sortBy('created_at') as $reply)
                @php
                    $isCurrentUser = Auth::check() && $reply->user_id === Auth::id();
                @endphp

                <div class="d-flex mb-3 {{ $isCurrentUser ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="p-3 rounded shadow-sm {{ $isCurrentUser ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 70%;">
                        <div class="small fw-bold mb-1">
                            {{ $reply->name ?? 'Khách' }} 
                            <span class="text-muted small">({{ $reply->created_at->format('H:i d/m/Y') }})</span>
                        </div>
                        <div>{{ $reply->reply }}</div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">Chưa có phản hồi nào từ cửa hàng.</p>
            @endforelse
        </div>

        {{-- Form gửi phản hồi --}}
        <div class="card-footer">
            <form action="{{ route('support.reply', $supportRequest->id) }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="reply" class="form-control" placeholder="Nhập tin nhắn..." required>
                    <button type="submit" class="btn btn-primary">Gửi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

