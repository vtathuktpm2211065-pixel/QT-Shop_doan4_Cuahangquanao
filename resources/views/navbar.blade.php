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

            <!-- Chuông thông báo -->
            {{-- Trong file navbar.blade.php --}}

<!-- Chuông thông báo -->
<li class="nav-item position-relative">
    <a href="#" class="nav-link position-relative" id="notification-bell">
        <img src="https://cdn-icons-png.flaticon.com/512/565/565422.png" alt="Thông báo" style="width: 24px; height: 24px;">
        <span class="notification-count badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" id="notification-count" style="{{ $supportUnreadCount > 0 ? '' : 'display: none;' }}">
            {{ $supportUnreadCount ?? 0 }}
        </span>
    </a>
    <!-- Dropdown thông báo -->
    <div class="notification-dropdown rounded shadow-sm" id="notification-dropdown">
        <div class="notification-header d-flex justify-content-between align-items-center p-3 border-bottom">
            <h6 class="mb-0">Thông báo hỗ trợ</h6>
            <a href="#" class="text-decoration-none mark-all-read" id="mark-all-read">Đánh dấu đã đọc tất cả</a>
        </div>
        <div class="notification-list" id="notification-list">
            <!-- Danh sách thông báo sẽ được thêm vào đây bằng JavaScript -->
            @if($supportUnreadCount > 0)
                <div class="notification-item p-3 border-bottom unread">
                    <div class="d-flex align-items-start">
                        <div class="notification-icon me-3">
                            <img src="https://cdn-icons-png.flaticon.com/512/3595/3595455.png" alt="Thông báo" style="width: 24px; height: 24px;">
                        </div>
                        <div class="notification-content flex-grow-1">
                            <p class="mb-1">Bạn có {{ $supportUnreadCount }} tin nhắn chưa đọc từ hỗ trợ viên</p>
                            <small class="text-muted">Nhấp để xem</small>
                        </div>
                    </div>
                </div>
            @else
                <div class="notification-item p-3 border-bottom">
                    <div class="d-flex align-items-start">
                        <div class="notification-icon me-3">
                            <img src="https://cdn-icons-png.flaticon.com/512/3595/3595455.png" alt="Thông báo" style="width: 24px; height: 24px;">
                        </div>
                        <div class="notification-content flex-grow-1">
                            <p class="mb-1">Chào mừng bạn đến với QT Shop!</p>
                            <small class="text-muted">Vừa xong</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="notification-footer text-center p-2">
            <a href="{{ route('support.index') }}" class="text-decoration-none">Xem tất cả tin nhắn hỗ trợ</a>
        </div>
    </div>
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
        <li><a href="" class="nav-link">Thông báo</a></li>
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

// Trong file navbar.blade.php - phần JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const notificationBell = document.getElementById('notification-bell');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationCount = document.getElementById('notification-count');
    const markAllReadBtn = document.getElementById('mark-all-read');
    const notificationList = document.getElementById('notification-list');
    
    // Toggle dropdown thông báo
    notificationBell.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        notificationDropdown.classList.toggle('show');
        loadSupportNotifications();
    });
    
    // Đóng dropdown khi click ra ngoài
    document.addEventListener('click', function(e) {
        if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.remove('show');
        }
    });
    
    // Đánh dấu tất cả đã đọc
    markAllReadBtn.addEventListener('click', function(e) {
        e.preventDefault();
        markAllSupportAsRead();
    });
    
    // Kiểm tra thông báo mới mỗi 30 giây
    setInterval(checkNewSupportMessages, 30000);
    
    // Kiểm tra ngay khi trang load
    checkNewSupportMessages();
});

// Hàm kiểm tra tin nhắn hỗ trợ mới
function checkNewSupportMessages() {
    fetch('/support/unread-count')
        .then(response => response.json())
        .then(data => {
            const notificationCount = document.getElementById('notification-count');
            if (data.unread_count > 0) {
                notificationCount.textContent = data.unread_count;
                notificationCount.style.display = 'block';
                
                // Hiệu ứng rung chuông nếu có tin nhắn mới
                const notificationBell = document.getElementById('notification-bell');
                notificationBell.classList.add('ringing');
                setTimeout(() => {
                    notificationBell.classList.remove('ringing');
                }, 1000);
                
                // Hiển thị thông báo desktop
                showDesktopNotification(data.unread_count);
            } else {
                notificationCount.style.display = 'none';
            }
        })
        .catch(error => console.error('Lỗi kiểm tra tin nhắn:', error));
}

