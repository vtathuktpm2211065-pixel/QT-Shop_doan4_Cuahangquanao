<div class="site-navbar bg-white py-3 shadow-sm">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between">
      <!-- Logo -->
      <div class="logo">
        <a href="{{ route('home') }}" class="site-logo fw-bold fs-3 text-dark text-decoration-none">QT SHOP</a>
      </div>

      <!-- Navigation -->
      <div class="main-nav d-none d-lg-flex align-items-center">
        <nav class="site-navigation text-right text-md-center" role="navigation">
          <ul class="site-menu d-flex align-items-center mb-0">
            <!-- Thanh tìm kiếm -->
            <li class="nav-item me-3">
              <form action="{{ route('search') }}" method="GET" class="d-flex align-items-center border rounded-pill shadow-sm overflow-hidden">
                <input type="text" name="keyword" class="form-control border-0 py-2 px-3" placeholder="Tìm kiếm sản phẩm..." required style="width: 200px;">
                <button type="submit" class="search-button bg-transparent border-0 p-2" title="" id="searchBtn">
                  <img src="https://cdn-icons-png.flaticon.com/512/54/54481.png" alt="Tìm kiếm" style="width: 20px; height: 20px;">
                </button>
              </form>
            </li>

            <!-- Menu chính -->
            <li class="nav-item"><a href="{{ route('home') }}" class="nav-link active">Home</a></li>

            <li class="nav-item has-children">
              <a href="#" class="nav-link">
                Các sản phẩm 
              </a>
              <ul class="dropdown rounded shadow-sm">
                <li><a href="{{ route('san-pham.cho-nu') }}">Quần áo Nữ</a></li>
                <li><a href="{{ route('san-pham.cho-nam') }}">Quần áo Nam</a></li>
                <li><a href="{{ route('san-pham.cho-tre-em') }}">Quần Áo Trẻ Em</a></li>
              </ul>
            </li>

            <li class="nav-item has-children">
              <a href="#" class="nav-link">Đơn hàng</a>
              <ul class="dropdown rounded shadow-sm">
                <li><a href="{{ route('orders.index') }}">Đơn hàng của tôi</a></li>
                <li><a href="{{ route('guest.track_order_form') }}">Tra cứu đơn hàng</a></li>
              </ul>
            </li>

            <!-- Giỏ hàng -->
            <li class="nav-item position-relative">
              <a href="{{ route('cart.index') }}" class="nav-link position-relative">
                <img src="{{ asset('images/cart.png') }}" alt="Giỏ hàng" style="width: 24px; height: 24px;">
                <span class="cart-count badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">
                  {{ $totalQuantity ?? 0 }}
                </span>
              </a>
            </li>

            <!-- Tin nhắn hỗ trợ -->
            <li class="nav-item position-relative">
              <a href="{{ route('support.index') }}" class="nav-link position-relative" title="Hỗ trợ">
                <img src="{{ asset('images/chatting.png') }}" alt="Tin nhắn" style="width: 24px; height: 24px;">
                {{-- Biểu tượng 🔴 đơn --}}
                <span id="unread-indicator"
                      style="position: absolute; top: 0; right: -4px; font-size: 16px; {{ Auth::check() && $unreadCount > 0 ? '' : 'display: none;' }}">
                  🔴
                </span>
              </a>
            </li>

            <!-- Tài khoản -->
            <li class="nav-item has-children">
              <a href="#" class="nav-link">Tài khoản </a>
              <ul class="dropdown rounded shadow-sm">
                <li><a href="{{ route('hoso.index') }}">Hồ sơ</a></li>
                <li><a href="{{ route('gioithieu') }}">Giới thiệu</a></li>
                <li>
                  <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                  </form>
                </li>
              </ul>
            </li>

          </ul>
        </nav>
      </div>

      <!-- Menu toggle cho mobile -->
      <div class="mobile-toggle d-lg-none">
        <button class="btn btn-outline-dark border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>

    <!-- Menu mobile -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="site-menu mobile-menu mt-3">
        <li><a href="{{ route('home') }}" class="nav-link">Home</a></li>
        <li class="has-children">
          <a href="#" class="nav-link">Các sản phẩm</a>
          <ul class="dropdown">
            <li><a href="{{ route('san-pham.cho-nu') }}">Quần áo Nữ</a></li>
            <li><a href="{{ route('san-pham.cho-nam') }}">Quần áo Nam</a></li>
            <li><a href="{{ route('san-pham.cho-tre-em') }}">Quần Áo Trẻ Em</a></li>
          </ul>
        </li>
        <li><a href="{{ route('gioithieu') }}" class="nav-link">Giới thiệu</a></li>
        <li><a href="{{ route('orders.index') }}" class="nav-link">Đơn hàng của tôi</a></li>
        <li><a href="{{ route('guest.track_order_form') }}" class="nav-link">Tra cứu đơn hàng</a></li>
        <li><a href="{{ route('cart.index') }}" class="nav-link">Giỏ hàng</a></li>
        <li class="has-children">
          <a href="#" class="nav-link">Tài khoản</a>
          <ul class="dropdown">
            <li><a href="{{ route('hoso.index') }}">Hồ sơ</a></li>
            <li>
              <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">Đăng xuất</a>
              <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>

