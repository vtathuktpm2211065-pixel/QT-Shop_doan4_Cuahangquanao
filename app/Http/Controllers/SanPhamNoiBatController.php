<?php

namespace App\Http\Controllers;

use App\Models\Product; // nhớ import model Product
use Illuminate\Http\Request;

class SanPhamNoiBatController extends Controller
{
    public function index()
{
    $sanPham = Product::where('noi_bat', true)->get();

    // Debug log đếm sản phẩm
    \Log::info('Số sản phẩm nổi bật: ' . $sanPham->count());

    // Debug show tên sản phẩm duy nhất
    $names = $sanPham->pluck('name')->unique();
    \Log::info('Tên sản phẩm nổi bật duy nhất: ' . $names->implode(', '));

    return view('San_pham.Noi_bat', compact('sanPham'));
}

    public function chi_tiet($slug)
{
    $product = Product::with('variants')->where('slug', $slug)->firstOrFail();

    $sizes = $product->variants
        ->where('stock_quantity', '>', 0)
        ->pluck('size')
        ->unique()
        ->sort()
        ->values()
        ->all();

    $orderedSizes = ['S', 'M', 'L'];
    $sizes = array_values(array_filter($orderedSizes, fn($size) => in_array($size, $sizes)));

    $colors = $product->variants
        ->where('stock_quantity', '>', 0)
        ->pluck('color')
        ->unique()
        ->sort()
        ->values()
        ->all();

    $relatedProducts = Product::where('category_id', $product->category_id)
                              ->where('id', '!=', $product->id)
                              ->take(4)
                              ->get();

    return view('San_pham.chi_tiet', compact('product', 'sizes', 'colors', 'relatedProducts'));
}

}
