<footer class="site-footer custom-border-top">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-lg-3 mb-4 mb-lg-0">
        <h3 class="footer-heading mb-4">Promo</h3>
        <a href="#" class="block-6">
          <img src="{{ asset('images/about_1.jpg') }}" alt="Image placeholder" class="img-fluid rounded mb-4">
          <h3 class="font-weight-light mb-0">Hãy chọn bộ quần áo mà bạn thích nhất</h3>
          <p>25/05/2025</p>
        </a>
      </div>
      <div class="col-lg-5 ml-auto mb-5 mb-lg-0">
        <div class="row">
          <div class="col-md-12">
            <h3 class="footer-heading mb-4">Liên kết nhanh</h3>
          </div>
        <div class="col-md-6 col-lg-4">
  <ul class="list-unstyled">
    <li><a href="#">Bán hàng trực tuyến</a></li>
    <li><a href="#">Tính năng</a></li>
    <li><a href="#">Giỏ hàng</a></li>
    <li><a href="#">Trình tạo cửa hàng</a></li>
  </ul>
</div>
<div class="col-md-6 col-lg-4">
  <ul class="list-unstyled">
    <li><a href="#">Thương mại di động</a></li>
    <li><a href="#">Bán hàng dropshipping</a></li>
    <li><a href="#">Phát triển website</a></li>
  </ul>
</div>
<div class="col-md-6 col-lg-4">
  <ul class="list-unstyled">
    <li><a href="#">Điểm bán hàng (POS)</a></li>
    <li><a href="#">Phần cứng</a></li>
    <li><a href="#">Phần mềm</a></li>
  </ul>
</div>

        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="block-5 mb-5">
          <h3 class="footer-heading mb-4">Thông tin</h3>
          <ul class="list-unstyled">
            <li class="address">256,Nguyễn Văn Cừ,An Hòa,Ninh Kiêu,Cần thơ</li>
            <li class="phone"><a href="tel://23923929210">0948056732</a></li>
            <li class="email">QT_Shop250525@gmai.com</li>
          </ul>
        </div>

        <div class="block-7">
          <form action="#" method="post">
            @csrf
            <label for="email_subscribe" class="footer-heading">Đăng ký nhận tin</label>
            <div class="form-group">
              <input type="text" class="form-control py-4" id="email_subscribe" placeholder="Email">
              <input type="submit" class="btn btn-sm btn-primary" value="Send">
            </div>
          </form>
        </div>
      </div>
    </div>
   
  </div>
</footer>
