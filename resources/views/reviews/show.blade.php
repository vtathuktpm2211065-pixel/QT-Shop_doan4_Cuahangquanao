@extends('app')

@section('title', 'Đánh giá sản phẩm')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-semibold"> {{ $product->name }}</h2>
    </div>

    @if($product->reviews->count())
        <div class="row">
            @foreach($product->reviews as $review)
                <div class="col-md-6 col-lg-4 mb-4 d-flex">
                    <div class="card border-0 shadow-sm w-100">
                        <div class="card-body p-4 d-flex flex-column">
                            {{-- Avatar + Tên --}}
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $review->user->avatar ? asset('storage/' . $review->user->avatar) : asset('images/default-avatar.jpg') }}"
                                     class="rounded-circle me-3" width="50" height="50" alt="Avatar">
                                <div>
                                    <h6 class="mb-0">{{ $review->user->name }}</h6>
                                    <small class="text-muted">⭐ {{ $review->rating }}/5</small>
                                </div>
                            </div>

                            {{-- Sao --}}
                            <div class="mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>

                            {{-- Nội dung --}}
                            <p class="flex-grow-1">{{ $review->comment }}</p>

                            {{-- Ảnh đính kèm --}}
                            @if($review->image)
                                <img src="{{ asset('storage/' . $review->image) }}"
                                     class="img-fluid rounded border mt-2"
                                     style="max-height: 150px; object-fit: cover;" alt="Ảnh đánh giá">
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-muted">
            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
        </div>
    @endif
</div>
@endsection
