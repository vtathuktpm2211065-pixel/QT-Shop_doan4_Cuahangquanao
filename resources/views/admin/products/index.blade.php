@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <h3 class="mb-4 text-center">📦 Danh sách sản phẩm</h3>

    {{-- KHUNG LỌC NẰM GIỮA --}}
    <div class="d-flex justify-content-center mb-4">
        <div class="card shadow-sm border-0" style="min-width: 320px;">
            <div class="card-header bg-light py-2 px-3 text-center">
                <a class="text-decoration-none d-block" data-bs-toggle="collapse" href="#filterBox" role="button"
                   aria-expanded="{{ request()->hasAny(['category_id', 'price_min', 'price_max']) ? 'true' : 'false' }}"
                   aria-controls="filterBox" id="filterToggle">
                    <strong style="font-size: 14px;">🔍 Lọc sản phẩm <span id="arrowIcon">▾</span></strong>
                </a>
            </div>
            <div class="collapse {{ request()->hasAny(['category_id', 'price_min', 'price_max']) ? 'show' : '' }}" id="filterBox">
                <div class="card-body p-3">
                    <form method="GET">
                        <div class="mb-2">
                            <label for="category_id" class="form-label mb-1">📁 Danh mục</label>
                            <select name="category_id" id="category_id" class="form-select form-select-sm">
                                <option value="">-- Tất cả danh mục --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label for="price_min" class="form-label mb-1">💰 Giá từ</label>
                            <input type="number" name="price_min" id="price_min"
                                   class="form-control form-control-sm" placeholder="VD: 100000"
                                   value="{{ request('price_min') }}">
                        </div>

                        <div class="mb-2">
                            <label for="price_max" class="form-label mb-1">💰 Giá đến</label>
                            <input type="number" name="price_max" id="price_max"
                                   class="form-control form-control-sm" placeholder="VD: 500000"
                                   value="{{ request('price_max') }}">
                        </div>

                        <div class="d-grid gap-2 mt-2">
                            <button type="submit" class="btn btn-sm btn-primary">🔍 Lọc</button>
                            <a href="{{ route('admin.san-pham.index') }}" class="btn btn-sm btn-secondary">🔄 Đặt lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH SẢN PHẨM --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
         <thead class="table-primary text-center align-middle">
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>STT</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Hình ảnh</th>
                    <th>Đặc điểm</th>
                    <th>Nổi bật</th>
                    <th>Phổ biến</th>
                    <th>Tác vụ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $index => $product)
                <tr>
                    <td><input type="checkbox" name="product_ids[]" value="{{ $product->id }}"></td>
                    <td class =" text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $product->name }}</strong><br>
                        <a href="{{ route('admin.san-pham.bien-the', $product->id) }}">🧬 Xem biến thể</a>
                    </td>
                    <td><small>{{ number_format($product->price, 0, ',', '.') }} VNĐ</small></td>
                    <td>
                        @if($product->image_url)
                            <img src="{{ asset('images/' . $product->image_url) }}" alt="{{ $product->name }}" style="width: 60px;">
                        @else
                            <em>Không có hình</em>
                        @endif
                    </td>
                    <td>{{ $product->description ?? 'Không thuộc đặc điểm nào' }}</td>
                    <td><input type="checkbox" {{ $product->noi_bat ? 'checked' : '' }} disabled></td>
                    <td><input type="checkbox" {{ $product->pho_bien ? 'checked' : '' }} disabled></td>
                    <td>
                        <a href="{{ route('admin.san-pham.edit', $product->id) }}" class="btn btn-sm btn-primary">✏️</a>
                        <form action="{{ route('admin.san-pham.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Bạn chắc chắn xoá?')" class="btn btn-sm btn-danger">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleLink = document.getElementById('filterToggle');
        const arrowIcon = document.getElementById('arrowIcon');
        const filterBox = document.getElementById('filterBox');

        if (toggleLink && arrowIcon && filterBox) {
            toggleLink.addEventListener('click', () => {
                setTimeout(() => {
                    const isExpanded = filterBox.classList.contains('show');
                    arrowIcon.textContent = isExpanded ? '▾' : '▴';
                }, 250);
            });
        }
    });
</script>
@endsection
