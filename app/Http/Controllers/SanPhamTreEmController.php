<?php

namespace App\Http\Controllers;

use App\Models\Product;

class SanPhamTreEmController extends Controller
{
    public function index()
    {
         $sanPham = Product::where('gioi_tinh', 'tre_em')->get();

        return view('San_pham.Tre_em', compact('sanPham'));
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
