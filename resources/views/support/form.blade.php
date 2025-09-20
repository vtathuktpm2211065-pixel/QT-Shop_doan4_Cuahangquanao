@extends('app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">📨 Gửi yêu cầu hỗ trợ</h2>

    {{-- Hiển thị thông báo lỗi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Hiển thị thông báo gửi thành công --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('support.submit') }}" method="POST">
        @csrf

        @guest
            <input type="text" name="name" class="form-control mb-2" placeholder="Họ tên" required>
            <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
            <input type="text" name="phone" class="form-control mb-2" placeholder="Số điện thoại ">
        @else
            {{-- Hiển thị thông tin người dùng --}}
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
            
            <input type="text" value="{{ Auth::user()->name }}" class="form-control mb-2" disabled>
            <input type="email" value="{{ Auth::user()->email }}" class="form-control mb-2" disabled>

            @if (!Auth::user()->phone)
                <div class="alert alert-warning d-flex justify-content-between align-items-center">
                    <span>Bạn chưa cập nhật số điện thoại.</span>
                    <a href="{{ route('hoso.index') }}" class="btn btn-sm btn-outline-primary">Cập nhật</a>
                </div>
            @else
                <input type="text" value="{{ Auth::user()->phone }}" class="form-control mb-2" disabled>
            @endif
        @endguest

        <textarea name="message" class="form-control mb-3" placeholder="Nội dung cần hỗ trợ" rows="5" required></textarea>
        <button type="submit" class="btn btn-success">Gửi yêu cầu</button>
    </form>
</div>
@endsection
