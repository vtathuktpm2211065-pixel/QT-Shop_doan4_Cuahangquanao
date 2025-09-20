@extends('app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="site-section">
  <div class="container">
    <h2 class="text-center mb-5">Giỏ hàng</h2>

    @if ($cartItems->isEmpty())
      <p class="text-center">Giỏ hàng của bạn đang trống.</p>
    @else
      <div class="mb-4">
        <table class="table text-center table-bordered">
          <thead class="table-light">
            <tr>
              <th class="text-center">
                <div class="form-check d-flex justify-content-center">
                  <input type="checkbox" id="select-all" class="form-check-input" checked>
                </div>
              </th>
              <th>Ảnh</th>
              <th>Sản phẩm</th>
              <th>Size</th>
              <th>Màu</th>
              <th>Giá</th>
              <th>Số lượng</th>
              <th>Tổng</th>
              <th>Xoá</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($cartItems as $item)
              @php
                $productVariants = $item->product->variants;
                $sizes = $productVariants->pluck('size')->unique();
                $colors = $productVariants->pluck('color')->unique();
              @endphp
              <tr data-id="{{ $item->id }}" 
                  data-price="{{ $item->product->price * 1000 }}" 
                  data-stock="{{ optional($item->variant)->stock_quantity }}">
                <td class="text-center align-middle">
                  <div class="form-check d-flex justify-content-center">
                    <input type="checkbox" class="form-check-input select-item" value="{{ $item->id }}" checked>
                  </div>
                </td>
                <td class="align-middle text-center">
                  <img src="{{ asset('images/' . $item->product->image_url) }}" width="70" class="img-thumbnail">
                </td>
                <td class="align-middle text-center"><strong>{{ $item->product->name }}</strong></td>

                {{-- Size --}}
                <td class="align-middle">
                  <select class="form-select form-select-sm size-select">
                    @foreach ($sizes as $size)
                      <option value="{{ $size }}" {{ optional($item->variant)->size == $size ? 'selected' : '' }}>
                        {{ $size }}
                      </option>
                    @endforeach
                  </select>
                </td>

                {{-- Màu --}}
                <td class="align-middle">
                  <select class="form-select form-select-sm color-select">
                    @foreach ($colors as $color)
                      <option value="{{ $color }}" {{ optional($item->variant)->color == $color ? 'selected' : '' }}>
                        {{ $color }}
                      </option>
                    @endforeach
                  </select>
                </td>

                <td class="align-middle text-center">
                  {{ number_format($item->product->price * 1000, 0, ',', '.') }}₫
                </td>

                <td class="align-middle text-center">
                  <div class="input-group justify-content-center">
                    <button class="btn btn-outline-secondary btn-sm btn-decrease" type="button">-</button>
                    <input type="number" 
                      class="form-control text-center quantity-input" 
                      value="{{ $item->quantity }}" 
                      min="1" 
                      max="{{ optional($item->variant)->stock_quantity ?? 1 }}" 
                      data-old="{{ $item->quantity }}"
                      style="width: 60px;">
                    <button class="btn btn-outline-secondary btn-sm btn-increase" type="button">+</button>
                  </div>
                </td>

                <td class="align-middle text-center item-total">
                  {{ number_format($item->product->price * $item->quantity * 1000, 0, ',', '.') }}₫
                </td>

                <td class="align-middle text-center">
                  <button class="btn btn-danger btn-sm btn-remove">X</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="row">
        <div class="col-md-4 offset-md-8 mt-4 mt-md-0">
          <div class="border p-4 rounded shadow-sm bg-light h-100">
            <h5 class="text-center border-bottom pb-2 mb-3">TỔNG ĐƠN</h5>
            <p class="text-end">Thành tiền: <strong id="thanh-tien"></strong></p>
            <p class="text-end">Tổng cộng: <strong id="tong-cong"></strong></p>

            <form id="checkout-form" method="POST" action="{{ route('checkout.process') }}" class="mt-4">
              @csrf
              <input type="hidden" name="selected_ids" id="selected-ids" value="{{ $cartItems->pluck('id')->implode(',') }}">
              <button type="submit" class="btn btn-danger w-100">Tiến hành thanh toán</button>
            </form>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>

<style>
.btn-danger {
  background-color: #f54670 !important;
  border-color: #f54670 !important;
  font-weight: bold;
}
.btn-outline-danger {
  color: #f54670 !important;
  border-color: #f54670 !important;
  font-weight: bold;
}
.btn-outline-danger:hover {
  background-color: #f54670 !important;
  color: #fff !important;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('success'))
<script>
Swal.fire({
  toast: true,
  position: 'top-end',
  icon: 'success',
  title: @json(session('success')),
  showConfirmButton: false,
  timer: 2500,
  timerProgressBar: true,
  customClass: { popup: 'colored-toast' }
});
</script>
@endif

<script>
$(document).ready(function () {
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  // Cập nhật selected_ids
  function updateSelectedIds() {
    const selectedIds = $('.select-item:checked').map(function () { return $(this).val(); }).get().join(',');
    $('#selected-ids').val(selectedIds);
    updateTotal();
  }

  // Cập nhật tổng tiền
  function updateTotal() {
    let total = 0;
    $('input.select-item:checked').each(function () {
      const row = $(this).closest('tr');
      const price = parseFloat(row.data('price')) || 0;
      const qty = parseInt(row.find('.quantity-input').val()) || 0;
      total += price * qty;

      // Cập nhật tổng của từng dòng
      row.find('.item-total').text((price * qty).toLocaleString('vi-VN') + '₫');
    });
    $('#thanh-tien').text(total.toLocaleString('vi-VN') + '₫');
    $('#tong-cong').text(total.toLocaleString('vi-VN') + '₫');
  }

  $('#select-all').on('change', function () {
    $('.select-item').prop('checked', $(this).is(':checked'));
    updateSelectedIds();
  });

  $('.select-item').on('change', updateSelectedIds);

  $('.quantity-input').on('focus', function () { $(this).data('old', $(this).val()); });

  $('.btn-increase').click(function () {
    const row = $(this).closest('tr');
    const input = row.find('.quantity-input');
    let qty = parseInt(input.val()) || 1;
    const max = parseInt(input.attr('max')) || 9999;
    if (qty < max) input.val(qty+1).trigger('change');
    else Swal.fire('Thông báo', 'Chỉ còn tối đa ' + max + ' sản phẩm trong kho.', 'warning');
  });

  $('.btn-decrease').click(function () {
    const row = $(this).closest('tr');
    const input = row.find('.quantity-input');
    let qty = parseInt(input.val()) || 1;
    if (qty > 1) input.val(qty-1).trigger('change');
  });

  $('.quantity-input').change(function () {
    const input = $(this);
    const row = input.closest('tr');
    const id = row.data('id');
    const oldQty = parseInt(input.data('old')) || 1;
    const newQty = parseInt(input.val());
    const maxStock = parseInt(input.attr('max')) || 9999;

    if (isNaN(newQty) || newQty < 1 || newQty > maxStock) {
      Swal.fire('Lỗi', 'Số lượng không hợp lệ.', 'error');
      input.val(oldQty);
      return;
    }

    $.post('/cart/update/' + id, { quantity: newQty })
      .done(function (res) {
        $('.cart-count').text(res.totalQuantity);
        updateTotal();
      })
      .fail(function () {
        Swal.fire('Lỗi', 'Có lỗi xảy ra.', 'error');
        input.val(oldQty);
      });
  });

  $('.btn-remove').click(function () {
    const row = $(this).closest('tr');
    const id = row.data('id');
    Swal.fire({
      title: 'Bạn chắc chắn?',
      text: 'Sản phẩm sẽ bị xoá khỏi giỏ hàng!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Xoá',
      cancelButtonText: 'Hủy'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({ url: '/cart/remove/' + id, type: 'DELETE',
          success: function(res){
            Swal.fire('Đã xoá', res.message, 'success');
            row.remove();
            $('.cart-count').text(res.totalQuantity);
            updateSelectedIds();
          }
        });
      }
    });
  });

  $('.size-select, .color-select').on('change', function () {
    const row = $(this).closest('tr');
    const id = row.data('id');
    const size = row.find('.size-select').val();
    const color = row.find('.color-select').val();
    $.post('/cart/update-variant/' + id, { _token: '{{ csrf_token() }}', size, color })
      .done(function(res){ Swal.fire('Thành công', res.message, 'success'); updateTotal(); })
      .fail(function(xhr){ Swal.fire('Lỗi', xhr.responseJSON?.error || 'Có lỗi xảy ra.', 'error'); });
  });

  // Load lần đầu
  updateSelectedIds();

  // Submit form
  $('#checkout-form').on('submit', function () { updateSelectedIds(); });
});
</script>
@endsection
