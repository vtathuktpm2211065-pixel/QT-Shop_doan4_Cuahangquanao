@extends('app')

@section('title', 'Thanh toán đơn hàng')

@section('content')
@if(auth()->check())
    @if(auth()->user()->orders()->count() === 0)
        <div class="alert alert-success">
            🎉 Xin chào khách hàng mới! Bạn sẽ được <strong>giảm 10%</strong> cho đơn hàng đầu tiên trên 10.000₫ khi nhập voucher: <strong>Khach_hang_moi</strong>.
        </div>
    @else
        <div class="alert alert-info">
            🎁 Vào trang voucher để nhận thêm nhiều ưu đãi hấp dẫn!
        </div>
    @endif
@else
    <div class="alert alert-warning">
        🔐 <strong>Vui lòng đăng nhập</strong> để nhận nhiều voucher ưu đãi dành riêng cho bạn.
    </div>
@endif
<div class="container py-4">
    <h2 class="mb-4 text-center">🛒 Thanh toán đơn hàng</h2>

    @if ($cartItems->isEmpty())
        <p class="text-center text-muted">Giỏ hàng của bạn đang trống.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
    @else
        <form id="checkout-form" method="POST" action="{{ route('order.place') }}">
            @csrf
            <div class="row">
                <!-- Product List -->
                <div class="col-md-8">
                    <h4 class="mb-3">Sản phẩm</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Hình ảnh</th>
                                <th>Size</th>
                                <th>Màu</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)
                                <tr data-id="{{ $item->id }}">
                                    <td>{{ $item->product->name }}</td>
                                    <td><img src="{{ asset('images/' . ($item->product->image_url ?? 'default.jpg')) }}" width="60" alt="Product Image"></td>
                                    <td>{{ $item->size ?? 'Không chọn' }}</td>
                                    <td>{{ ucfirst($item->color ?? 'Không chọn') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                   <td>{{ number_format($item->product->price * 1000, 0, ',', '.') }}₫</td>
<td>{{ number_format($item->product->price * $item->quantity * 1000, 0, ',', '.') }}₫</td>

                                </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                    
@if($availableVouchers->isNotEmpty())
    <div class="mb-3">
        <label for="voucher_select">🎁 Chọn mã giảm giá:</label>
        <select id="voucher_select" class="form-control">
            <option value="">-- Chọn voucher --</option>
            @foreach($availableVouchers as $voucher)
                <option value="{{ $voucher->code }}">
                    {{ $voucher->code }} - 
                    {{ $voucher->discount_type === 'percent' ? $voucher->discount_value . '%' : number_format($voucher->discount_value) . 'đ' }}
                </option>
            @endforeach
        </select>
    </div>
@endif

                    <!-- Voucher -->
                    <div class="voucher-form mb-3">
                        <label>Mã giảm giá:</label>
                        <input type="text" name="voucher_code" id="voucher_code" value="{{ $voucher_code ?? '' }}" class="form-control d-inline-block w-auto">
                        <button type="button" id="apply-voucher" class="btn btn-danger">Áp dụng</button>
                        <p id="voucher-message" style="color: {{ session('voucher_discount') ? 'green' : 'red' }}">
                            @if (session('voucher_discount'))
                                Giảm: {{ number_format(session('voucher_discount')) }} VNĐ
                            @elseif (session('voucher_error'))
                                {{ session('voucher_error') }}
                            @endif
                        </p>
                    </div>

                   <h4>Tổng cộng: 
    <span id="total-amount" class="text-danger">
        {{ number_format(($cartItems->sum(fn($item) => $item->quantity * $item->product->price) - ($discount ?? 0)), 0, ',', '.') }} VNĐ
    </span>
</h4>

                </div>

                <!-- Order Information -->
                <div class="col-md-4">
                    <h4 class="mb-3">📦 Thông tin đơn hàng</h4>

                    <div class="form-group mb-3">
                        <label>Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" id="full_name" maxlength="40" pattern="[A-Za-zÀ-ỹ\s]+" class="form-control" required value="{{ old('full_name', Auth::user()->name ?? '') }}">
                        @error('full_name')
                            <small class="text-danger" id="name-error">{{ $message }}</small>
                        @else
                            <small class="text-danger" id="name-error"></small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="phone_number" id="phone_number" maxlength="10" pattern="[0-9]{10}" class="form-control" required value="{{ old('phone_number', $addresses->where('is_default', 1)->first()->phone_number ?? '') }}" placeholder="Nhập 10 số">
                        @error('phone_number')
                            <small class="text-danger" id="phone-error">{{ $message }}</small>
                        @else
                            <small class="text-danger" id="phone-error"></small>
                        @enderror
                    </div>

                    @foreach ($checkoutIds as $id)
                        <input type="hidden" name="checkout_ids[]" value="{{ $id }}">
                    @endforeach

                    <div class="form-group mb-3">
                        <label>Chọn địa chỉ đã lưu:</label>
                        <select name="address_id" id="address_id" class="form-select">
                            <option value="">➕ Thêm địa chỉ mới</option>
                            @foreach ($addresses as $address)
                                <option value="{{ $address->id }}" {{ old('address_id', $address->is_default ? $address->id : '') == $address->id ? 'selected' : '' }}>
                                    {{ trim("{$address->detail}, {$address->ward}, {$address->district}, {$address->province}", ', ') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="new-address" class="border p-3 rounded bg-light mb-3" style="display: {{ old('address_id') || $addresses->isEmpty() ? 'block' : 'none' }};">
                        <div class="form-group mb-3">
                            <label>Tỉnh/Thành <span class="text-danger">*</span></label>
                            <select name="province" id="province" class="form-select" {{ old('address_id') ? 'disabled' : '' }}>
                                <option value="">Chọn tỉnh/thành</option>
                            </select>
                            @error('province')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label>Quận/Huyện <span class="text-danger">*</span></label>
                            <select name="district" id="district" class="form-select" disabled>
                                <option value="">Chọn quận/huyện</option>
                            </select>
                            @error('district')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label>Xã/Phường <span class="text-danger">*</span></label>
                            <select name="ward" id="ward" class="form-select" disabled>
                                <option value="">Chọn xã/phường</option>
                            </select>
                            @error('ward')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label>Chi tiết (số nhà, đường) <span class="text-danger">*</span></label>
                            <input type="text" name="detail" id="detail" class="form-control" value="{{ old('detail') }}">
                            @error('detail')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <input type="hidden" name="province_name" id="province_name">
                        <input type="hidden" name="district_name" id="district_name">
                        <input type="hidden" name="ward_name" id="ward_name">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            <label class="form-check-label">Đặt làm địa chỉ mặc định</label>
                        </div>
                    </div>
<!-- Hiển thị phí ship -->

<table class="table">
    <tfoot>
        <tr>
    <td colspan="6" class="text-end"><strong>Phí vận chuyển:</strong></td>
    <td id="shipping_fee_display">{{ number_format($shippingFee, 0, ',', '.') }}₫</td>
</tr>
<tr>
    <td colspan="6" class="text-end"><strong>Tổng thanh toán:</strong></td>
    <td id="total_amount_display">{{ number_format($total  + $shippingFee - $discount, 0, ',', '.') }}₫</td>
</tr>

    </tfoot>
</table>
<!-- Giá trị tổng đơn hàng để gửi -->
<input type="hidden" id="order-total" value="{{ $total }}">

                    <div class="form-group mb-4">
                        <label>Phương thức thanh toán <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Thanh toán khi nhận hàng</option>
                            <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Thẻ tín dụng</option>
                            <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản ngân hàng</option>
                        </select>
                        @error('payment_method')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Modal xác nhận đặt hàng -->
                    <div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Xác nhận đặt hàng</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Vui lòng xác nhận lại thông tin trước khi đặt hàng.</p>
                                    <div class="mb-3">
                                        <label for="confirmPhone" class="form-label">Xác nhận số điện thoại:</label>
                                        <input type="text" name="phone_confirm" id="confirmPhone" class="form-control" placeholder="Nhập lại số điện thoại" maxlength="10">
                                        <div id="confirmPhoneError" class="text-danger small mt-1 d-none"></div>
                                    </div>
                                    @if(Auth::check())
                                        <div class="mb-3">
                                            <label for="confirmPassword" class="form-label">Mật khẩu:</label>
                                            <input type="password" id="confirmPassword" class="form-control" placeholder="Nhập mật khẩu">
                                            <div id="confirmPasswordError" class="text-danger small mt-1 d-none"></div>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button id="confirmPlaceOrderBtn" class="btn btn-primary">Xác nhận</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Nút đặt hàng -->
                    <button type="button" id="submit-order-btn" class="btn btn-danger w-100">🛍️ Đặt hàng ngay</button>

                </div>
            </div>
        </form>
    @endif
</div>

<form action="{{ route('vnpay.payment') }}" method="post">
                                            @csrf
                                           <input type="hidden" name="total" id="vnpay-total" value="{{ $total + $shippingFee - $discount }}">
                                            <button type="submit" class="btn btn-success check_out"
                                                name="redirect">Thanh toán VNPAY</button>
                                        </form>
 <form action="{{ url('/momo_payment') }}" method="post">
                                            @csrf
                                          <input type="hidden" name="total_momo" id="total_momo" value="{{ $total + $shippingFee - $discount }}">
                                            <button type="submit" class="btn btn-default check_out" name="payUrl">Thanh
                                                toán MOMO</button>
                                        </form>
                                       

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const baseTotal = {{ $total }};
    const discount = {{ $discount }};
    const innerCities = [ 'TP.HCM', 'Thành phố Hồ Chí Minh', 'Hồ Chí Minh', 'Cần Thơ'];

    function calculateShippingFeeJS(addressText) {
        let fee = 30000;
        for (let city of innerCities) {
            if (addressText.toLowerCase().includes(city.toLowerCase())) {
                fee = 15000;
                break;
            }
        }
        if (baseTotal >= 5000000) fee = 0;
        return fee;
    }
    function updateShippingFee() {
        let address = $('#shipping_address_select').val(); // hoặc field địa chỉ bạn đang dùng
        let csrfToken = '{{ csrf_token() }}';

        if (!address) return;

        $('#shipping-loading').removeClass('d-none');

        $.ajax({
            url: '{{ route("calculate.shipping") }}',
            method: 'POST',
            data: {
                address: address,
                _token: csrfToken
            },
            success: function (response) {
                if (response.shipping_fee !== undefined && response.total_amount !== undefined) {
                    // Cập nhật phí ship
                    $('#shipping_fee_display').html(
                        response.shipping_fee.toLocaleString('vi-VN') + '₫ <span id="shipping-loading" class="spinner-border spinner-border-sm d-none" role="status"></span>'
                    );

                    // Cập nhật tổng thanh toán cuối cùng
                    $('#total_amount_display').text(
                        response.total_amount.toLocaleString('vi-VN') + '₫'
                    );

                    // Cập nhật phần Tổng cộng phía trên (nếu có)
                    $('#total-amount').text(
                        response.total_amount.toLocaleString('vi-VN') + ' VNĐ'
                    );
                } else {
                    alert("Không thể tính phí vận chuyển.");
                }
            },
            error: function () {
                alert("Lỗi khi tính phí vận chuyển.");
            },
            complete: function () {
                $('#shipping-loading').addClass('d-none');
            }
        });
    }

    // Trigger khi chọn địa chỉ
    $('#shipping_address_select').on('change', function () {
        updateShippingFee();
    });

    // Gọi lại khi trang load (nếu cần)
    updateShippingFee();


   function updateFeeDisplay(fee) {
    const finalTotal = baseTotal - discount + fee;

    // ✅ Giao diện tổng cộng (trên đầu)
    $('#total-amount').text(finalTotal.toLocaleString('vi-VN') + ' VNĐ');

    // ✅ Giao diện phí ship trong bảng đơn hàng
    $('#shipping_fee_display').html(fee.toLocaleString('vi-VN') + '₫ <span id="shipping-loading" class="spinner-border spinner-border-sm d-none" role="status"></span>');

    // ✅ Giao diện tổng thanh toán cuối trong bảng
    $('#total_amount_display').text(finalTotal.toLocaleString('vi-VN') + '₫');

    // ✅ Giao diện chỗ khác (nếu có)
    $('#shipping-fee').text(fee.toLocaleString('vi-VN') + ' đ');
    $('#total-with-shipping').text(finalTotal.toLocaleString('vi-VN') + ' đ');

    // ✅ Hidden inputs nếu có
    $('#final-total').val(finalTotal);
    $('#shipping-fee-value').val(fee);
    $('#vnpay-total').val(finalTotal);
    $('#total_momo').val(finalTotal);
}

    $('#province_id').on('change', function () {
    let provinceName = $(this).find('option:selected').text();
    let total = $('#order-total').val();

    $.ajax({
        url: '/ajax/calculate-shipping',
        method: 'POST',
        data: {
            province: provinceName,
            total: total,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            $('#shipping-fee').text(response.fee.toLocaleString());
        }

    });
});

    $('#address_id').on('change', function () {
        const addressId = $(this).val();
        if (!addressId) {
            updateFeeDisplay(0);
            return;
        }

     $.ajax({
    url: '{{ route("calculate-shipping-ajax") }}',
    type: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({
        address_id: addressId,
        order_total: baseTotal - discount
    }),
    success: function (data) {
        console.log('Phí vận chuyển:', data.fee);
        updateFeeDisplay(data.fee);
    },
    error: function (xhr) {
        console.error('Không thể tính phí vận chuyển.', xhr.responseText);
        updateFeeDisplay(0);
    }
});


    }).trigger('change');

    $('#shipping-address').on('change', function () {
        const address = $(this).val();
        const fee = calculateShippingFeeJS(address);
        updateFeeDisplay(fee);
    });

    function submitOrder(password = null) {
    const form = $('#checkout-form');
    let formData = form.serializeArray();

    // Thêm confirm_password nếu có
    if (password) {
        formData.push({ name: 'confirm_password', value: password });
    }

    const $btn = $('#submit-order-btn');
    $btn.prop('disabled', true).text('Đang xử lý...');

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: $.param(formData),
        success: function(res) {
            // Chuyển trang thẳng tới chi tiết đơn hàng
            window.location.href = '/orders/' + res.order_id;
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.error || 'Lỗi xảy ra!');
        },
        complete: function() {
            $btn.prop('disabled', false).text('🛍️ Đặt hàng ngay');
            const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmOrderModal'));
            if (confirmModal) confirmModal.hide();
        }
    });
}


    // Xác nhận đặt hàng
    $('#confirmPlaceOrderBtn').click(function () {
        const phone = $('#phone_number').val().trim();
        const confirmPhone = $('#confirmPhone').val().trim();
        let hasError = false;

        $('#confirmPhoneError').addClass('d-none').text('');
        $('#confirmPasswordError').addClass('d-none').text('');

        if (!confirmPhone) {
            $('#confirmPhoneError').removeClass('d-none').text('Vui lòng nhập lại số điện thoại.');
            hasError = true;
        } else if (phone !== confirmPhone) {
            $('#confirmPhoneError').removeClass('d-none').text('Số điện thoại xác nhận không khớp.');
            hasError = true;
        }

        @if(Auth::check())
        const password = $('#confirmPassword').val().trim();
        if (!password) {
            $('#confirmPasswordError').removeClass('d-none').text('Vui lòng nhập mật khẩu.');
            hasError = true;
        }
        @endif

        if (hasError) return;

        @if(Auth::check())
            submitOrder(password);
        @else
            submitOrder();
        @endif
    });

    // Xử lý địa chỉ mới
    $('#address_id').change(function () {
        const showNew = !$(this).val();
        $('#new-address').toggle(showNew);
        $('#province, #district, #ward, #detail')
            .prop('required', showNew)
            .prop('disabled', !showNew);

        if (!showNew) {
            $('#province, #district, #ward, #detail').val('');
            $('#province_name, #district_name, #ward_name').val('');
        }
    }).trigger('change');

    // Gán địa danh
    $('#submit-order-btn').click(function () {
        const form = $('#checkout-form');
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        $('#province_name').val($('#province option:selected').text());
        $('#district_name').val($('#district option:selected').text());
        $('#ward_name').val($('#ward option:selected').text());

        const confirmModal = new bootstrap.Modal(document.getElementById('confirmOrderModal'));
        confirmModal.show();
    });

    // Tự động điền SĐT từ địa chỉ
    $('#address_id').change(function () {
        const id = $(this).val();
        if (id) {
            $.get(`/api/addresses/${id}`, function (data) {
                $('#phone_number').val(data.phone_number || '');
                $('#phone-error').text('');
            }).fail(() => alert('Không thể tải thông tin địa chỉ.'));
        } else {
            $('#phone_number').val('');
        }
    });

    // Tải tỉnh/huyện/xã
    $.get('/api/provinces', function (data) {
        $('#province').append(data.map(p => `<option value="${p.code}">${p.name}</option>`));
    });

    $('#province').change(function () {
        const code = $(this).val();
        $('#province_name').val($(this).find('option:selected').text());
        $('#district').prop('disabled', true).empty().append('<option>Đang tải...</option>');
        $('#ward').prop('disabled', true).empty();
        $.get(`/api/provinces/${code}`, function (data) {
            $('#district').empty().append('<option value="">Chọn quận/huyện</option>')
                .append(data.districts.map(d => `<option value="${d.code}">${d.name}</option>`))
                .prop('disabled', false);
        });
    });

    $('#district').change(function () {
        const code = $(this).val();
        $('#district_name').val($(this).find('option:selected').text());
        $('#ward').prop('disabled', true).empty().append('<option>Đang tải...</option>');
        $.get(`/api/districts/${code}`, function (data) {
            $('#ward').empty().append('<option value="">Chọn xã/phường</option>')
                .append(data.wards.map(w => `<option value="${w.code}">${w.name}</option>`))
                .prop('disabled', false);
        });
    });

    $('#ward').change(function () {
        $('#ward_name').val($(this).find('option:selected').text());
    });

  
    $('#phone_number').on('input', function () {
        const phone = $(this).val();
        const phoneRegex = /^[0-9]{10}$/;
        $('#phone-error').text(phone && !phoneRegex.test(phone) ? 'Số điện thoại phải là 10 chữ số.' : '');
    });
   $('#province, #district, #ward').on('change', function () {
    const province = $('#province option:selected').text().trim();
    const district = $('#district option:selected').text().trim();
    const ward = $('#ward option:selected').text().trim();

    if (!province || !district || !ward) return;

    const fullAddress = `${ward}, ${district}, ${province}`;
    const fee = calculateShippingFeeJS(fullAddress);

    updateFeeDisplay(fee);
});

    $('#full_name').on('input', function () {
        const name = $(this).val();
        const regex = /^[A-Za-zÀ-ỹ\s]+$/;
        $('#name-error').text(
            name && !regex.test(name) ? 'Họ và tên chỉ chứa chữ và dấu cách.'
            : name.length > 40 ? 'Họ và tên tối đa 40 ký tự.'
            : ''
        );
    });
});

