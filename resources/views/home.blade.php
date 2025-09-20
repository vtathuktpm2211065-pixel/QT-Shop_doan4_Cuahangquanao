@extends('app')

@section('title', 'QT SHOP - Home')

@section('content')

@if ($errors->any())
  <div class="alert alert-danger" style="margin: 20px;">
      <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
      </ul>
  </div>
@endif

<div class="site-blocks-cover" data-aos="fade">
  <div class="container">
    <div class="row">
      <div class="col-md-6 ml-auto order-md-2 align-self-start">
        <div class="site-block-cover-content">
          <h2 class="sub-title">Chào các khách hàng thân yêu</h2>
          <h3>Sản phẩm hot</h3>
          <a href="{{ route('san-pham.noi-bat') }}" class="btn btn-black rounded-0">Shop Now</a>
        </div>
      </div>
      <div class="col-md-6 order-1 align-self-end">
        <img src="{{ asset('images/model_3.png') }}" alt="Image" class="img-fluid">
      </div>
    </div>
  </div>
</div>
<div class="site-section">
  <div class="container">
    <div class="title-section mb-5">
      <h2 class="text-uppercase"><span class="d-block">Discover</span> The Collections</h2>
    </div>
    <div class="row align-items-stretch">
      <div class="col-lg-8">
        <div class="product-item sm-height full-height bg-gray">
          <a href="{{ route('san-pham.cho-nu') }}" class="product-category">
            Women <span>10 items</span>
          </a>
          <img src="{{ asset('images/model_4.png') }}" alt="Image" class="img-fluid">
        </div>
      </div>
      <div class="col-lg-4">
        <div class="product-item sm-height bg-gray mb-4">
          <a href="{{ route('san-pham.cho-nam') }}" class="product-category">Men <span>10 items</span></a>
          <img src="{{ asset('images/model_5.png') }}" alt="Image" class="img-fluid">
        </div>
        <div class="product-item sm-height bg-gray">
          <a href="{{ route('san-pham.cho-tre-em') }}"  class="product-category">Trẻ em <span>10 items</span></a>
          <img src="{{ asset('images/children.jpg') }}" alt="Image" class="img-fluid">
        </div>
      </div>
    </div>
  </div>
</div>

<div class="site-section">
  <div class="container">
    <div class="row">
      <div class="title-section mb-5 col-12">
        <h2 class="text-uppercase">Sản phẩm phổ biến</h2>
      </div>
    </div>
    <div class="row">
      @foreach($products as $product)
      <div class="col-lg-4 col-md-6 item-entry mb-4">
        <div class="product-card shadow-sm rounded overflow-hidden">
          <a href="{{ route('chi_tiet', ['slug' => $product->slug]) }}">
            <img src="{{ asset('images/' . $product->image_url) }}"
                 alt="{{ $product->name }}"
                 class="product-img">
          </a>
          <div class="p-3 text-center">
            <h2 class="item-title fs-5 mb-2">
              <a href="{{ route('chi_tiet', ['slug' => $product->slug]) }}" class="text-dark text-decoration-none">
                {{ $product->name }}
              </a>
            </h2>
            <strong class="text-primary text-danger">
              {{ number_format($product->price, 3, ',', '.') }}₫
            </strong>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
<div class="site-section">
  <div class="container">
    <div class="row">
      <div class="title-section text-center mb-5 col-12">
        <h2 class="text-uppercase">Most Rated</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 block-3">
        <div class="nonloop-block-3 owl-carousel">
          @foreach ($mostRated as $product)
            <div class="item">
              <div class="item-entry">
                <a href="{{ route('chi_tiet', ['slug' => $product->slug]) }}" class="product-item md-height bg-gray d-block">
                  <img src="{{ asset('images/' . ($product->image_url ?? 'default.jpg')) }}" alt="{{ $product->name }}" class="img-fluid">
                </a>
                <h2 class="item-title"><a href="#">{{ $product->name }}</a></h2>
                <strong class="item-price">{{ number_format($product->price, 0, ',', '.') }}₫</strong>
                <div class="star-rating">
                  @for ($i = 0; $i < 5; $i++)
                    <span class="icon-star2 text-warning"></span>
                  @endfor
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>


<style>
  .product-img {
  width: 100%;
  height: 300px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card {
  border: 1px solid #f0f0f0;
  background: #fff;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.product-card:hover .product-img {
  transform: scale(1.05);
}

.item-title a:hover {
  color: #007bff;
}
.product-category {
    text-decoration: none !important;
}

.product-category span {
    text-decoration: none !important;
}
  </style>
@endsection



