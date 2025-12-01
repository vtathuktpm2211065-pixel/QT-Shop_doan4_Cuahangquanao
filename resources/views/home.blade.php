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
@if($behaviorBased->isNotEmpty())
<div class="recommended-products mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Sản phẩm gợi ý cho bạn</h2>
        <div class="section-line"></div>
    </div>
    <div class="row g-4">
        @foreach($behaviorBased as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card card h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="position-relative overflow-hidden" style="height: 220px;">
                        <img src="{{ asset('images/' . $product->image_url) }}" 
                             alt="{{ $product->name }}" 
                             class="card-img-top img-fluid h-100 object-fit-cover">
                        <div class="product-badge position-absolute top-0 end-0 m-2">
                            <span class="badge bg-danger">Gợi ý</span>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column p-3">
                        <h5 class="card-title product-name fw-semibold mb-2 text-truncate" 
                            title="{{ $product->name }}">
                            {{ $product->name }}
                        </h5>
                        <div class="mt-auto">
                            <p class="card-text product-price text-danger fw-bold fs-5 mb-2">
                                {{ number_format($product->price*1000,0,',','.') }} ₫
                            </p>
                            <a href="{{ route('chi_tiet', ['slug' => $product->slug]) }}" 
                               class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-eye me-2"></i>
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@if($categoryBased->isNotEmpty())
<div class="featured-products mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Sản phẩm nổi bật</h2>
        <div class="section-line"></div>
    </div>
    <div class="row g-4">
        @foreach($categoryBased as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card card h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="position-relative overflow-hidden" style="height: 220px;">
                        <img src="{{ asset('images/' . $product->image_url) }}" 
                             alt="{{ $product->name }}" 
                             class="card-img-top img-fluid h-100 object-fit-cover">
                        <div class="product-badge position-absolute top-0 end-0 m-2">
                            <span class="badge bg-warning text-dark">Nổi bật</span>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column p-3">
                        <h5 class="card-title product-name fw-semibold mb-2 text-truncate" 
                            title="{{ $product->name }}">
                            {{ $product->name }}
                        </h5>
                        <div class="mt-auto">
                            <p class="card-text product-price text-danger fw-bold fs-5 mb-2">
                                {{ number_format($product->price*1000,0,',','.') }} ₫
                            </p>
                            <a href="{{ route('chi_tiet', ['slug' => $product->slug]) }}" 
                               class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-eye me-2"></i>
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@if($recentProducts->count())
<h3>Sản phẩm bạn vừa xem</h3>
<div class="row g-4">
    @foreach($recentProducts as $item)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100">
                <img src="{{ asset('images/'.$item->image_url) }}" class="card-img-top" style="height:220px;object-fit:cover;">
                <div class="card-body">
                    <h5>{{ $item->name }}</h5>
                    <p class="text-danger">{{ number_format($item->price*1000,0,',','.') }} ₫</p>
                    <a href="{{ route('chi_tiet',['slug'=>$item->slug]) }}" class="btn btn-primary">Xem chi tiết</a>
                </div>
            </div>
        </div>
    @endforeach
</div>
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
    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
        position: relative;
        padding-bottom: 10px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #3498db, #2ecc71);
        border-radius: 2px;
    }
    
    .section-line {
        flex-grow: 1;
        height: 2px;
        background: #f8f9fa;
        margin-left: 20px;
    }
    
    .product-card {
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    
    .product-name {
        font-size: 1rem;
        color: #34495e;
        height: 48px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    .product-price {
        font-size: 1.25rem;
        color: #e74c3c !important;
    }
    
    .product-badge .badge {
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .btn-outline-primary, .btn-primary {
        border-radius: 8px;
        padding: 10px 15px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary {
        border: 2px solid #3498db;
        color: #3498db;
    }
    
    .btn-outline-primary:hover {
        background: #3498db;
        color: white;
        transform: scale(1.02);
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
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    @media (max-width: 768px) {
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
        
        .section-title {
            font-size: 1.5rem;
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



