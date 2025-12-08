@extends('app')

@section('title', $product->name)

@section('content')
<div class="container mt-5">
    <div class="row">
        {{-- Ảnh sản phẩm --}}
        <div class="col-md-5">
            <img src="{{ asset('images/' . $product->image_url) }}" alt="{{ $product->name }}" class="img-fluid rounded shadow-sm">
        </div>

        {{-- Thông tin sản phẩm --}}
        <div class="col-md-7">
            <h2 class="mb-3 text-center fw-bold text-dark">{{ $product->name }}</h2>
            <h3 class="text-danger fw-bold mb-3">Giá bán: {{ number_format($product->price * 1000, 0, ',', '.') }} VNĐ</h3>


            <p><strong>Mô tả:</strong></p>
            <p>{{ $product->description }}</p>

            <p><strong>Giới tính:</strong> {{ ucfirst($product->gioi_tinh) }}</p>
            <p><strong>Phổ biến:</strong> {{ $product->pho_bien ? 'Có' : 'Không' }}</p>
            <p><strong>Nổi bật:</strong> {{ $product->noi_bat ? 'Có' : 'Không' }}</p>

            @if($sizes)
                <div class="mb-3">
                    <label class="form-label"><strong>Chọn Size:</strong></label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($sizes as $size)
                            <input type="radio" class="btn-check" name="size" id="size-{{ $size }}" value="{{ $size }}" autocomplete="off" required>
                            <label class="btn btn-outline-danger" for="size-{{ $size }}">{{ $size }}</label>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($colors)
                <div class="mb-4">
                    <label class="form-label"><strong>Chọn Màu sắc:</strong></label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($colors as $color)
                            @php
                                $colorClasses = [
                                    'hồng' => 'background-color: rgb(248, 128, 170);',
                                    'vàng' => 'background-color: hsl(54, 97.80%, 64.10%);',
                                    'xanh' => 'background-color: hsl(244, 90.20%, 52.00%);',
                                    'đen'  => 'background-color: rgb(24, 25, 26);',
                                    'trắng' => 'background-color: rgb(245, 247, 248);',
                                    'đỏ'    => 'background-color: #e53935;',
                                    'nâu'   => 'background-color: rgb(104, 75, 65);',
                                    'cam' => 'background-color: hsl(29, 85.70%, 58.80%);',
                                    'tím' => 'background-color: hsl(244, 14.90%, 35.50%);',
                                    'xanh_xám' => 'background-color: hsl(244, 25.40%, 65.30%);',
                                    'xanh_đen' => 'background-color: hsl(244, 20.30%, 27.10%);',
                                    'xanh_trắng' => 'background-color: hsl(243, 71.40%, 89.00%);',
                                    'trắng_hồng' => 'background-color: hsl(314, 68.00%, 90.20%);',
                                    'hồng_trắng' => 'background-color: hsl(332, 67.20%, 88.00%);',
                                    'nâu_be' => 'background-color: hsl(0, 3.60%, 32.90%);',
                                    'xám_xanh' => 'background-color: hsl(244, 27.50%, 44.90%);',
                                ];
                                $style = $colorClasses[strtolower($color)] ?? 'background-color: #e0e0e0;';
                            @endphp

                            <input type="radio" class="btn-check" name="color" id="color-{{ $color }}" value="{{ $color }}" autocomplete="off" required>
                            <label class="btn" for="color-{{ $color }}" style="{{ $style }}">{{ ucfirst($color) }}</label>
                        @endforeach
                    </div>
                </div>
            @endif
            

            <div class="mb-4">
                <label for="quantityInput" class="form-label"><strong>Số lượng:</strong></label>
                <input type="number" id="quantityInput" class="form-control w-auto" value="1" min="1">
            </div>

            <button type="button" class="btn btn-primary btn-lg" onclick="addToCartSlug('{{ $product->slug }}')">
                Thêm vào giỏ hàng
            </button>
           


        </div>
    </div>
</div>
<div id="cart-alert" class="alert alert-success" style="display: none; position: fixed; top: 80px; right: 20px; z-index: 9999;">
  Đã thêm vào giỏ hàng!
