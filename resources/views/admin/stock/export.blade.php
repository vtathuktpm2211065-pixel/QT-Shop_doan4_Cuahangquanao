@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">📦 Xuất kho</h3>
        <a href="{{ route('admin.stock.index') }}" class="btn btn-danger">
            ⬅ Quay lại
        </a>
    </div>

    {{-- Form lọc --}}
      <form method="GET" action="{{ route('admin.stock.index') }}" class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
            <label class="form-label">Tìm kiếm theo tên, màu, size</label>
            <input type="text" name="keyword" class="form-control form-control-sm" placeholder="VD: Bộ đồ bà ba/đen.m" value="{{ request('keyword') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Danh mục</label>
            <select name="category_id" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

       <div class="col-md-3 d-flex justify-content-between">
    <button type="submit" class="btn btn-outline-primary btn-sm mb-1">🔍 Tìm kiếm</button>
    <a href="{{ route('admin.stock.export') }}" class="btn btn-outline-secondary btn-sm mb-1">❌ Xóa bộ lọc</a>
</div>

    </form>
    {{-- Form xuất kho --}}
    <form action="{{ route('admin.stock.storeBulkExport') }}" method="POST" id="exportForm">
        @csrf
 <div class="mb-3 d-flex flex-wrap align-items-center gap-3">
    <label for="bulk_quantity" class="form-label mb-0">Xuất số lượng chung</label>

    <input type="number" id="bulk_quantity" 
           class="form-control w-auto" 
           min="0" value="0" />

    <button type="button" class="btn btn-primary" id="apply_bulk_quantity">Áp dụng</button>
    <button type="submit" class="btn btn-success">Xuất kho</button>
    <button type="reset" class="btn btn-secondary">Hủy</button>
    <a href="{{ route('admin.stock.index') }}" class="btn btn-danger">Thoát</a>
</div>

        {{-- Bảng sản phẩm --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle table-bordered">
                <thead class="table-primary">
                    <tr class="text-center">
                        <th><input type="checkbox" id="select_all"></th>
                        <th>Tên sản phẩm</th>
                        <th>Màu</th>
                        <th>Size</th>
                        <th>Tồn kho</th>
                        <th>Giá (₫)</th>
                        <th>Số lượng xuất</th>
                        <th>Thành tiền (₫)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $index => $variant)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="select_variant" data-index="{{ $index }}">
                        </td>
                        <td>{{ $variant->product->name }}</td>
                        <td>{{ $variant->color }}</td>
                        <td>{{ $variant->size }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $variant->stock_quantity }}</span>
                        </td>
                        <td class="text-end">{{ number_format($variant->price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <input type="hidden" name="exports[{{ $index }}][variant_id]" value="{{ $variant->id }}">
                            <input type="number" 
                                   name="exports[{{ $index }}][quantity]" 
                                   min="0"
                                   class="form-control quantity-input text-center"
                                   value="0"
                                   data-price="{{ $variant->price }}"
                                   data-index="{{ $index }}"
                                   disabled>
                        </td>
                        <td class="text-end">
                            <span class="total-price fw-bold text-success" id="total_price_{{ $index }}">0</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Không có sản phẩm nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>

{{-- CSS nhỏ --}}
<style>
    .form-label { font-size: 14px; }
    table td, table th { vertical-align: middle; }
</style>


<script>
// Toggle checkbox all
document.getElementById('select_all').addEventListener('change', function () {
    document.querySelectorAll('.select_variant').forEach(cb => {
        cb.checked = this.checked;
        toggleQuantityInput(cb);
    });
});

// Bật/tắt input khi chọn
function toggleQuantityInput(checkbox) {
    const index = checkbox.dataset.index;
    const qtyInput = document.querySelector(`input[name="exports[${index}][quantity]"]`);
    if (checkbox.checked) {
        qtyInput.disabled = false;
    } else {
        qtyInput.disabled = true;
        qtyInput.value = 0;
        updateTotalPrice(index);
    }
}

// Tính thành tiền
function updateTotalPrice(index) {
    const qtyInput = document.querySelector(`input[name="exports[${index}][quantity]"]`);
    const price = parseFloat(qtyInput.dataset.price);
    const qty = parseInt(qtyInput.value) || 0;
    const total = price * qty;
    document.getElementById(`total_price_${index}`).textContent = total.toLocaleString("vi-VN");
}

// Check/uncheck từng dòng
document.querySelectorAll('.select_variant').forEach(cb => {
    cb.addEventListener('change', () => toggleQuantityInput(cb));
});

// Cập nhật tiền khi thay đổi số lượng
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('input', () => updateTotalPrice(input.dataset.index));
});

// Áp dụng số lượng chung
document.getElementById('apply_bulk_quantity').addEventListener('click', function () {
    const bulkQty = parseInt(document.getElementById('bulk_quantity').value);
    if (bulkQty < 0) return alert('Số lượng >= 0');
    document.querySelectorAll('.select_variant:checked').forEach(cb => {
        const index = cb.dataset.index;
        const qtyInput = document.querySelector(`input[name="exports[${index}][quantity]"]`);
        qtyInput.value = bulkQty;
        updateTotalPrice(index);
    });
});

// Trước khi submit: xoá input không chọn
document.getElementById('exportForm').addEventListener('submit', function () {
    document.querySelectorAll('.select_variant').forEach(cb => {
        if (!cb.checked) {
            const index = cb.dataset.index;
            document.querySelector(`input[name="exports[${index}][quantity]"]`).disabled = true;
            document.querySelector(`input[name="exports[${index}][variant_id]"]`).disabled = true;
        }
    });
});
</script>

@endsection
