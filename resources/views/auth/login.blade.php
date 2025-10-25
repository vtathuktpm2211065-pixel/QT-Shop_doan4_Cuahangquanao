<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập - QT SHOP</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css?family=Mukta:300,400,700" rel="stylesheet"> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .btn-custom-blue {
      background-color: #0d6efd;
      color: white;
      font-weight: 600;
      transition: background-color 0.3s ease;
      box-shadow: 0 4px 8px rgba(13, 110, 253, 0.4);
    }
    .btn-custom-blue:hover {
      background-color: #0b5ed7;
      color: white;
    }
    .btn-outline-blue {
      color: #0d6efd;
      border: 2px solid #0d6efd;
      font-weight: 600;
      background-color: transparent;
    }
    .btn-outline-blue:hover {
      background-color: #0d6efd;
      color: white;
    }
    .btn-outline-red {
      color: #db4437;
      border: 2px solid #db4437;
      font-weight: 600;
      background-color: transparent;
    }
    .btn-outline-red:hover {
      background-color: #db4437;
      color: white;
    }
    .login-container {
      min-height: 100vh;
      background: url('https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=1200&q=80') no-repeat center center;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-form-wrapper {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      max-width: 500px;
      width: 100%;
      border-radius: 8px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }
    .toggle-btn {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
    }
    .divider {
      display: flex;
      align-items: center;
      text-align: center;
      margin: 20px 0;
    }
    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid #ddd;
    }
    .divider span {
      padding: 0 10px;
      color: #6c757d;
      font-size: 0.9rem;
    }
    .social-login-buttons {
      display: flex;
      gap: 10px;
    }
    .social-login-buttons .btn {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-form-wrapper">
      <h4 class="text-center mb-4">Đăng nhập</h4>

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
          <label for="login" class="form-label">Username hoặc Email</label>
          <input type="text" name="login" id="login" class="form-control @error('login') is-invalid @enderror" value="{{ old('login') }}" required autofocus>
          @error('login')
          <span class="invalid-feedback" role="alert">{{ $message }}</span>
          @enderror
        </div>

        <div class="mb-3 position-relative">
          <label for="password" class="form-label">Mật khẩu</label>
          <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
          <span class="toggle-btn" id="togglePassword">👁️</span>
          @error('password')
          <span class="invalid-feedback" role="alert">{{ $message }}</span>
          @enderror
        </div>

        <div class="mb-3 form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
        </div>

        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-custom-blue">Đăng nhập</button>
        </div>

        <div class="divider">
          <span>Hoặc đăng nhập bằng</span>
        </div>

        <div class="social-login-buttons mb-3">
          <a href="{{ url('auth/facebook') }}" class="btn btn-outline-blue d-flex justify-content-center align-items-center">
            <img src="https://cdn-icons-png.flaticon.com/24/733/733547.png" alt="Facebook logo" style="margin-right: 8px;">
            Facebook
          </a>
          <a href="{{ route('google.login') }}" class="btn btn-outline-red d-flex justify-content-center align-items-center">
            <img src="https://cdn-icons-png.flaticon.com/24/300/300221.png" alt="Google logo" style="margin-right: 8px;">
            Google
          </a>
        </div>


        <div class="d-flex justify-content-between small">
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
          @endif
          <a href="{{ route('register') }}">Tạo tài khoản mới</a>
        </div>
      </form>
    </div>
  </div>

  <!-- JS -->
  <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
      const pwd = document.getElementById('password');
      if (pwd.type === 'password') {
        pwd.type = 'text';
        this.textContent = '🙈';
      } else {
        pwd.type = 'password';
        this.textContent = '👁️';
      }
    });
  </script>
</body>
</html>