<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\ProductView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RecommendationService
{
    public function getRecommendations($userId = null, $limit = 10)
    {
        if (!$userId && Auth::check()) {
            $userId = Auth::id();
        }

        $recommendations = collect();

        if ($userId) {
            $behaviorBased = $this->getBehaviorBasedRecommendations($userId, $limit);
            $recommendations = $recommendations->merge($behaviorBased);
        }

        if ($recommendations->count() < $limit) {
            $similar = $this->getSimilarProducts($limit - $recommendations->count());
            $recommendations = $recommendations->merge($similar);
        }

        if ($recommendations->count() < $limit) {
            $categoryBased = $this->getCategoryBasedRecommendations($limit - $recommendations->count());
            $recommendations = $recommendations->merge($categoryBased);
        }

        if ($recommendations->count() < $limit) {
            $trending = $this->getTrendingProducts($limit - $recommendations->count());
            $recommendations = $recommendations->merge($trending);
        }

        if ($recommendations->count() < $limit) {
            $topRated = $this->getTopRatedProducts($limit - $recommendations->count());
            $recommendations = $recommendations->merge($topRated);
        }

        if ($recommendations->count() < $limit) {
            $popular = $this->getPopularProducts($limit - $recommendations->count());
            $recommendations = $recommendations->merge($popular);
        }
if ($userId && $recommendations->count() < $limit) {
    $collab = $this->getCollaborativeRecommendations($userId, $limit - $recommendations->count());
    $recommendations = $recommendations->merge($collab);
}

        
        if ($recommendations->count() < $limit) {
            $fallback = $this->getFallbackRecommendations($limit - $recommendations->count());
            $recommendations = $recommendations->merge($fallback);
        }
      


        return $recommendations->unique('id')->take($limit);
    }


  
   public function getBehaviorBasedRecommendations($userId, $limit = 8) 
{
    // Lấy category từ giỏ hàng
    $cartCategories = CartItem::where('user_id', $userId)
        ->with('variant.product')
        ->get()
        ->map(function($item){
            return $item->variant->product->category_id ?? null;
        })
        ->filter()
        ->unique()
        ->toArray();

    // Lấy category từ sản phẩm user đã đánh giá
    $reviewCategories = ProductReview::where('user_id', $userId)
        ->with('product')
        ->get()
        ->map(function($r){
            return $r->product->category_id ?? null;
        })
        ->filter()
        ->unique()
        ->toArray();

    // Gộp 2 loại category
    $categories = array_unique(array_merge($cartCategories, $reviewCategories));

    // Nếu không có danh mục -> không có gợi ý theo hành vi
    if (empty($categories)) {
        return collect();
    }

    // Gợi ý các sản phẩm trong các danh mục này
    return Product::whereIn('category_id', $categories)
        ->inRandomOrder()
        ->limit($limit)
        ->get();
}

    public function getSimilarProducts($limit = 6)
    {
        if (!Auth::check()) return collect();

        // If product_views table doesn't exist yet (migrations not run), skip similar recommendations
        if (!Schema::hasTable('product_views')) {
            return collect();
        }

        $lastView = ProductView::where('user_id', Auth::id())
            ->latest()
            ->with('product')
            ->first();

        if (!$lastView) return collect();

        $product = $lastView->product;

        return Product::where('category_id', $product->category_id)
            ->whereBetween('price', [$product->price * 0.8, $product->price * 1.2])
            ->where("id", "!=", $product->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }


    
    public function getCategoryBasedRecommendations($limit = 6)
    {
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


  private function getTrendingProducts($limit = 6)
    {
        // If product_views table doesn't exist, avoid running withCount which builds a subquery
        if (!Schema::hasTable('product_views')) {
            // Fallback: return newest products as a reasonable approximation for "trending"
            return Product::orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }

        return Product::withCount([
                'views' => function($q) {
                    $q->where('created_at', '>=', now()->subDays(7));
                }
            ])
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }


   
  public function getTopRatedProducts($limit = 6)
    {
        return Product::withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'desc')
            ->limit($limit)
            ->get();
    }


   
     public function getPopularProducts($limit = 6) 
    {
        return Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit($limit)
            ->get();
    }


   
   private function getFallbackRecommendations($limit = 6)
    {
        return Product::inRandomOrder()
            ->limit($limit)
            ->get();
    }
    public function getCollaborativeRecommendations($userId, $limit = 6)
{
    // 1. Sản phẩm user này đã mua
    $userProductIds = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('orders.user_id', $userId)
        ->pluck('order_items.product_id')
        ->unique()
        ->toArray();

    if (empty($userProductIds)) {
        return collect();
    }

    // 2. Tìm user khác cũng mua cùng sản phẩm
    $similarUsers = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
        ->whereIn('order_items.product_id', $userProductIds)
        ->where('orders.user_id', '!=', $userId)
        ->pluck('orders.user_id')
        ->unique()
        ->toArray();

    if (empty($similarUsers)) {
        return collect();
    }

    // 3. Lấy các sản phẩm mà các user đó đã mua thêm
    $recommendedProductIds = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
        ->whereIn('orders.user_id', $similarUsers)
        ->whereNotIn('order_items.product_id', $userProductIds)
        ->pluck('order_items.product_id')
        ->unique()
        ->toArray();

    return Product::whereIn('id', $recommendedProductIds)
        ->limit($limit)
        ->get();
}

}
