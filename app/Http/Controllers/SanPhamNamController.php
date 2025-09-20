<?php

namespace App\Http\Controllers;

use App\Models\Product; 

class SanPhamNamController extends Controller
{
    public function index()
    {
       
         $sanPham = Product::with('variants')
        ->where('gioi_tinh', 'nam')
        ->get();

    return view('San_pham.Nam', compact('sanPham'));
    }
   public function chi_tiet($slug)
{
    // Lấy sản phẩm theo slug và kèm biến thể
    $product = Product::where('slug', $slug)->with('variants')->first();

    if (!$product) {
        abort(404);
    }

    // Lấy danh sách size và màu từ các variants
    $sizes = $product->variants->pluck('size')->unique();
    $colors = $product->variants->pluck('color')->unique();

    // Sản phẩm liên quan
    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->take(4)
        ->get();

    return view('San_pham.chi_tiet', compact('product', 'relatedProducts', 'sizes', 'colors'));
}


}