</div>
{{-- Đánh giá sản phẩm --}}
<div class="container py-5">
    <h3 class="fw-semibold mb-4 text-center">Đánh giá sản phẩm</h3>

    @if($product->reviews->count())
        {{-- Tổng điểm trung bình --}}
        <div class="bg-warning bg-opacity-10 p-4 rounded mb-4 text-center">
            <h2 class="fw-bold mb-1">
                {{ number_format($product->reviews->avg('rating'), 1) }} 
                <small class="text-muted fs-6">/ 5.0</small>
            </h2>
            <div class="mb-2">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= round($product->reviews->avg('rating')) ? 'text-warning' : 'text-muted' }}"></i>
                @endfor
            </div>
            <small class="text-muted">{{ $product->reviews->count() }} đánh giá đã mua hàng</small>
        </div>

        {{-- Danh sách review --}}
        <div class="row">
            @foreach($product->reviews as $review)
                <div class="col-12 mb-4">

                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            {{-- Tên + ngày --}}
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">{{ $review->user->name }}</h6>
                                <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                            </div>

                            {{-- Số sao --}}
                            <div class="mb-2">
                                <small class="text-warning">⭐ {{ $review->rating }}/5</small>
                                <div>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                            </div>

                            {{-- Nội dung --}}
                            <p>{{ $review->comment }}</p>


{{-- Nội dung --}}
<p class="flex-grow-1">{{ $review->comment }}</p>

{{-- Ảnh đính kèm --}}
@if($review->images && $review->images->count())
    <div class="d-flex flex-wrap gap-2 mt-2">
        @foreach($review->images as $img)
            <img src="{{ asset('storage/' . $img->image) }}"
                 class="review-thumb rounded border"
                 style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
        @endforeach
    </div>
@endif

                            {{-- Phản hồi từ shop --}}
                            @if($review->admin_reply)
                                <div class="mt-3 p-3 bg-light border rounded">
                                    <strong>Phản hồi từ shop: </strong>
                                    <p class="mb-0">{{ $review->admin_reply }}</p>
                                </div>
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

{{-- CSS nhỏ --}}
<style>
.review-thumb {
    width: 80px;
    height: 80px;
    object-fit: cover;
    cursor: pointer;
    border-radius: 8px;
    border: 1px solid #ddd;
}
</style>

