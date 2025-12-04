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
@if($combined->count())
<div class="container mt-5 pt-4 pb-5">
    <!-- Tiêu đề section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <div class="section-divider section-divider-left"></div>
                <h2 class="section-title text-center px-4 mb-0 fw-bold">CÓ THỂ BẠN SẼ THÍCH</h2>
                <div class="section-divider section-divider-right"></div>
            </div>
            <p class="text-muted text-center">Sản phẩm được đề xuất dựa trên sở thích và hành vi của bạn</p>
        </div>
    </div>

    <div class="row g-4">
        @foreach($combined as $product)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
                <div class="product-card-wrapper position-relative">
                    <!-- Card sản phẩm -->
                    <div class="product-card card border-0 h-100 overflow-hidden shadow-sm">
                        <!-- Badge theo nguồn -->
                        <div class="product-badge position-absolute start-0 top-0 z-2">
                            @if($product->source == 'behavior')
                                <span class="badge bg-gradient-behavior"></span>
                            @elseif($product->source == 'category')
                                <span class="badge bg-gradient-category"></span>
                            @elseif($product->source == 'recent')
                                <span class="badge bg-gradient-recent"></span>
                            @elseif($product->source == 'top_rated')
                                <span class="badge bg-gradient-top-rated"></span>
                            @elseif($product->source == 'collab')
                                <span class="badge bg-gradient-collab"></span>
                            @endif
                        </div>
                        
                  
                        <!-- Hình ảnh sản phẩm -->
                        <div class="product-image-wrapper position-relative overflow-hidden">
                            <a href="{{ route('chi_tiet', ['slug' => $product->slug]) }}" class="text-decoration-none">
                                <img src="{{ asset('images/' . $product->image_url) }}" 
                                     class="product-img card-img-top img-fluid" 
                                     alt="{{ $product->name }}"
                                     onerror="this.src='https://placehold.co/600x400/f8f9fa/6c757d?text=No+Image'">
                                <div class="image-overlay"></div>
                            </a>
                            
                        </div>

                        <!-- Thông tin sản phẩm -->
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-2">
                                <a href="{{ route('chi_tiet', ['slug' => $product->slug]) }}" class="text-decoration-none">
                                    <h5 class="product-name card-title fw-semibold text-truncate-2 lh-sm text-dark mb-2">
                                        {{ $product->name }}
                                    </h5>
                                </a>
                            </div>
                            
                            <!-- Giá sản phẩm -->
                            <div class="mt-auto">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <p class="product-price text-danger fw-bold fs-4 mb-0">
                                            {{ number_format($product->price*1000,0,',','.') }} ₫
                                        </p>
                                        @if($product->price > 500)
                                            <p class="product-old-price text-muted text-decoration-line-through small mb-0">
                                                {{ number_format($product->price*1000*1.2,0,',','.') }} ₫
                                            </p>
                                        @endif
                                    </div>
                                    @if($product->source == 'top_rated')
                                        <div class="rating-badge">
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-star me-1"></i> 4.8
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Nút hành động -->
                                <div class="d-grid">
                                    <a href="{{ route('chi_tiet', ['slug' => $product->slug]) }}" 
                                       class="btn btn-outline-primary btn-hover-fill rounded-pill py-2 fw-medium">
                                        <i class="fas fa-shopping-cart me-2"></i> Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

<!-- Thêm Font Awesome cho icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endif


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
    :root {
        --primary-color: #242529;
        --secondary-color: #e7e6eb;
        --accent-color: #f72585;
        --light-bg: #f8f9fa;
    }
    
    /* Tiêu đề section */
    .section-divider {
        height: 2px;
        width: 50px;
        background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
    }
    
    .section-divider-left {
        border-radius: 2px 0 0 2px;
    }
    
    .section-divider-right {
        border-radius: 0 2px 2px 0;
    }
    
    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
        position: relative;
        padding-bottom: 10px;
        letter-spacing: 0.5px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        border-radius: 2px;
    }
    
    /* Card sản phẩm */
    .product-card-wrapper {
        transition: transform 0.3s ease;
    }
    
    .product-card-wrapper:hover {
        transform: translateY(-8px);
    }
    
    .product-card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: white;
        border: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .product-card:hover {
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15) !important;
        border-color: #ddd;
    }
    
    /* Hình ảnh sản phẩm */
    .product-image-wrapper {
        height: 240px;
        background-color: #f8f9fa;
        border-radius: 12px 12px 0 0;
        position: relative;
        overflow: hidden;
    }
    
    .product-img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .product-card:hover .product-img {
        transform: scale(1.05);
    }
    
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, transparent 60%, rgba(0,0,0,0.1));
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .product-card:hover .image-overlay {
        opacity: 1;
    }
    
    /* Badge */
    .product-badge .badge {
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border: none;
    }
    
    .bg-gradient-behavior {
        background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    }
    
    .bg-gradient-category {
        background: linear-gradient(135deg, #ffd166, #ffb347);
        color: #333 !important;
    }
    
    .bg-gradient-recent {
        background: linear-gradient(135deg, #8a8d93, #6c757d);
    }
    
    .bg-gradient-top-rated {
        background: linear-gradient(135deg, #06d6a0, #05b384);
    }
    
    .bg-gradient-collab {
        background: linear-gradient(135deg, #118ab2, #0a6c8f);
    }
    
   
    
    .btn-favorite:hover {
        background: white;
        color: var(--accent-color);
        transform: scale(1.1);
    }
    
    /* Tên sản phẩm */
    .product-name {
        font-size: 1rem;
        color: #34495e;
        height: 48px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        margin-bottom: 0.5rem;
        transition: color 0.2s ease;
    }
    
    .product-name:hover {
        color: var(--primary-color);
    }
    
    /* Giá sản phẩm */
    .product-price {
        font-size: 1.25rem;
        color: #e74c3c !important;
        font-weight: 700;
    }
    
    .product-old-price {
        font-size: 0.85rem;
        color: #95a5a6;
        text-decoration: line-through;
    }
    
    /* Nút hành động */
    .btn-outline-primary, .btn-primary {
        border-radius: 8px;
        padding: 10px 15px;
        font-weight: 600;
        transition: all 0.3s ease;
        border-width: 1.5px;
    }
    
    .btn-outline-primary {
        border: 2px solid #3498db;
        color: #3498db;
        background: transparent;
    }
   
    
    .btn-primary {
        background: linear-gradient(135deg, #3498db, #2ecc71);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #2980b9, #27ae60);
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }
    
   
    
    .quick-view-btn {
        transition: all 0.3s ease;
        transform: translateY(10px);
        z-index: 3;
        opacity: 0;
    }
    
    /* Category link */
    .product-category {
        text-decoration: none !important;
    }
    
    .product-category span {
        text-decoration: none !important;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .product-image-wrapper {
            height: 200px;
        }
        
        .section-title {
            font-size: 1.5rem;
        }
        
        .col-lg-3 {
            width: 50%;
        }
        
        .product-name {
            font-size: 0.9rem;
            height: 40px;
        }
        
        .product-price {
            font-size: 1.1rem;
        }
    }
    
    @media (max-width: 576px) {
        .col-lg-3 {
            width: 100%;
        }
        
        .product-card {
            max-width: 300px;
            margin: 0 auto;
        }
    }
</style>
@endsection



