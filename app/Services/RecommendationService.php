<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\ProductView;
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

        // 1. Behavior-based
        if ($userId) {
            $behaviorBased = $this->getBehaviorBasedRecommendations($userId, $limit);
            $recommendations = $recommendations->merge($behaviorBased);
        }

        // 2. Similar products
        if ($recommendations->count() < $limit) {
            $similar = $this->getSimilarProducts($limit - $recommendations->count());
            $recommendations = $recommendations->merge($similar);
        }

        // 3. Category-based
        if ($recommendations->count() < $limit) {
            $categoryBased = $this->getCategoryBasedRecommendations($limit - $recommendations->count());
            $recommendations = $recommendations->merge($categoryBased);
        }

        // 4. Trending (7 ngày gần nhất)
        if ($recommendations->count() < $limit) {
            $trending = $this->getTrendingProducts($limit - $recommendations->count());
            $recommendations = $recommendations->merge($trending);
        }

        // 5. Top Rated
        if ($recommendations->count() < $limit) {
            $topRated = $this->getTopRatedProducts($limit - $recommendations->count());
            $recommendations = $recommendations->merge($topRated);
        }

        // 6. Popular
        if ($recommendations->count() < $limit) {
            $popular = $this->getPopularProducts($limit - $recommendations->count());
            $recommendations = $recommendations->merge($popular);
        }

        // 7. Fallback
        if ($recommendations->count() < $limit) {
            $fallback = $this->getFallbackRecommendations($limit - $recommendations->count());
            $recommendations = $recommendations->merge($fallback);
        }

        return $recommendations->unique('id')->take($limit);
    }


    /* =================================================
     |   1. Behavior-based Recommendation
    ================================================= */
    public function getBehaviorBasedRecommendations($userId, $limit = 8) 
    {
         $cartCategories = CartItem::where('user_id', $userId)
        ->with('product')
        ->get()
        ->pluck('product.category_id')
        ->filter()
        ->unique()
        ->toArray();

    // 2. Category từ review
    $reviewCategories = ProductReview::where('user_id', $userId)
        ->with('product')
        ->get()
        ->pluck('product.category_id')
        ->filter()
        ->unique()
        ->toArray();

    // 3. Kết hợp
    $categories = array_unique(array_merge($cartCategories, $reviewCategories));

    if (!empty($categories)) {
        $excludedIds = ProductReview::where('user_id', $userId)
            ->pluck('product_id')
            ->toArray();

        return Product::whereIn('category_id', $categories)
            ->whereNotIn('id', $excludedIds)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

        
        return collect();
    }


    /* =================================================
     |   2. Similar Products
    ================================================= */
    public function getSimilarProducts($limit = 6)
    {
        if (!Auth::check()) return collect();

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


    /* =================================================
     |   3. Category-based
    ================================================= */
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


    /* =================================================
     |   4. Trending Products
    ================================================= */
  private function getTrendingProducts($limit = 6)
    {
        return Product::withCount([
                'views' => function($q) {
                    $q->where('created_at', '>=', now()->subDays(7));
                }
            ])
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }


    /* =================================================
     |   5. Top Rated
    ================================================= */
  public function getTopRatedProducts($limit = 6)
    {
        return Product::withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'desc')
            ->limit($limit)
            ->get();
    }


    /* =================================================
     |   6. Popular Products
    ================================================= */
     public function getPopularProducts($limit = 6) 
    {
        return Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit($limit)
            ->get();
    }


    /* =================================================
     |   7. Fallback Random
    ================================================= */
   private function getFallbackRecommendations($limit = 6)
    {
        return Product::inRandomOrder()
            ->limit($limit)
            ->get();
    }
    
}
