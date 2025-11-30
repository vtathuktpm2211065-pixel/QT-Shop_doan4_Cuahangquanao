<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductRiview;
use App\Models\OrderItem;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function getRecommendations($userId = null, $limit = 10)
    {
        if (!$userId && Auth::check()) {
            $userId = Auth::id();
        }

        $recommendations = collect();

        // Nếu có user, lấy gợi ý dựa trên hành vi
        if ($userId) {
            $behaviorBased = $this->getBehaviorBasedRecommendations($userId, $limit);
            $recommendations = $recommendations->merge($behaviorBased);
        }

        // Nếu chưa đủ, thêm gợi ý dựa trên danh mục phổ biến
        if ($recommendations->count() < $limit) {
            $categoryBased = $this->getCategoryBasedRecommendations($limit - $recommendations->count());
            $recommendations = $recommendations->merge($categoryBased);
        }

        // Nếu vẫn chưa đủ, thêm sản phẩm phổ biến
        if ($recommendations->count() < $limit) {
            $popularProducts = $this->getPopularProducts($limit - $recommendations->count());
            $recommendations = $recommendations->merge($popularProducts);
        }

        // Loại bỏ trùng lặp
        $recommendations = $recommendations->unique('id');

        return $recommendations->take($limit);
    }

   private function getBehaviorBasedRecommendations($userId = null, $limit = 8)
{
    if (!$userId) {
        $userId = Auth::id();
    }

    if (!$userId) {
        return $this->getFallbackRecommendations();
    }

    // 1. Lấy danh mục từ sản phẩm đã xem
    $viewedCategories = ProductView::where('user_id', $userId)
        ->with('product.category')
        ->get()
        ->pluck('product.category_id')
        ->filter()
        ->unique()
        ->toArray();

    // 2. Lấy danh mục từ sản phẩm đã đánh giá
    $reviewedCategories = ProductReview::where('user_id', $userId)
        ->with('product.category')
        ->get()
        ->pluck('product.category_id')
        ->filter()
        ->unique()
        ->toArray();

    // 3. Lấy danh mục từ sản phẩm trong giỏ hàng
    $cartCategories = CartItem::where('user_id', $userId)
        ->with('product.category')
        ->get()
        ->pluck('product.category_id')
        ->filter()
        ->unique()
        ->toArray();

    if (!empty($cartCategories)) {
        // Chỉ loại sản phẩm đã đánh giá, không loại sản phẩm đã xem
        $excludedIds = ProductReview::where('user_id', $userId)
            ->pluck('product_id')
            ->toArray();

        return Product::whereIn('category_id', $cartCategories)
            ->whereNotIn('id', $excludedIds)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    return $this->getFallbackRecommendations();
}


    private function getCategoryBasedRecommendations($limit)
    {
        // Lấy các danh mục phổ biến dựa trên đơn hàng
        $popularCategoryIds = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->groupBy('products.category_id')
            ->orderBy(DB::raw('COUNT(*)'), 'desc')
            ->limit(3)
            ->pluck('products.category_id');

        return Product::whereIn('category_id', $popularCategoryIds)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    private function getPopularProducts($limit)
    {
        return Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit($limit)
            ->get();
    }

    // Có thể thêm các phương thức khác như dựa trên đánh giá, dựa trên sản phẩm tương tự, v.v.
}