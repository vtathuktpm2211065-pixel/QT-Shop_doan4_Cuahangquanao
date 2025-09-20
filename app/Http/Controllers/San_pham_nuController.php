<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Thêm dòng này để gọi model

class San_pham_nuController extends Controller
{
    public function index()
    {
        $sanPham = Product::where('gioi_tinh', 'nu')->get(); // hoặc ->paginate(9)
        return view('San_pham.Nu', compact('sanPham')); // ✅ Truyền biến vào view
    }
  public function chi_tiet($slug)
{
    // Bước 1: Lấy sản phẩm kèm biến thể
    $product = Product::with(['variants' => function ($query) {
        $query->where('stock_quantity', '>', 0);
    }])->where('slug', $slug)->firstOrFail();

    // Bước 2: Lấy danh sách size còn hàng (S, M, L)
    $sizes = $product->variants->pluck('size')->unique()->sort()->values()->all();
    $orderedSizes = ['S', 'M', 'L'];
    $sizes = array_values(array_filter($orderedSizes, fn($size) => in_array($size, $sizes)));

    // Bước 3: Lấy danh sách màu còn hàng
    $colors = $product->variants->pluck('color')->unique()->sort()->values()->all();

    // Bước 4: Tạo mảng tồn kho theo cặp "màu_size"
    $variantStocks = [];
    foreach ($product->variants as $variant) {
        $key = strtolower($variant->color) . '_' . strtoupper($variant->size);
        $variantStocks[$key] = $variant->stock_quantity;
    }

    // Bước 5: Lấy sản phẩm liên quan (cùng danh mục, khác id)
    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->take(4)
        ->get();

    return view('San_pham.chi_tiet', compact(
        'product',
        'sizes',
        'colors',
        'variantStocks',
        'relatedProducts'
    ));
}

}
