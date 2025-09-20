{{-- NQ note --}}
@extends('app')

@section('title', 'Tìm kiếm sản phẩm')

@section('styles')
<style>
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 12px;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .card-img-top {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        height: 300px;
        object-fit: cover;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: bold;
    }

    .card-text {
        color: #6c757d;
        font-size: 0.95rem;
    }

    .price-text {
        font-size: 1.1rem;
        font-weight: bold;
        color: #dc3545;
    }

    .search-title {
        font-weight: 600;
        font-size: 1.8rem;
        margin-bottom: 1rem;
    }

    .empty-result {
        font-size: 1.2rem;
        color: #999;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <h1 class="search-title text-center">Kết quả tìm kiếm cho: <span class="text-primary">"{{ $query }}"</span></h1>

    @if ($sanPham->count() > 0)
    <div class="row g-4">
        @foreach ($sanPham as $sp)
        <div class="col-md-4">
            <div class="card product-card h-100 shadow-sm">
                <a href="{{ route('chi_tiet', ['slug' => $sp->slug]) }}" style="text-decoration: none; color: inherit;">
                    @if ($sp->image_url)
                        <img src="{{ asset('images/' . $sp->image_url) }}" class="card-img-top" alt="{{ $sp->name }}">
                    @else
                        <img src="{{ asset('images/default.jpg') }}" class="card-img-top" alt="Không có ảnh">
                    @endif

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $sp->name }}</h5>

                        <p class="card-text text-truncate" style="min-height: 60px;">
                            {{ $sp->description ?? 'Chưa có mô tả' }}
                        </p>

                        <p class="price-text mt-2">{{ number_format($sp->price, 3, ',', '.') }} VNĐ</p>

                        <a href="{{ route('chi_tiet', ['slug' => $sp->slug]) }}" class="btn btn-outline-primary mt-auto w-100">
                            Xem chi tiết
                        </a>
                    </div>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
   
    @else
        <p class="text-center empty-result">Không tìm thấy sản phẩm nào khớp với "{{ $query }}".</p>
    @endif
</div>
@endsection
