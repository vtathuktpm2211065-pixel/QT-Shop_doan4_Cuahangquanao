@extends('layouts.admin')

@section('content')
<div class="container">
    <h3 class="mb-4">📝 Sửa thông tin người dùng</h3>

    <form action="{{ route('admin.updateUserInfo', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name">Họ và tên</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
        </div>

        <div class="mb-3 position-relative">
            <label for="password">Mật khẩu mới (nếu cần đổi)</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••">
                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                    🙈
                </button>
            </div>
            <small class="text-muted">Để trống nếu không muốn đổi mật khẩu.</small>
        </div>

        <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        this.textContent = isHidden ? '🐵' : '🙈';
    });
</script>
@endsection
