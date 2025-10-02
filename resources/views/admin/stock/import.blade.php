@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-2">
  <h3>Nhập kho</h3>
  <a href="{{ route('admin.stock.index') }}" class="btn btn-danger">Thoát</a>
</div>

{{-- Form lọc --}}
    <form method="GET" action="{{ route('admin.stock.import') }}" class="row g-2 align-items-end mb-3">
        {{-- Tìm kiếm --}}
        <div class="col-md-4">
            <label class="form-label">Tìm kiếm theo tên, màu, size</label>
            <input type="text" name="keyword" class="form-control form-control-sm" placeholder="VD: Bộ đồ bà ba/đen.m" value="{{ request('keyword') }}">
        </div>

        {{-- Lọc danh mục --}}
        <div class="col-md-3">
            <label class="form-label">Danh mục</label>
            <select name="category_id" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nút tìm và reset --}}
        <div class="col-md-3 d-flex justify-content-between">
    <button type="submit" class="btn btn-outline-primary btn-sm mb-1">🔍 Tìm kiếm</button>
    <a href="{{ route('admin.stock.export') }}" class="btn btn-outline-secondary btn-sm mb-1">❌ Xóa bộ lọc</a>
</div>

    </form>

    {{-- Form nhập kho --}}
    <form action="{{ route('admin.stock.storeBulkImport') }}" method="POST">
        @csrf

        {{-- Gợi ý định dạng --}}
        <small class="text-muted d-block mb-3" style="margin-top: -6px;">
            Tìm theo định dạng:
            <code>Áo thun/đen.s</code>,
            <code>Áo sơ mi/s</code>,
            <code>đỏ.m</code>,
            <code>đen</code>,
            <code>s</code>,
            <code>bộ đồ bà ba</code>
        </small>

        {{-- Số lượng chung và nút thao tác --}}
        <div class="mb-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <label for="bulk_quantity" class="form-label mb-2">Số lượng chung</label>
                <input type="number" id="bulk_quantity" class="form-control w-auto mb-2" min="0" value="0" />

                <button type="button" class="btn btn-primary mb-2" id="apply_bulk_quantity">Áp dụng</button>
                <button type="submit" class="btn btn-success mb-2">Nhập kho</button>
                <button type="button" class="btn btn-secondary mb-2" id="reset-form">Hủy nhập</button>
                
            </div>
        </div>

        {{-- Bảng danh sách biến thể --}}
        <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle text-center">
        <thead class="table-primary">
            <tr>
                <th style="width:40px"><input type="checkbox" id="select_all"></th>
                <th>Tên sản phẩm</th>
                <th>Màu</th>
                <th>Size</th>
                <th>Tồn kho</th>
                <th>Số lượng nhập</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $index => $variant)
            <tr>
                <td>
                    <input type="checkbox" class="select_variant" data-index="{{ $index }}">
                </td>
                <td class="text-start">{{ $variant->product->name }}</td>
                <td><span class="badge bg-light text-dark">{{ $variant->color }}</span></td>
                <td><span class="badge bg-light text-dark">{{ $variant->size }}</span></td>
                <td><span class="badge bg-info text-white">{{ $variant->stock_quantity }}</span></td>
                <td style="width:130px">
                    <input type="hidden" name="imports[{{ $index }}][variant_id]" value="{{ $variant->id }}">
                    <input type="number" 
                           name="imports[{{ $index }}][quantity]" 
                           min="0" 
                           class="form-control form-control-sm text-center quantity-input" 
                           value="0" 
                           disabled>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    // Chọn / bỏ chọn tất cả checkbox
    document.getElementById('select_all').addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('.select_variant').forEach(cb => {
            cb.checked = checked;
            toggleQuantityInput(cb);
        });
    });

    // Bật / tắt input số lượng dựa theo checkbox chọn biến thể
    document.querySelectorAll('.select_variant').forEach(cb => {
        cb.addEventListener('change', function () {
            toggleQuantityInput(this);
        });
    });
    

    function toggleQuantityInput(checkbox) {
        const index = checkbox.getAttribute('data-index');
        const quantityInput = document.querySelector(`input[name="imports[${index}][quantity]"]`);
        const variantIdInput = document.querySelector(`input[name="imports[${index}][variant_id]"]`);

        if (checkbox.checked) {
            quantityInput.disabled = false;
            variantIdInput.disabled = false;
        } else {
            quantityInput.disabled = true;
            quantityInput.value = 0;
            variantIdInput.disabled = true;
        }
    }

    // Trước khi submit: chỉ giữ lại inputs của các biến thể được chọn
    document.querySelector('form[action="{{ route('admin.stock.storeBulkImport') }}"]').addEventListener('submit', function (e) {
        document.querySelectorAll('.select_variant').forEach(cb => {
            const index = cb.getAttribute('data-index');
            const quantityInput = document.querySelector(`input[name="imports[${index}][quantity]"]`);
            const variantIdInput = document.querySelector(`input[name="imports[${index}][variant_id]"]`);

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
            const input = document.querySelector(`input[name="imports[${index}][quantity]"]`);
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
