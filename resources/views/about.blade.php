@extends('app')

@section('content')


<!-- PHẦN MỚI THÊM VÀO -->
<div class="site-blocks-cover inner-page" data-aos="fade">
  <div class="container">
    <div class="row">
      <div class="col-md-6 ml-auto order-md-2 align-self-start">
        <div class="site-block-cover-content text-center">
          <h1 class="mb-3">Khám Phá Phong Cách Việt</h1>
          <a href="{{ route('san-pham.noi-bat') }}" 
             class="btn btn-dark rounded-pill px-3 py-1 mx-auto d-inline-block" 
             style="font-size: 0.875rem;">
             Mua Ngay
          </a>
        </div>
      </div>
      <div class="col-md-6 order-1 align-self-end">
        <img src="images/model_7.png" alt="Image" class="img-fluid" />
      </div>
    </div>
  </div>
</div>


      <div
        class="site-section site-section-sm site-blocks-1 border-0"
        data-aos="fade"
      >
        <div class="container">
          <div class="row">
            <div
              class="col-md-6 col-lg-4 d-lg-flex mb-4 mb-lg-0 pl-4"
              data-aos="fade-up"
              data-aos-delay=""
            >
              <div class="icon mr-4 align-self-start">
                <span class="icon-truck"></span>
              </div>
              <div class="text">
              <h2 class="text-uppercase">Miễn Phí Giao Hàng</h2>
<p>Miễn phí giao hàng toàn quốc cho đơn từ 299.000đ. Giao nhanh – nhận ngay tại nhà!</p>

              </div>
            </div>
            <div
              class="col-md-6 col-lg-4 d-lg-flex mb-4 mb-lg-0 pl-4"
              data-aos="fade-up"
              data-aos-delay="100"
            >
              <div class="icon mr-4 align-self-start">
                <span class="icon-refresh2"></span>
              </div>
              <div class="text">
               <h2 class="text-uppercase">Đổi Trả Dễ Dàng</h2>
<p>Không ưng ý? Bạn có thể đổi trả trong vòng 7 ngày. Nhanh ngọn lẹ, không rắc rối.</p>

              </div>
            </div>
            <div
              class="col-md-6 col-lg-4 d-lg-flex mb-4 mb-lg-0 pl-4"
              data-aos="fade-up"
              data-aos-delay="200"
            >
              <div class="icon mr-4 align-self-start">
                <span class="icon-help"></span>
              </div>
              <div class="text">
               <h2 class="text-uppercase">Hỗ Trợ Tận Tình</h2>
<p>Đội ngũ tư vấn luôn sẵn sàng giúp bạn chọn size, mix đồ và giải đáp thắc mắc.</p>

              </div>
            </div>
          </div>
        </div>
      </div>

<!-- Hành trình -->
<div class="site-section custom-border-bottom py-5 bg-light" data-aos="fade">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6 mb-4 mb-md-0">
        <div class="position-relative">
          <img src="images/blog_1.jpg" alt="Image" class="img-fluid rounded shadow" />
          <a href="https://vimeo.com/channels/staffpicks/93951774" class="position-absolute top-50 start-50 translate-middle popup-vimeo">
            <span class="icon-play fs-2 text-white bg-dark rounded-circle p-3"></span>
          </a>
        </div>
      </div>
      <div class="col-md-6">
        <h2 class="fw-bold mb-3">Hành Trình Của Chúng Tôi</h2>
        <p class="text-muted">
          Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius repellat, dicta at laboriosam...
        </p>
        <p class="text-muted">
          Accusantium dolor ratione maiores est deleniti nihil? Dignissimos est, sunt nulla illum...
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Đội ngũ -->
<div class="site-section bg-white py-5" data-aos="fade">
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-md-7 text-center">
        <h2 class="fw-bold text-black">Đội Ngũ </h2>
      </div>
    </div>
<div class="row">
  @foreach($members as $member)
  <div class="col-md-6 col-lg-3 mb-4">
    <div class="bg-light p-4 rounded shadow text-center h-100">
<img src="{{ asset('storage/' . $member->photo) }}" alt="Ảnh thành viên"
     class="mb-3 rounded-circle shadow-sm"
     style="width: 100px; height: 100px; object-fit: cover;">
      <h5 class="mb-1 fw-bold">{{ $member->name }}</h5>
      <p class="text-primary small">{{ $member->position }}</p>
      <p class="text-muted small">{{ $member->bio }}</p>
    </div>
  </div>
  @endforeach
</div>

  </div>
</div>

@endsection