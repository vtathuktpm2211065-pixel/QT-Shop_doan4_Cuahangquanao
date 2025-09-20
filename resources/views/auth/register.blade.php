<html lang="en">
<head>
  <title>@yield('title', 'QT SHOP')</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://fonts.googleapis.com/css?family=Mukta:300,400,700" rel="stylesheet"> 
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
  <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">
  <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/aos.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
      @yield('styles')
</head>
<style>
    body {
      background-image: url('https://images.unsplash.com/photo-1521334884684-d80222895322?auto=format&fit=crop&w=800&q=90');
        background-size: cover;
        background-position: center;
        min-height: 100vh;
    }

    .register-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: brightness(0.9);
    }

    .register-card {
        background: rgba(255, 255, 255, 0.95);
        padding: 40px;
        border-radius: 10px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    }

    .facebook-btn img {
        margin-right: 8px;
    }
        .btn-custom-blue {
    background-color: #0d6efd; /* màu xanh Bootstrap primary */
    color: white;
    border: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.4);
}

.btn-custom-blue:hover {
    background-color: #0b5ed7;
    box-shadow: 0 6px 12px rgba(11, 94, 215, 0.6);
    color: white;
}

.btn-outline-blue {
    color: #0d6efd;
    border: 2px solid #0d6efd;
    background-color: transparent;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: none;
}

.btn-outline-blue:hover {
    color: white;
    background-color: #0d6efd;
    border-color: #0d6efd;
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.5);
}
</style>
<body>
@if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif


<div class="register-wrapper">
    <div class="register-card">
        <h3 class="text-center mb-4">Tạo tài khoản mới</h3>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <input id="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       name="name" value="{{ old('name') }}" required autofocus>
<small class="form-text text-muted">
  Tên không được chứa số và ký tự đặc biệt.
</small>

                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

          <div class="mb-3">
    <label for="username" class="form-label">Username</label>
    <input id="username" type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
    <small class="form-text text-muted">
    Username chỉ được chứa các ký tự chữ hoa, chữ thường (a-zA-Z), số (0-9), và dấu gạch dưới _.
    </small>
    @error('username')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
    <small class="form-text text-muted">
    Email chỉ chấp nhận khi có đuôi @gmail.com.
       </small>
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3 position-relative">
    <label for="password" class="form-label">Mật khẩu</label>
    <input id="password" type="password"
           class="form-control @error('password') is-invalid @enderror"
           name="password" required>
    <span class="toggle-password" style="position: absolute; top: 38px; right: 15px; cursor: pointer;">
        👁️
    </span>
   <small class="form-text text-muted">
    Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số.
</small>

    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


<div class="mb-3 position-relative">
    <label for="password-confirm" class="form-label">Xác nhận mật khẩu</label>
    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
    <span class="toggle-password" style="position: absolute; top: 38px; right: 15px; cursor: pointer;">
        👁️
    </span>
<small class="form-text text-muted">
    Mật khẩu xác nhận phải trùng với mật khẩu.
</small>

</div>

            <div class="mb-3 d-flex justify-content-center">
                <button type="submit" class="btn btn-custom-blue">
                    Đăng ký
                </button>
            </div>

            <div class="text-center mb-3">
                <span>Hoặc đăng ký bằng</span>
            </div>

            <div class="d-grid mb-3">
                <a href="{{ url('auth/facebook') }}" class="btn btn-outline-blue facebook-btn d-flex align-items-center justify-content-center">
                    <img src="https://cdn-icons-png.flaticon.com/24/733/733547.png" alt="Facebook logo">
                    <span>Facebook</span>
                </a>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}">Bạn đã có tài khoản? Đăng nhập</a>
            </div>
        </form>
    </div>
</div>


 
<script>
document.querySelectorAll('.toggle-password').forEach(el => {
    el.addEventListener('click', function() {
        const input = this.previousElementSibling;
        if (input.type === 'password') {
            input.type = 'text';
            this.textContent = '🙈'; 
        } else {
            input.type = 'password';
            this.textContent = '👁️';
        }
    });
});
</script>

</body>
</html>