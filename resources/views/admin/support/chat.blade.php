@extends('layouts.admin')

@section('content')
<style>
  .chat-box {
    height: 400px;
    overflow-y: auto;
    padding: 10px;
    background-color: #f2f3f5;
  }

  .message {
    padding: 10px 15px;
    border-radius: 10px;
    margin-bottom: 12px;
    max-width: 70%;
    word-wrap: break-word;
  }

  /* Tin nhắn khách hàng (bên trái) */
  .message.user {
    background-color: #e2e3e5;
    color: #000;
    text-align: left;
  }

  /* Tin nhắn admin hoặc staff (bên phải) */
  .message.admin {
    background-color: #007bff;
    color: #fff;
    margin-left: auto;
    text-align: right;
  }

  .sender-name {
    font-weight: bold;
    margin-bottom: 5px;
  }

  .message-time {
    font-size: 0.75rem;
    color: #666;
    margin-top: 5px;
  }
</style>

<div class="container py-4">
  <h4 class="mb-4">Trò chuyện với {{ $request->name }}</h4>
  <div class="card shadow">
    <div class="card-body chat-box">
      <!-- Tin nhắn gốc từ người dùng -->
      <div class="message user">
        <div class="sender-name">{{ $request->name }}</div>
        <div>{{ $request->message }}</div>
        <div class="message-time">{{ $request->created_at->format('d/m/Y H:i') }}</div>
      </div>

      <!-- Các phản hồi -->
      @foreach($request->replies as $reply)
        @php
            // Nếu có user_id là admin hoặc staff → hiển thị bên phải
            $isAdminOrStaff = $reply->user && ($reply->user->hasRole('admin') || $reply->user->hasRole('staff'));
            $senderName = $reply->user ? $reply->user->name : 'Khách hàng';
        @endphp

        <div class="message {{ $isAdminOrStaff ? 'admin' : 'user' }}">
          <div class="sender-name">{{ $senderName }}</div>
          <div>{{ $reply->reply }}</div>
          <div class="message-time">{{ $reply->created_at->format('d/m/Y H:i') }}</div>
        </div>
      @endforeach
    </div>

    <!-- Form trả lời admin/staff -->
    <div class="card-footer">
      <form action="{{ route('admin.support.reply', $request->id) }}" method="POST">
        @csrf
        <div class="input-group">
          <input type="text" name="reply" class="form-control" placeholder="Nhập nội dung trả lời..." required>
          <button class="btn btn-primary" type="submit">Gửi</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
