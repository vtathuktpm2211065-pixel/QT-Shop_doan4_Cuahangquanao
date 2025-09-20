<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a href="#" class="nav-link">🏠 Trang chủ Admin</a>
    </li>
  </ul>

  <!-- Thêm phần bên phải thanh navbar -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <a href="{{ route('home') }}" class="nav-link">🏠 Trang người dùng</a>
    </li>
    <li class="nav-item">
      <form action="{{ route('logout') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="nav-link btn btn-link" style="display: inline; padding: 0; border: none; background: none;">🚪 Đăng xuất</button>
      </form>
    </li>
  </ul>
</nav>
