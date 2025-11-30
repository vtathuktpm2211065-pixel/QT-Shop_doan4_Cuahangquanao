<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Kiểm tra xem user có sở hữu order này không
        $order = Order::where('id', $request->order_id)
                     ->where('user_id', Auth::id())
                     ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Đơn hàng không tồn tại hoặc bạn không có quyền đánh giá đơn hàng này.');
        }

        // Kiểm tra xem order có chứa sản phẩm này không
        $orderItem = OrderItem::where('order_id', $request->order_id)
                             ->where('product_id', $request->product_id)
                             ->first();

        if (!$orderItem) {
            return redirect()->back()->with('error', 'Sản phẩm không có trong đơn hàng này.');
        }

        // Kiểm tra xem order đã được giao hàng chưa
        if (!$order->isDelivered()) {
            return redirect()->back()->with('error', 'Chỉ có thể đánh giá sản phẩm sau khi đơn hàng đã được giao.');
        }

        // Kiểm tra xem đã review sản phẩm này trong order này chưa
        $existingReview = ProductReview::where('order_id', $request->order_id)
                                     ->where('product_id', $request->product_id)
                                     ->where('user_id', Auth::id())
                                     ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'Bạn đã đánh giá sản phẩm này trong đơn hàng này rồi.');
        }

        // Kiểm tra thời gian đánh giá (ví dụ: trong vòng 30 ngày kể từ khi giao hàng)
        if ($order->delivered_at && $order->delivered_at->diffInDays(now()) > 30) {
            return redirect()->back()->with('error', 'Thời gian đánh giá sản phẩm đã hết (30 ngày kể từ khi nhận hàng).');
        }

        try {
            $review = new ProductReview();
            $review->user_id = Auth::id();
            $review->order_id = $request->order_id;
            $review->product_id = $request->product_id;
            $review->rating = $request->rating;
            $review->comment = $request->comment;
            $review->status = 'pending'; // hoặc 'approved' tùy cài đặt

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('review_images', 'public');
                $review->image = $path;
            }

            $review->save();

            // Cập nhật rating trung bình cho sản phẩm
            $this->updateProductRating($request->product_id);

            return redirect()->back()->with('success', 'Đánh giá thành công! Cảm ơn bạn đã đóng góp ý kiến.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function showReviews($productId)
    {
        $product = Product::with(['reviews.user', 'reviews' => function($query) {
            $query->where('status', 'approved')
                  ->orderBy('created_at', 'desc');
        }])->findOrFail($productId);

        // Tính toán thống kê rating
        $ratingStats = $this->getRatingStatistics($productId);
        
        // Lấy reviews gần đây
        $recentReviews = ProductReview::with('user')
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('reviews.show', compact('product', 'ratingStats', 'recentReviews'));
    }

    public function userReviews()
    {
        $reviews = ProductReview::with('product')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reviews.user-reviews', compact('reviews'));
    }

    public function edit($id)
    {
        $review = ProductReview::where('id', $id)
                              ->where('user_id', Auth::id())
                              ->firstOrFail();

        // Chỉ cho phép chỉnh sửa trong vòng 24h
        if ($review->created_at->diffInHours(now()) > 24) {
            return redirect()->back()->with('error', 'Chỉ có thể chỉnh sửa đánh giá trong vòng 24 giờ sau khi đăng.');
        }

        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        $review = ProductReview::where('id', $id)
                              ->where('user_id', Auth::id())
                              ->firstOrFail();

        // Chỉ cho phép chỉnh sửa trong vòng 24h
        if ($review->created_at->diffInHours(now()) > 24) {
            return redirect()->back()->with('error', 'Đã hết thời gian chỉnh sửa đánh giá.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $review->rating = $request->rating;
            $review->comment = $request->comment;
            $review->status = 'pending'; // Đưa về trạng thái chờ duyệt nếu cần

            if ($request->hasFile('image')) {
                // Xóa ảnh cũ nếu có
                if ($review->image) {
                    Storage::disk('public')->delete($review->image);
                }
                
                $path = $request->file('image')->store('review_images', 'public');
                $review->image = $path;
            }

            $review->save();

            // Cập nhật lại rating trung bình
            $this->updateProductRating($review->product_id);

            return redirect()->route('reviews.user')->with('success', 'Cập nhật đánh giá thành công!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $review = ProductReview::where('id', $id)
                              ->where('user_id', Auth::id())
                              ->firstOrFail();

        try {
            $productId = $review->product_id;
            
            // Xóa ảnh nếu có
            if ($review->image) {
                Storage::disk('public')->delete($review->image);
            }
            
            $review->delete();

            // Cập nhật lại rating trung bình
            $this->updateProductRating($productId);

            return redirect()->back()->with('success', 'Xóa đánh giá thành công!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật rating trung bình cho sản phẩm
     */
    private function updateProductRating($productId)
    {
        $product = Product::find($productId);
        
        if ($product) {
            $averageRating = ProductReview::where('product_id', $productId)
                                         ->where('status', 'approved')
                                         ->avg('rating');
            
            $product->average_rating = round($averageRating, 1);
            $product->save();
        }
    }

    /**
     * Lấy thống kê rating cho sản phẩm
     */
    private function getRatingStatistics($productId)
    {
        return ProductReview::where('product_id', $productId)
                           ->where('status', 'approved')
                           ->selectRaw('
                               COUNT(*) as total_reviews,
                               AVG(rating) as average_rating,
                               COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                               COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                               COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                               COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                               COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
                           ')
                           ->first();
    }

    /**
     * API để lấy reviews với phân trang
     */
    public function apiReviews($productId, Request $request)
    {
        $reviews = ProductReview::with('user')
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'reviews' => $reviews,
            'rating_stats' => $this->getRatingStatistics($productId)
        ]);
    }
}