<div style="white-space: nowrap; overflow: hidden; background-color: #f8f9fa; padding: 10px 0;">
  <div style="
      display: inline-block;
      padding-left: 100%;
      animation: marquee 15s linear infinite;
      font-weight: bold;
      color: #333;
  ">
    Quý khách có nhu cầu mua sỉ hoặc cần hỗ trợ, vui lòng nhắn tin trực tiếp cho shop để được tư vấn nhanh chóng. Xin chân thành cảm ơn 💖
  </div>
</div>

<script>
function addToCartSlug(slug) {
  $.post('/cart/add/' + slug)
    .done(function(response) {
      if (response.message && response.message.includes('Đã thêm vào giỏ hàng')) {
        alert('Đã thêm vào giỏ hàng!');
        $('.cart-count').text(parseInt(response.totalQuantity));
      } else if (response.error) {
        alert(response.error);
      } else {
        alert('Phản hồi không xác định!');
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.error('Lỗi AJAX:', textStatus, errorThrown);
      alert('Có lỗi xảy ra, vui lòng thử lại.');
    });
}

// Mobile menu toggle dropdown
document.querySelectorAll('.mobile-menu .has-children > a').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const parentLi = this.parentElement;
    parentLi.classList.toggle('active');
  });
});
</script>

<style>
.site-navbar {
  transition: all 0.3s ease;
  z-index: 1000;
}

.site-logo {
  font-size: 1.8rem;
  font-weight: 700;
  color: #333 !important;
  text-decoration: none !important;
}

.site-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.site-menu .nav-item {
  margin-left: 1.5rem;
}

.nav-link {
  color: #333 !important;
  font-size: 1rem;
  padding: 0.5rem 0;
  transition: color 0.3s ease;
  cursor: pointer;
}

.nav-link:hover {
  color: #007bff !important;
}

.has-children {
  position: relative;
}

/* Dropdown mặc định ẩn */
.dropdown {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  background: #fff;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 0.5rem 0;
  min-width: 150px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

/* Hiện dropdown khi hover trên desktop */
.has-children:hover > ul.dropdown {
  display: block;
}

.dropdown li {
  padding: 0.5rem 1rem;
}

.dropdown li a {
  color: #333;
  text-decoration: none;
  font-size: 0.95rem;
}

.dropdown li a:hover {
  color: #007bff;
}

.form-control:focus {
  box-shadow: none;
  border-color: #007bff;
}

.badge {
  font-size: 0.75rem;
  padding: 0.3em 0.5em;
}

/* Responsive */
@media (max-width: 992px) {
  .main-nav {
    display: none !important;
  }

  .mobile-toggle {
    display: block !important;
  }

  .mobile-menu {
    list-style: none;
    padding: 0;
  }

  .mobile-menu .nav-item {
    margin: 0.5rem 0;
  }

  /* Dropdown menu mobile: vị trí static, ẩn mặc định */
  .mobile-menu .dropdown {
    position: static;
    display: none;
    background: #f8f9fa;
    border: none;
    box-shadow: none;
    padding-left: 1.5rem;
  }

  /* Khi active thì xổ ra */
  .mobile-menu .has-children.active > .dropdown {
    display: block;
  }
}
@keyframes marquee {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}
</style>