$('#voucher_select').on('change', function () {
    const code = $(this).val();

    // Lấy tất cả ID sản phẩm trong checkout
    let selectedIds = [];
    $('tr[data-id]').each(function () {
        selectedIds.push($(this).data('id'));
    });

    if (!code) return; // nếu chưa chọn voucher thì thôi

    $.ajax({
        url: "{{ route('apply.voucher') }}",
        method: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            voucher_code: code,
            selected_ids: selectedIds
        },
        success: function (res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.message);
            }
        },
        error: function (xhr) {
            console.log(xhr.responseJSON);
            alert('Đã xảy ra lỗi khi áp dụng mã.');
        }
    });
});
$('#submit-vnpay-btn').click(function() {
    const form = $('#checkout-form');

    // Set payment_method = vnpay
    $('<input>').attr({
        type: 'hidden',
        name: 'payment_method',
        value: 'vnpay'
    }).appendTo(form);

    // Submit form chính
    form.submit();
});

$('#submit-momo-btn').click(function() {
    const form = $('#checkout-form');

    // Cập nhật payment_method
    if ($('#checkout-form input[name="payment_method"]').length) {
        $('#checkout-form input[name="payment_method"]').val('momo');
    } else {
        $('<input>').attr({
            type: 'hidden',
            name: 'payment_method',
            value: 'momo'
        }).appendTo(form);
    }

    // Cập nhật giá ship và tổng tiền cuối cùng
    const shippingFee = parseInt($('#shipping_fee_input').val()) || 0;
    const baseTotal = {{ $total }};
    const discount = {{ $discount }};
    $('#total_amount_final').val(baseTotal + shippingFee - discount);

    // Submit form chính
    form.attr('action', "{{ url('/momo_payment') }}");
    form.submit();
});

</script>

@endsection