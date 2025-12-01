<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

use App\Services\RecommendationService;

class ProductController extends Controller
{
   public function index()
{
    $sanPham = Product::where('noi_bat', true)->get();

    \Log::info('Số lượng sản phẩm nổi bật: ' . $sanPham->count());
    \Log::info('ID sản phẩm nổi bật: ' . $sanPham->pluck('id')->implode(', '));

    return view('San_pham.Noi_bat', compact('sanPham'));
}


    public function choNu()
    {
        $sanPham = Product::where('gioi_tinh', 'nu')->get();
        return view('sanpham.chonu', compact('sanPham'));
    }

    public function choNam()
    {
        $sanPham = Product::where('gioi_tinh', 'nam')->get();
        return view('sanpham.chonam', compact('sanPham'));
    }

    public function choTreEm()
    {
        $sanPham = Product::where('gioi_tinh', 'tre-em')->get();
        return view('sanpham.chotreem', compact('sanPham'));
    }
public function chi_tiet($slug)
{
    // 1. Lấy sản phẩm kèm biến thể còn hàng
    $product = Product::with(['variants' => function ($query) {
        $query->where('stock_quantity', '>', 0);
    }])->where('slug', $slug)->firstOrFail();

    // 2. Lấy danh sách size còn hàng (S, M, L)
    $sizes = $product->variants->pluck('size')->unique()->sort()->values()->all();
    $orderedSizes = ['S', 'M', 'L'];
    $sizes = array_values(array_filter($orderedSizes, fn($size) => in_array($size, $sizes)));

    // 3. Lấy danh sách màu còn hàng
    $colors = $product->variants->pluck('color')->unique()->sort()->values()->all();

    // 4. Tạo mảng tồn kho theo cặp "màu_size"
    $variantStocks = [];
    foreach ($product->variants as $variant) {
        $key = strtolower($variant->color) . '_' . strtoupper($variant->size);
        $variantStocks[$key] = $variant->stock_quantity;
    }

    // 5. Sản phẩm cùng danh mục (4 sản phẩm)
    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->take(4)
        ->get();

    // 6. Gợi ý sản phẩm dựa trên RecommendationService (ví dụ 8 sản phẩm)
    $recommendations = (new RecommendationService)->getRecommendations(Auth::id(), 8);

    // 7. Lấy đánh giá sản phẩm
    $reviews = $product->reviews;
// --- Lưu sản phẩm vừa xem vào session ---
// Lưu sản phẩm vừa xem vào session
$recentViews = session()->get('recent_views', []);
$productId = $product->id;

// Nếu đã tồn tại trong session, xóa đi để đưa lên đầu
if(($key = array_search($productId, $recentViews)) !== false) {
    unset($recentViews[$key]);
}

// Thêm sản phẩm vừa xem lên đầu
array_unshift($recentViews, $productId);

// Giới hạn số sản phẩm vừa xem, ví dụ 6 sản phẩm
$recentViews = array_slice($recentViews, 0, 6);

// Lưu lại session
session(['recent_views' => $recentViews]);


    return view('San_pham.chi_tiet', compact(
        'product',
        'sizes',
        'colors',
        'variantStocks',
        'relatedProducts',
        'recommendations',
        'reviews'
    ));
}


public function showReviews($id)
{
    $product = Product::with(['variants' => function ($query) {
        $query->where('stock_quantity', '>', 0);
    }])->findOrFail($id);

    $sizes = $product->variants->pluck('size')->unique()->sort()->values()->all();
    $orderedSizes = ['S', 'M', 'L'];
    $sizes = array_values(array_filter($orderedSizes, fn($size) => in_array($size, $sizes)));

    $colors = $product->variants->pluck('color')->unique()->sort()->values()->all();

    $variantStocks = [];
    foreach ($product->variants as $variant) {
        $key = strtolower($variant->color) . '_' . strtoupper($variant->size);
        $variantStocks[$key] = $variant->stock_quantity;
    }

    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->take(4)
        ->get();

    $recommendations = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->take(8)
        ->get();

    $reviews = $product->reviews;

    return view('san_pham.chi_tiet', compact(
        'product',
        'sizes',
        'colors',
        'variantStocks',
        'relatedProducts',
        'recommendations',
        'reviews'
    ));
}


public function search(Request $request)
{
    $keyword = $request->input('keyword'); // hoặc $query = ...

    $sanPham = Product::with('variants')
        ->where('name', 'like', '%' . $keyword . '%')
        ->paginate(9);

    return view('San_pham.search_results', [
        'sanPham' => $sanPham,
        'query' => $keyword, // ✅ Truyền biến $query vào view
    ]);
}


}