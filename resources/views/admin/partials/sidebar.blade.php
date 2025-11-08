<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="{{ route('admin.dashboard') }}" class="brand-link">
    <span class="brand-text font-weight-light">Admin Panel</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">

        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item has-treeview">
  <a href="#" class="nav-link">
    <i class="nav-icon fas fa-chart-line"></i>
    <p>
      Báo cáo doanh thu
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="{{ route('admin.revenue.daily') }}" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Theo ngày</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.revenue.monthly') }}" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Theo tháng</p>
      </a>
    </li>
    
    <li class="nav-item">
      <a href="{{ route('admin.revenue.yearly') }}" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Theo năm</p>
      </a>
    </li>
  </ul>
</li>

<li class="nav-item">
    <a href="{{ route('admin.phanquyen') }}" class="nav-link">
        <i class="nav-icon fas fa-users-cog"></i>
        <p>Phân quyền</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.orders.index') }}" class="nav-link">
        <i class="nav-icon fas fa-receipt me-1"></i>
        <p>Quản lý đơn hàng</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.carts.index') }}" class="nav-link">
        <i class="nav-icon fas fa-shopping-cart me-1"></i>
        <p>Quản lý giỏ hàng</p>
    </a>
</li>

 <li class="nav-item">
      <a href="{{ route('admin.stock.index') }}" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Quản lý kho</p>
      </a>
    </li>
<li class="nav-item has-treeview">
  <a href="#" class="nav-link">
    <i class="nav-icon fas fa-box"></i>
    <p>
      Sản phẩm
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="{{ route('admin.san-pham.index') }}" class="nav-link">
        <i class="far fa-dot-circle nav-icon"></i>
        <p>Danh sách sản phẩm</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.san-pham.create') }}" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Thêm sản phẩm</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.danh-muc.index') }}" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Danh mục</p>
      </a>
    </li>
    
  </ul>
</li> 
<li class="nav-item">
  <a href="{{ route('admin.customers.index') }}" class="nav-link">
    <i class="fas fa-users"></i>
    <p>Quản lý khách hàng</p>
  </a>
</li>

        <li class="nav-item">
  <a href="{{ route('admin.team.index') }}" class="nav-link">
    <i class="fas fa-user-cog"></i>
    <p>Quản lý nhân viên</p>
  </a>
</li>
<li class="nav-item">
  <a href="{{ route('admin.vouchers.index') }}" class="nav-link">
    <i class="fas fa-ticket-alt"></i>
    <p>Quản lý Voucher</p>
  </a>
</li>
<li class="nav-item">
          <a href="{{ route('admin.requests.index') }}" class="nav-link">
            <i class="fas fa-headset"></i>
            <p>Hỗ trợ khách hàng</p>
          </a>
        </li>
<li class="nav-item">
          <a href="{{ route('admin.reviews.index') }}" class="nav-link">
            <i class="fas fa-star nav-icon"></i>
            <p>Quản lý đánh giá</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
