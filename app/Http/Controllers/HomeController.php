<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\CartItem;

class HomeController extends Controller
{
    public function index()
    {
        $behaviorBased = $this->getBehaviorBasedRecommendations();
        $categoryBased = $this->getCategoryBasedRecommendations();
        $popularProducts = $this->getPopularProducts();
        $mostRated = $this->getMostRatedProducts();
        $products = $this->getBehaviorBasedRecommendations(); // hoặc getCategoryBasedRecommendations()



        return view('home', compact('products','behaviorBased', 'categoryBased', 'popularProducts', 'mostRated'));
    }
    
    private function getBehaviorBasedRecommendations()
{
    if (!Auth::check()) {
        return $this->getFallbackRecommendations();
    }
    
    $userId = Auth::id();
    
    // 1. Lấy danh mục từ sản phẩm đã đánh giá
    $reviewedCategories = ProductReview::where('user_id', $userId)
        ->with('product.category')
        ->get()
        ->pluck('product.category_id')
        ->filter()
        ->unique()
        ->toArray();

    // 2. Lấy danh mục từ sản phẩm trong giỏ hàng
    $cartCategories = CartItem::where('user_id', $userId)
        ->with('product.category')
        ->get()
        ->pluck('product.category_id')
        ->filter()
        ->unique()
        ->toArray();

    // 3. Gộp tất cả danh mục
    $allCategories = array_unique(array_merge($reviewedCategories, $cartCategories));

    if (!empty($allCategories)) {
        // Loại sản phẩm đã đánh giá thôi, không loại sản phẩm trong giỏ hàng
        $excludedIds = ProductReview::where('user_id', $userId)->pluck('product_id')->toArray();

        return Product::whereIn('category_id', $allCategories)
            ->whereNotIn('id', $excludedIds)
            ->inRandomOrder()
            ->limit(8)
            ->get();
    }

    return $this->getFallbackRecommendations();
}

    private function getCategoryBasedRecommendations()
    {
        // Dựa trên sản phẩm phổ biến trong các danh mục
        return Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->inRandomOrder()
            ->limit(8)
            ->get();
    }
    
    private function getPopularProducts()
    {
        return Product::withCount('reviews')
            ->orderBy('views_count', 'desc')
            ->limit(6)
            ->get();
    }
    
    private function getMostRatedProducts()
    {
        return Product::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->having('reviews_count', '>', 0)
            ->orderBy('reviews_avg_rating', 'desc')
            ->limit(6)
            ->get();
    }
    
    private function getViewedProductIds($userId)
    {
        return ProductReview::where('user_id', $userId)
            ->pluck('product_id')
            ->toArray();
    }
    
    private function getFallbackRecommendations()
    {
        // Trả về sản phẩm phổ biến nếu không có dữ liệu hành vi
        return Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(8)
            ->get();
    }
    
    // Theo dõi hành vi xem sản phẩm
    public function trackProductView(Request $request, $productId)
    {
        if (Auth::check()) {
            $view = ProductView::firstOrNew([
                'user_id' => Auth::id(),
                'product_id' => $productId
            ]);
            
            $view->view_count += 1;
            $view->last_viewed_at = now();
            $view->save();
        }
        
        return response()->json(['success' => true]);
    }
}