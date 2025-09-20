@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-2">
  <h3>Xuất  kho</h3>
  <a href="{{ route('admin.stock.index') }}" class="btn btn-danger">Thoát</a>
</div>


    {{-- Form lọc --}}
    <form method="GET" action="{{ route('admin.stock.export') }}" class="row g-2 align-items-end mb-3">
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
    <form action="{{ route('admin.stock.storeBulkExport') }}" method="POST">
        @csrf

        {{-- Số lượng chung và nút thao tác --}}
        <div class="mb-3 d-flex flex-wrap align-items-center gap-3">
            <label for="bulk_quantity" class="form-label mb-2">Xuất số lượng chung các sản phẩm được chọn</label>
            <input type="number" id="bulk_quantity" class="form-control w-auto mb-2" min="0" value="0" />
            <button type="button" class="btn btn-primary mb-2" id="apply_bulk_quantity">Áp dụng</button>
            <button type="submit" class="btn btn-success mb-2">Xuất kho</button>
            <button type="button" class="btn btn-secondary mb-2" id="reset-form">Hủy</button>
            <a href="{{ route('admin.stock.index') }}" class="btn btn-danger mb-2">Thoát</a>
        </div>

        {{-- Bảng danh sách sản phẩm --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select_all"></th>
                    <th>Tên sản phẩm</th>
                    <th>Màu</th>
                    <th>Size</th>
                    <th>Tồn kho</th>
                    <th>Số lượng xuất</th>
                </tr>
            </thead>
            <tbody>
                @foreach($variants as $index => $variant)
                <tr>
                    <td><input type="checkbox" class="select_variant" data-index="{{ $index }}"></td>
                    <td>{{ $variant->product->name }}</td>
                    <td>{{ $variant->color }}</td>
                    <td>{{ $variant->size }}</td>
                    <td>{{ $variant->stock_quantity }}</td>
                    <td>
                        <input type="hidden" name="exports[{{ $index }}][variant_id]" value="{{ $variant->id }}">
                        <input type="number" name="exports[{{ $index }}][quantity]" min="0" class="form-control quantity-input" value="0" disabled>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </form>
</div>

<script>
    // Script chọn / bỏ chọn tất cả checkbox
    document.getElementById('select_all').addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('.select_variant').forEach(cb => {
            cb.checked = checked;
            toggleQuantityInput(cb);
        });
    });

    // Bật / tắt input số lượng theo checkbox
    document.querySelectorAll('.select_variant').forEach(cb => {
        cb.addEventListener('change', function () {
            toggleQuantityInput(this);
        });
    });

    function toggleQuantityInput(checkbox) {
        const index = checkbox.getAttribute('data-index');
        const quantityInput = document.querySelector(`input[name="exports[${index}][quantity]"]`);
        const variantIdInput = document.querySelector(`input[name="exports[${index}][variant_id]"]`);

        if (checkbox.checked) {
            quantityInput.disabled = false;
            variantIdInput.disabled = false;
        } else {
            quantityInput.disabled = true;
            quantityInput.value = 0;
            variantIdInput.disabled = true;
        }
    }

    // Trước submit form xuất kho, disable input của biến thể không chọn
    document.querySelector('form[action="{{ route('admin.stock.storeBulkExport') }}"]').addEventListener('submit', function () {
        document.querySelectorAll('.select_variant').forEach(cb => {
            const index = cb.getAttribute('data-index');
            const quantityInput = document.querySelector(`input[name="exports[${index}][quantity]"]`);
            const variantIdInput = document.querySelector(`input[name="exports[${index}][variant_id]"]`);

            if (!cb.checked) {
                quantityInput.disabled = true;
                variantIdInput.disabled = true;
            }
        });
    });

    // Áp dụng số lượng chung cho các biến thể được chọn
    document.getElementById('apply_bulk_quantity').addEventListener('click', function () {
        const bulkQty = parseInt(document.getElementById('bulk_quantity').value);
        if (bulkQty < 0) {
            alert('Số lượng phải lớn hơn hoặc bằng 0');
            return;
        }

        document.querySelectorAll('.select_variant:checked').forEach(cb => {
            const index = cb.getAttribute('data-index');
            const input = document.querySelector(`input[name="exports[${index}][quantity]"]`);
            input.value = bulkQty;
        });
    });
</script>
@endsection

<style>
    .d-flex.gap-3 > * {
        margin-right: 1rem;
        margin-bottom: 0.5rem;
    }
</style>