@if($relatedProducts->count())
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-center flex-grow-1">Sản phẩm cùng danh mục</h3>
        <div class="section-line ms-3"></div>
    </div>
    <div class="row g-4">
        @foreach($relatedProducts as $item)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card card h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="position-relative overflow-hidden" style="height: 220px;">
                        <img src="{{ asset('images/' . $item->image_url) }}" 
                             alt="{{ $item->name }}" 
                             class="card-img-top img-fluid h-100 object-fit-cover">
                    </div>
                    <div class="card-body d-flex flex-column p-3">
                        <h5 class="card-title product-name fw-semibold mb-2 text-truncate" 
                            title="{{ $item->name }}">
                            {{ $item->name }}
                        </h5>
                        <div class="mt-auto">
                            <p class="card-text product-price text-danger fw-bold fs-5 mb-2">
                                {{ number_format($item->price*1000,0,',','.') }} ₫
                            </p>
                            <a href="{{ route('chi_tiet', ['slug' => $item->slug]) }}" 
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
@if($recommendations->count())
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-center flex-grow-1">Có thể bạn thích</h3>
        <div class="section-line ms-3"></div>
    </div>
    <div class="row g-4">
        @foreach($recommendations as $item)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card card h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="position-relative overflow-hidden" style="height: 220px;">
                        <img src="{{ asset('images/' . $item->image_url) }}" 
                             alt="{{ $item->name }}" 
                             class="card-img-top img-fluid h-100 object-fit-cover">
                    </div>
                    <div class="card-body d-flex flex-column p-3">
                        <h5 class="card-title product-name fw-semibold mb-2 text-truncate" 
                            title="{{ $item->name }}">
                            {{ $item->name }}
                        </h5>
                        <div class="mt-auto">
                            <p class="card-text product-price text-danger fw-bold fs-5 mb-2">
                                {{ number_format($item->price*1000,0,',','.') }} ₫
                            </p>
                            <a href="{{ route('chi_tiet', ['slug' => $item->slug]) }}" 
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


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const variantStocks = @json($variantStocks);

    // Khi thay đổi size hoặc color
    $('input[name="size"], input[name="color"]').on('change', function () {
        const size = $('input[name="size"]:checked').val();
        const color = $('input[name="color"]:checked').val();

        if (size && color) {
            const key = color.toLowerCase() + '_' + size.toUpperCase(); // ✅ Chuẩn key
            const maxQty = variantStocks[key] || 0;

            if (maxQty === 0) {
                alert(`Sản phẩm size ${size}, màu ${color} đã hết hàng!`);
                $('#quantityInput').val(0).prop('disabled', true);
            } else {
                $('#quantityInput').val(1).attr('max', maxQty).prop('disabled', false);
            }
        }
    });

    // Khi thay đổi số lượng
    $('#quantityInput').on('input', function () {
        const size = $('input[name="size"]:checked').val();
        const color = $('input[name="color"]:checked').val();
        const quantity = parseInt($(this).val()) || 1;

        if (size && color) {
            const key = color.toLowerCase() + '_' + size.toUpperCase();
            const maxQty = variantStocks[key] || 0;

            if (quantity > maxQty) {
                alert(`Chỉ còn ${maxQty} sản phẩm size ${size}, màu ${color} trong kho.`);
                $(this).val(maxQty);
            } else if (quantity < 1) {
                $(this).val(1);
            }
        }
    });

    // Thêm vào giỏ hàng
    function addToCartSlug(slug) {
        const size = $('input[name="size"]:checked').val();
        const color = $('input[name="color"]:checked').val();
        const quantity = parseInt($('#quantityInput').val());

        if (!size || !color) {
            alert('Vui lòng chọn kích thước và màu sắc!');
            return;
        }

        const key = color.toLowerCase() + '_' + size.toUpperCase();
        const maxQty = variantStocks[key] || 0;

        if (quantity > maxQty || maxQty === 0) {
            alert(`Số lượng yêu cầu vượt tồn kho. Chỉ còn ${maxQty} sản phẩm.`);
            return;
        }

        $.post('/cart/add/' + slug, {
            _token: '{{ csrf_token() }}',
            size: size,
            color: color,
            quantity: quantity
        }).done(function (response) {
            if (response.message === 'Đã thêm vào giỏ hàng') {
                $('#cart-alert').stop(true, true).fadeIn().delay(3000).fadeOut();
                if (response.totalQuantity !== undefined) {
                    $('.cart-count').text(response.totalQuantity);
                }
            } else if (response.error) {
                alert(response.error);
            }
        }).fail(function (xhr) {
            alert('Có lỗi xảy ra, vui lòng thử lại. Mã lỗi: ' + xhr.status);
        });
    }

    // Mua ngay
    function buyNow(slug) {
        const size = $('input[name="size"]:checked').val();
        const color = $('input[name="color"]:checked').val();
        const quantity = parseInt($('#quantityInput').val());

        if (!size || !color) {
            alert('Vui lòng chọn kích thước và màu sắc!');
            return;
        }

        const key = color.toLowerCase() + '_' + size.toUpperCase();
        const maxQty = variantStocks[key] || 0;

        if (quantity > maxQty || maxQty === 0) {
            alert(`Số lượng yêu cầu vượt tồn kho. Chỉ còn ${maxQty} sản phẩm.`);
            return;
        }

        $.post('/buy-now/' + slug, {
            _token: '{{ csrf_token() }}',
            size: size,
            color: color,
            quantity: quantity
        }).done(function (response) {
            if (response.redirect) {
                window.location.href = response.redirect;
            } else {
                alert('Không thể chuyển đến trang thanh toán.');
            }
        }).fail(function (xhr) {
            alert(xhr.responseJSON?.error || 'Có lỗi xảy ra, vui lòng thử lại.');
        });
    }
</script>


@endsection


