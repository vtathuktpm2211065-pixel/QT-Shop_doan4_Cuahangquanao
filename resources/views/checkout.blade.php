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
        <!-- Form chính cho COD -->
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

                    <!-- Thông tin khách hàng chung -->
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
                        <!-- Form địa chỉ mới (giữ nguyên) -->
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

                    <!-- Hiển thị phí ship và tổng tiền -->
                    <table class="table">
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end"><strong>Phí vận chuyển:</strong></td>
                                <td id="shipping_fee_display">{{ number_format($shippingFee, 0, ',', '.') }}₫</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end"><strong>Tổng thanh toán:</strong></td>
                                <td id="total_amount_display">{{ number_format($total + $shippingFee - $discount, 0, ',', '.') }}₫</td>
                            </tr>
                        </tfoot>
                    </table>

                    <input type="hidden" id="order-total" value="{{ $total }}">
                    <input type="hidden" id="shipping-fee-value" value="{{ $shippingFee }}">
                    <input type="hidden" id="discount-value" value="{{ $discount }}">

                    <!-- Phương thức thanh toán -->
                    <div class="form-group mb-4">
                        <label>Phương thức thanh toán <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
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
<button type="button" id="submit-order-btn" class="btn btn-danger w-100 mb-2">🛍️ Đặt hàng ngay (COD)</button>
                    <!-- Các phương thức thanh toán khác -->
                    <div class="row g-2">
                        <div class="col-12">
                            <button type="button" id="submit-vnpay-btn" class="btn btn-primary w-100">💳 Thanh toán VNPay</button>
                        </div>
                        <div class="col-12">
                            <button type="button" id="submit-momo-btn" class="btn btn-pink w-100" style="background-color: #e10a7e; color: white;">💗 Thanh toán MOMO</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Form riêng cho VNPay -->
        <form id="vnpay-form" method="POST" action="{{ route('vnpay.payment') }}" style="display: none;">
            @csrf
            <input type="hidden" name="full_name" id="vnpay_full_name">
            <input type="hidden" name="phone_number" id="vnpay_phone_number">
            <input type="hidden" name="address_id" id="vnpay_address_id">
            <input type="hidden" name="province" id="vnpay_province">
            <input type="hidden" name="district" id="vnpay_district">
            <input type="hidden" name="ward" id="vnpay_ward">
            <input type="hidden" name="detail" id="vnpay_detail">
            <input type="hidden" name="checkout_ids" id="vnpay_checkout_ids">
            <input type="hidden" name="voucher_code" id="vnpay_voucher_code">
            <input type="hidden" name="total_amount" id="vnpay_total_amount">
        </form>

        <!-- Form riêng cho MOMO -->
        <form id="momo-form" method="POST" action="{{ url('/momo_payment') }}" style="display: none;">
            @csrf
            <input type="hidden" name="full_name" id="momo_full_name">
            <input type="hidden" name="phone_number" id="momo_phone_number">
            <input type="hidden" name="address_id" id="momo_address_id">
            <input type="hidden" name="province" id="momo_province">
            <input type="hidden" name="district" id="momo_district">
            <input type="hidden" name="ward" id="momo_ward">
            <input type="hidden" name="detail" id="momo_detail">
            <input type="hidden" name="checkout_ids" id="momo_checkout_ids">
            <input type="hidden" name="voucher_code" id="momo_voucher_code">
            <input type="hidden" name="total_momo" id="momo_total_amount">
        </form>
    @endif
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const baseTotal = {{ $total }};
    const discount = {{ $discount }};
    const innerCities = ['TP.HCM', 'Thành phố Hồ Chí Minh', 'Hồ Chí Minh', 'Cần Thơ', 'Hà Nội', 'Đà Nẵng'];

    // ========== PHẦN XỬ LÝ PHÍ SHIP ==========
    function calculateShippingFee(addressText) {
        if (!addressText) return 30000;
        
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

    function updateFeeDisplay(fee) {
        const finalTotal = baseTotal + fee - discount;
        $('#shipping_fee_display').text(fee.toLocaleString('vi-VN') + '₫');
        $('#total_amount_display').text(finalTotal.toLocaleString('vi-VN') + '₫');
        $('#total-amount').text(finalTotal.toLocaleString('vi-VN') + ' VNĐ');
        $('#shipping-fee-value').val(fee);
    }

    function calculateShippingFromAddress() {
        const addressId = $('#address_id').val();
        
        if (!addressId) {
            const province = $('#province option:selected').text().trim();
            if (province && province !== 'Chọn tỉnh/thành') {
                const fee = calculateShippingFee(province);
                updateFeeDisplay(fee);
            } else {
                updateFeeDisplay(30000);
            }
            return;
        }

        $.ajax({
            url: '{{ route("calculate-shipping-ajax") }}',
            type: 'POST',
            data: JSON.stringify({
                address_id: addressId,
                order_total: baseTotal - discount
            }),
            contentType: 'application/json',
            success: function (data) {
                if (data.fee !== undefined) {
                    updateFeeDisplay(data.fee);
                } else {
                    updateFeeDisplay(30000);
                }
            },
            error: function (xhr) {
                console.error('Không thể tính phí vận chuyển.', xhr.responseText);
                updateFeeDisplay(30000);
            }
        });
    }

    // ========== PHẦN XỬ LÝ ĐỊA CHỈ ==========
    $('#address_id').on('change', function () {
        calculateShippingFromAddress();
        const showNew = !$(this).val();
        $('#new-address').toggle(showNew);
        $('#province, #district, #ward, #detail')
            .prop('required', showNew)
            .prop('disabled', !showNew);
    });

    $('#province').on('change', function () {
        const provinceName = $(this).find('option:selected').text().trim();
        $('#province_name').val(provinceName);
        
        if (provinceName && provinceName !== 'Chọn tỉnh/thành') {
            const fee = calculateShippingFee(provinceName);
            updateFeeDisplay(fee);
        }

        const code = $(this).val();
        if (code) {
            $('#district').prop('disabled', true).empty().append('<option>Đang tải...</option>');
            $('#ward').prop('disabled', true).empty();
            
            $.get(`/api/provinces/${code}`, function (data) {
                $('#district').empty().append('<option value="">Chọn quận/huyện</option>')
                    .append(data.districts.map(d => `<option value="${d.code}">${d.name}</option>`))
                    .prop('disabled', false);
            }).fail(function() {
                $('#district').empty().append('<option value="">Lỗi tải dữ liệu</option>');
            });
        }
    });

    $('#district').on('change', function () {
        const districtName = $(this).find('option:selected').text().trim();
        $('#district_name').val(districtName);
        
        const code = $(this).val();
        if (code) {
            $('#ward').prop('disabled', true).empty().append('<option>Đang tải...</option>');
            
            $.get(`/api/districts/${code}`, function (data) {
                $('#ward').empty().append('<option value="">Chọn xã/phường</option>')
                    .append(data.wards.map(w => `<option value="${w.code}">${w.name}</option>`))
                    .prop('disabled', false);
            }).fail(function() {
                $('#ward').empty().append('<option value="">Lỗi tải dữ liệu</option>');
            });
        }
    });

    $('#ward').on('change', function () {
        $('#ward_name').val($(this).find('option:selected').text().trim());
    });

    function loadProvinces() {
        $.get('/api/provinces', function (data) {
            $('#province').empty().append('<option value="">Chọn tỉnh/thành</option>')
                .append(data.map(p => `<option value="${p.code}">${p.name}</option>`));
        }).fail(function() {
            $('#province').empty().append('<option value="">Lỗi tải dữ liệu</option>');
        });
    }

    // ========== PHẦN VALIDATE ==========
    function validateCheckoutFields() {
        const name = $('#full_name').val()?.trim() || '';
        const phone = $('#phone_number').val()?.trim() || '';
        
        if (!name) {
            alert('⚠️ Vui lòng nhập họ tên người nhận.');
            return false;
        }
        
        const nameRegex = /^[A-Za-zÀ-ỹ\s]+$/;
        if (!nameRegex.test(name)) {
            alert('⚠️ Họ và tên chỉ được chứa chữ cái và dấu cách.');
            return false;
        }

        if (name.length > 40) {
            alert('⚠️ Họ và tên tối đa 40 ký tự.');
            return false;
        }

        const phoneRegex = /^0[0-9]{9}$/;
        if (!phoneRegex.test(phone)) {
            alert('⚠️ Số điện thoại không hợp lệ (phải gồm 10 số và bắt đầu bằng 0).');
            return false;
        }

        const addressSelect = $('#address_id').val();
        const detail = $('#detail').val()?.trim() || '';
        const province = $('#province option:selected').text()?.trim() || '';
        const district = $('#district option:selected').text()?.trim() || '';
        const ward = $('#ward option:selected').text()?.trim() || '';

        if (!addressSelect && (!detail || !province || !district || !ward)) {
            alert('⚠️ Vui lòng chọn hoặc nhập đầy đủ địa chỉ giao hàng.');
            return false;
        }

        return true;
    }

    // ========== PHẦN XỬ LÝ ĐẶT HÀNG ==========

    // Xử lý nút "Đặt hàng ngay" - COD (có modal xác nhận)
    $('#submit-order-btn').click(function(e) {
        e.preventDefault();
        
        if (!validateCheckoutFields()) return;
        
        // Hiển thị modal xác nhận cho COD
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmOrderModal'));
        confirmModal.show();
    });

    // Xử lý nút VNPay (không có modal, chuyển hướng thẳng)
    $('#submit-vnpay-btn').click(function(e) {
        e.preventDefault();
        
        if (!validateCheckoutFields()) return;
        
        // Hiển thị loading và chuyển hướng thẳng đến VNPay
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Đang chuyển hướng...');
        
        // Điền dữ liệu và submit form VNPay
        fillPaymentForm('vnpay');
        setTimeout(() => {
            $('#vnpay-form').submit();
        }, 1000);
    });

    // Xử lý nút MOMO (không có modal, chuyển hướng thẳng)
    $('#submit-momo-btn').click(function(e) {
        e.preventDefault();
        
        if (!validateCheckoutFields()) return;
        
        // Hiển thị loading và chuyển hướng thẳng đến MOMO
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Đang chuyển hướng...');
        
        // Điền dữ liệu và submit form MOMO
        fillPaymentForm('momo');
        setTimeout(() => {
            $('#momo-form').submit();
        }, 1000);
    });
// ✅ THÊM HÀM DEBUG
function debugFormData(formData) {
    console.log('=== DEBUG FORM DATA ===');
    formData.forEach(item => {
        console.log(item.name + ': ', item.value);
    });
}

// ✅ Hàm chuẩn bị dữ liệu địa chỉ - ĐÃ SỬA CHO ĐỊA CHỈ CŨ
function prepareAddressData() {
    const addressId = $('#address_id').val();
    
    console.log('=== DEBUG ADDRESS ===');
    console.log('address_id:', addressId);
    
    // Nếu chọn địa chỉ cũ, KHÔNG set rỗng mà set giá trị mặc định
    if (addressId) {
        console.log('Using saved address, setting default address values');
        
        // ✅ THAY VÌ SET RỖNG, SET GIÁ TRỊ MẶC ĐỊNH
        $('#province_name').val('Default Province');
        $('#district_name').val('Default District');
        $('#ward_name').val('Default Ward');
        
        return true;
    } 
    // Nếu chọn địa chỉ mới, đảm bảo có đủ thông tin
    else {
        const provinceName = $('#province option:selected').text()?.trim() || '';
        const districtName = $('#district option:selected').text()?.trim() || '';
        const wardName = $('#ward option:selected').text()?.trim() || '';
        const detail = $('#detail').val()?.trim() || '';
        
        console.log('New address data:', {
            province: provinceName,
            district: districtName,
            ward: wardName,
            detail: detail
        });
        
        // Đảm bảo các trường hidden có giá trị
        $('#province_name').val(provinceName);
        $('#district_name').val(districtName);
        $('#ward_name').val(wardName);
        
        // Validate địa chỉ mới
        if (!provinceName || provinceName === 'Chọn tỉnh/thành') {
            alert('⚠️ Vui lòng chọn tỉnh/thành.');
            return false;
        }
        if (!districtName || districtName === 'Chọn quận/huyện') {
            alert('⚠️ Vui lòng chọn quận/huyện.');
            return false;
        }
        if (!wardName || wardName === 'Chọn xã/phường') {
            alert('⚠️ Vui lòng chọn xã/phường.');
            return false;
        }
        if (!detail) {
            alert('⚠️ Vui lòng nhập địa chỉ chi tiết.');
            return false;
        }
        
        return true;
    }
}
    // Xử lý xác nhận trong modal (chỉ cho COD)
// Xử lý xác nhận trong modal (chỉ cho COD)
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

    // ✅ THÊM DÒNG NÀY - Chuẩn bị dữ liệu địa chỉ
    if (!prepareAddressData()) {
        return;
    }

    // Nếu validate thành công, submit form COD
    submitCODOrder();
});

    // Hàm xử lý COD - ĐÃ SỬA
   function submitCODOrder() {
    const form = $('#checkout-form');
    let formData = form.serializeArray();

    // ✅ Thêm payment_method
    formData.push({ name: 'payment_method', value: 'cod' });

    // Thêm confirm_password nếu đã đăng nhập
    @if(Auth::check())
    const password = $('#confirmPassword').val().trim();
    if (password) {
        formData.push({ name: 'confirm_password', value: password });
    }
    @else
    // Thêm phone_confirm cho khách
    const confirmPhone = $('#confirmPhone').val().trim();
    if (confirmPhone) {
        formData.push({ name: 'phone_confirm', value: confirmPhone });
    }
    @endif

    const $btn = $('#submit-order-btn');
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Đang xử lý...');

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: $.param(formData),
        success: function(res) {
            window.location.href = '/orders/' + res.order_id;
        },
        error: function(xhr) {
            let errorMessage = 'Lỗi xảy ra khi đặt hàng!';
            
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                // Hiển thị lỗi validation đầu tiên
                const errors = xhr.responseJSON.errors;
                const firstError = Object.values(errors)[0];
                if (firstError && firstError[0]) {
                    errorMessage = firstError[0];
                }
            }
            
            alert('❌ ' + errorMessage);
            $btn.prop('disabled', false).html('🛍️ Đặt hàng ngay (COD)');
            bootstrap.Modal.getInstance(document.getElementById('confirmOrderModal')).hide();
        }
    });
}

    // Hàm điền dữ liệu vào form thanh toán
    function fillPaymentForm(formType) {
        const shippingFee = parseInt($('#shipping-fee-value').val()) || 0;
        const finalTotal = baseTotal + shippingFee - discount;

        const checkoutIds = [];
        $('input[name="checkout_ids[]"]').each(function() {
            checkoutIds.push($(this).val());
        });

        const commonData = {
            full_name: $('#full_name').val(),
            phone_number: $('#phone_number').val(),
            address_id: $('#address_id').val(),
            province: $('#province').val(),
            district: $('#district').val(),
            ward: $('#ward').val(),
            detail: $('#detail').val(),
            checkout_ids: JSON.stringify(checkoutIds),
            voucher_code: $('#voucher_code').val(),
            total_amount: finalTotal
        };

        if (formType === 'vnpay') {
            $('#vnpay_full_name').val(commonData.full_name);
            $('#vnpay_phone_number').val(commonData.phone_number);
            $('#vnpay_address_id').val(commonData.address_id);
            $('#vnpay_province').val(commonData.province);
            $('#vnpay_district').val(commonData.district);
            $('#vnpay_ward').val(commonData.ward);
            $('#vnpay_detail').val(commonData.detail);
            $('#vnpay_checkout_ids').val(commonData.checkout_ids);
            $('#vnpay_voucher_code').val(commonData.voucher_code);
            $('#vnpay_total_amount').val(commonData.total_amount);
        } else if (formType === 'momo') {
            $('#momo_full_name').val(commonData.full_name);
            $('#momo_phone_number').val(commonData.phone_number);
            $('#momo_address_id').val(commonData.address_id);
            $('#momo_province').val(commonData.province);
            $('#momo_district').val(commonData.district);
            $('#momo_ward').val(commonData.ward);
            $('#momo_detail').val(commonData.detail);
            $('#momo_checkout_ids').val(commonData.checkout_ids);
            $('#momo_voucher_code').val(commonData.voucher_code);
            $('#momo_total_amount').val(commonData.total_amount);
        }
    }

    // Reset modal khi đóng
    $('#confirmOrderModal').on('hidden.bs.modal', function () {
        $('#confirmPhone').val('');
        $('#confirmPassword').val('');
        $('#confirmPhoneError').addClass('d-none').text('');
        $('#confirmPasswordError').addClass('d-none').text('');
        
        // Reset nút COD
        $('#submit-order-btn').prop('disabled', false).html('🛍️ Đặt hàng ngay (COD)');
    });

    // ========== PHẦN VALIDATE REAL-TIME ==========
    $('#phone_number').on('input', function () {
        const phone = $(this).val();
        const phoneRegex = /^[0-9]{10}$/;
        $('#phone-error').text(phone && !phoneRegex.test(phone) ? 'Số điện thoại phải là 10 chữ số.' : '');
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

    // ========== PHẦN VOUCHER ==========
    $('#voucher_select').on('change', function () {
        const code = $(this).val();
        let selectedIds = [];
        $('tr[data-id]').each(function () {
            selectedIds.push($(this).data('id'));
        });

        if (!code) return;

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
                alert('Đã xảy ra lỗi khi áp dụng mã.');
            }
        });
    });

    // ========== KHỞI TẠO ==========
    loadProvinces();
    calculateShippingFromAddress();
});
</script>
<style>
.btn-pink {
    background-color: #e10a7e;
    border-color: #e10a7e;
    color: white;
}
.btn-pink:hover {
    background-color: #c0096b;
    border-color: #c0096b;
    color: white;
}
</style>
@endsection