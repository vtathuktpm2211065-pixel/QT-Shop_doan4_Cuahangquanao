@extends('app')

@section('content')
<style>
    .reset-wrapper {
        background-image: url('{{ asset("images/forgot-password.png") }}');
        background-size: cover;
        background-position: center;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .reset-card {
        background: rgba(255, 255, 255, 0.95);
        padding: 40px;
        border-radius: 12px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.2);
    }
</style>

<div class="reset-wrapper">
    <div class="reset-card">
        <h3 class="text-center mb-4">Khôi phục mật khẩu</h3>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Địa chỉ Email</label>
                <input id="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ $email ?? old('email') }}"
                       required autofocus autocomplete="email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu mới</label>
                <input id="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password-confirm" class="form-label">Xác nhận mật khẩu</label>
                <input id="password-confirm" type="password"
                       class="form-control"
                       name="password_confirmation" required autocomplete="new-password">
            </div>

            <!-- Submit -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    Đặt lại mật khẩu
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