// Hàm đánh dấu tất cả đã đọc
function markAllSupportAsRead() {
    fetch('{{ route("support.mark-all-read", ["id" => $supportRequest->id ?? 0]) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật số lượng thông báo
            const notificationCount = document.getElementById('notification-count');
            notificationCount.textContent = '0';
            notificationCount.style.display = 'none';
            
            // Cập nhật giao diện thông báo
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            
            // Hiển thị thông báo thành công
            showAlert('Đã đánh dấu tất cả tin nhắn là đã đọc', 'success');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        showAlert('Có lỗi xảy ra khi đánh dấu đã đọc', 'error');
    });
}

// Hàm tải thông báo hỗ trợ
function loadSupportNotifications() {
    fetch('/support/unread-count')
        .then(response => response.json())
        .then(data => {
            const notificationList = document.getElementById('notification-list');
            if (data.unread_count > 0) {
                notificationList.innerHTML = `
                    <div class="notification-item p-3 border-bottom unread">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-3">
                                <img src="https://cdn-icons-png.flaticon.com/512/3595/3595455.png" alt="Thông báo" style="width: 24px; height: 24px;">
                            </div>
                            <div class="notification-content flex-grow-1">
                                <p class="mb-1">Bạn có <strong>${data.unread_count}</strong> tin nhắn chưa đọc từ hỗ trợ viên</p>
                                <small class="text-muted">Nhấp vào "Xem tất cả" để xem chi tiết</small>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                notificationList.innerHTML = `
                    <div class="notification-item p-3 border-bottom">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-3">
                                <img src="https://cdn-icons-png.flaticon.com/512/3595/3595455.png" alt="Thông báo" style="width: 24px; height: 24px;">
                            </div>
                            <div class="notification-content flex-grow-1">
                                <p class="mb-1">Chào mừng bạn đến với QT Shop!</p>
                                <small class="text-muted">Không có tin nhắn mới</small>
                            </div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => console.error('Lỗi tải thông báo:', error));
}

// Hiển thị thông báo desktop
function showDesktopNotification(unreadCount) {
    if (Notification.permission === 'granted') {
        new Notification('QT Shop - Tin nhắn mới', {
            body: `Bạn có ${unreadCount} tin nhắn chưa đọc từ hỗ trợ viên`,
            icon: 'https://cdn-icons-png.flaticon.com/512/3595/3595455.png'
        });
    } else if (Notification.permission !== 'denied') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                new Notification('QT Shop - Tin nhắn mới', {
                    body: `Bạn có ${unreadCount} tin nhắn chưa đọc từ hỗ trợ viên`,
                    icon: 'https://cdn-icons-png.flaticon.com/512/3595/3595455.png'
                });
            }
        });
    }
}

// Hiển thị thông báo
function showAlert(message, type = 'success') {
    // Tạo và hiển thị thông báo tạm thời
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);

}
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

/* Dropdown thông báo */
.notification-dropdown {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  background: #fff;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  width: 320px;
  max-height: 400px;
  overflow-y: auto;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 1050;
}

.notification-dropdown.show {
  display: block;
}

.notification-item {
  transition: background-color 0.2s;
}

.notification-item:hover {
  background-color: #f8f9fa;
}

.notification-item.unread {
  background-color: #f0f8ff;
  border-left: 3px solid #007bff;
}

.mark-all-read {
  font-size: 0.85rem;
  color: #007bff;
}

.mark-all-read:hover {
  text-decoration: underline !important;
}

/* Hiệu ứng rung chuông */
@keyframes ring {
  0% { transform: rotate(0); }
  10% { transform: rotate(-10deg); }
  20% { transform: rotate(10deg); }
  30% { transform: rotate(-10deg); }
  40% { transform: rotate(10deg); }
  50% { transform: rotate(0); }
  100% { transform: rotate(0); }
}

.ringing img {
  animation: ring 0.5s ease-in-out;
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
  
  /* Điều chỉnh dropdown thông báo trên mobile */
  .notification-dropdown {
    width: 280px;
    right: -50px;
  }
}
@keyframes marquee {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}
</style>
