@extends('app')

@section('title', 'Sản phẩm Trẻ em')

@section('styles')
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
    position: relative;
  }
  .product-card:hover .product-img {
    transform: scale(1.05);
  }
  .item-title a:hover {
    color: #007bff;
  }

  /* Cart sticker */
  .cart-sticker {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #fff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    box-shadow: 0 0 8px rgba(0,0,0,0.2);
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: opacity 0.3s ease, transform 0.3s ease;
    z-index: 10;
  }
  .product-card:hover .cart-sticker {
    display: flex;
    opacity: 1;
    transform: scale(1.05);
  }
  .cart-sticker:hover {
    transform: scale(1.2);
  }
</style>
@endsection

@section('content')
<div class="container py-4">
  <div class="row mb-4">
    <div class="title-section mb-3 col-12">
      <h2 class="text-uppercase text-center">Sản phẩm dành cho trẻ em</h2>
    </div>
  </div>

  @if ($sanPham->count() > 0)
  <div class="row">
    @foreach ($sanPham as $sp)
    <div class="col-lg-4 col-md-6 item-entry mb-4">
      <div class="product-card shadow-sm rounded overflow-hidden">
        <a href="{{ route('chi_tiet', ['slug' => $sp->slug]) }}">
          @if ($sp->image_url)
          <img src="{{ asset('images/' . $sp->image_url) }}" alt="{{ $sp->name }}" class="product-img">
          @else
          <img src="{{ asset('images/default.jpg') }}" alt="Không có ảnh" class="product-img">
          @endif
        </a>

        <a href="javascript:void(0);"  
           onclick="event.stopPropagation(); openCartModal('{{ $sp->slug }}');"
           class="cart-sticker"
           title="Thêm vào giỏ">
           <img src="{{ asset('images/cart.png') }}" alt="Thêm vào giỏ" style="width:24px; height:24px;">
        </a>

        <div class="p-3 text-center d-flex flex-column flex-grow-1">
          <h2 class="item-title fs-5 mb-2">
            <a href="{{ route('chi_tiet', ['slug' => $sp->slug]) }}" class="text-dark text-decoration-none">
              {{ $sp->name }}
            </a>
          </h2>
      
          <strong class="text-primary text-danger fs-5 mb-2">
            {{ number_format($sp->price * 1000, 0, ',', '.') }} VNĐ
          </strong>

        </div>
      </div>
    </div>
    @endforeach
  </div>
  @else
    <p class="text-center">Chưa có sản phẩm nào trong mục này.</p>
  @endif
</div>

{{-- MODAL --}}
<div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Thêm vào giỏ hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="modalCartForm">
          <input type="hidden" id="modalProductSlug">
          <div class="mb-3">
            <label class="form-label">Size:</label>
            <select id="modalSize" class="form-select" required></select>
          </div>
          <div class="mb-3">
            <label class="form-label">Màu sắc:</label>
            <select id="modalColor" class="form-select" required></select>
          </div>
          <div class="mb-3">
            <label class="form-label">Số lượng:</label>
            <input type="number" id="modalQuantity" class="form-control" value="1" min="1">
            <small class="form-text text-muted" id="stockNote"></small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="button" class="btn btn-primary" onclick="submitModalCart()">Thêm vào giỏ</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  const productVariants = @json($sanPham->mapWithKeys(fn($sp) => [$sp->slug => $sp->variants]));

  function openCartModal(slug) {
    const variants = productVariants[slug];
    if (!variants || variants.length === 0) {
        alert("Sản phẩm này chưa có biến thể.");
        return;
    }

    $('#modalProductSlug').val(slug);

    const sizes = [...new Set(variants.map(v => v.size))];
    $('#modalSize').html('<option value="">Chọn size</option>' + sizes.map(s => `<option value="${s}">${s}</option>`).join(''));

    const colors = [...new Set(variants.map(v => v.color))];
    $('#modalColor').html('<option value="">Chọn màu</option>' + colors.map(c => `<option value="${c}">${c}</option>`).join(''));

    $('#modalQuantity').val(1);
    $('#stockNote').text('');

    new bootstrap.Modal(document.getElementById('addToCartModal')).show();
  }

  $('#modalSize, #modalColor').on('change', function () {
    const slug = $('#modalProductSlug').val();
    const variants = productVariants[slug];
    const size = $('#modalSize').val();
    const color = $('#modalColor').val();

    if (size && color) {
        const variant = variants.find(v => v.size === size && v.color === color);
        if (variant) {
            $('#modalQuantity').attr('max', variant.stock_quantity);
            $('#stockNote').html(`Tồn kho: <strong>${variant.stock_quantity}</strong> sản phẩm`);
        } else {
            $('#stockNote').html(`<span class="text-danger">Không tìm thấy biến thể phù hợp!</span>`);
        }
    }
  });

  function submitModalCart() {
    const slug = $('#modalProductSlug').val();

    const size = $('#modalSize').val();
    const color = $('#modalColor').val();
    const quantity = parseInt($('#modalQuantity').val());

    if (!size || !color) {
        alert('Vui lòng chọn size và màu sắc!');
        return;
    }

    const variants = productVariants[slug];
    const variant = variants.find(v => v.size === size && v.color === color);

    if (!variant) {
        alert('Không tìm thấy biến thể phù hợp.');
        return;
    }

    if (quantity > variant.stock_quantity) {
        $('#stockNote').html(`<span class="text-danger">❗ Chỉ còn lại ${variant.stock_quantity} sản phẩm cho size ${size}, màu ${color}</span>`);
        return;
    }

    $.post('/cart/add/' + slug, {
        _token: '{{ csrf_token() }}',
        size: size,
        color: color,
        quantity: quantity
    })
    .done(function(response) {
        if (response.message === 'Đã thêm vào giỏ hàng') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '🛒 ' + response.message,
                showConfirmButton: false,
                timer: 2000
            });
            $('.cart-count').text(response.totalQuantity);
            bootstrap.Modal.getInstance(document.getElementById('addToCartModal')).hide();
        } else if (response.error) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: response.error
            });
        }
    })
    .fail(function(xhr) {
        const error = xhr.responseJSON?.error;
        if (xhr.status === 422 && error === 'Số lượng tồn kho không đủ') {
            const variant = productVariants[slug].find(v => v.size === size && v.color === color);
            if (variant) {
                $('#stockNote').html(`<span class="text-danger">❗ Chỉ còn lại ${variant.stock_quantity} sản phẩm cho size ${size}, màu ${color}</span>`);
            } else {
                $('#stockNote').html(`<span class="text-danger">Không tìm thấy tồn kho phù hợp!</span>`);
            }

            Swal.fire({
                icon: 'warning',
                title: 'Kho không đủ',
                text: 'Vui lòng chọn lại số lượng phù hợp.'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi không xác định',
                text: 'Mã lỗi: ' + xhr.status
            });
        }
    });
  }
</script>
@endsection