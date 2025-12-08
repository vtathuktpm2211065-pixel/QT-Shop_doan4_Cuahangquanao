<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\ProductReviewImage;
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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return back()->with('error', 'Đơn hàng không hợp lệ.');
        }

        $orderItem = OrderItem::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$orderItem) {
            return back()->with('error', 'Sản phẩm không có trong đơn hàng.');
        }

        if (!$order->isDelivered()) {
            return back()->with('error', 'Chỉ có thể đánh giá khi đã giao hàng.');
        }

        $existingReview = ProductReview::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        try {
            $review = ProductReview::create([
                'user_id' => Auth::id(),
                'order_id' => $request->order_id,
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'pending',
            ]);

            // Lưu nhiều ảnh
         if ($request->hasFile('images')) {
    foreach ($request->file('images') as $img) {
        $path = $img->store('review_images', 'public');

        ProductReviewImage::create([
            'review_id' => $review->id,
            'image' => $path,
        ]);

}
         }
            $this->updateProductRating($request->product_id);

            return back()->with('success', 'Đánh giá thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        $review = ProductReview::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($review->created_at->diffInHours(now()) > 24) {
            return back()->with('error', 'Hết thời gian chỉnh sửa.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        try {
            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'pending',
            ]);

            // Thêm ảnh mới
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $path = $img->store('review_images', 'public');

                   ProductReviewImage::create([
    'review_id' => $review->id,
    'image' => $path,
]);

                }
            }

            $this->updateProductRating($review->product_id);

            return redirect()->route('reviews.user')->with('success', 'Cập nhật thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        $review = ProductReview::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            foreach ($review->images as $img) {
                Storage::disk('public')->delete($img->image);
                $img->delete();
            }

            $productId = $review->product_id;

            $review->delete();

            $this->updateProductRating($productId);

            return back()->with('success', 'Xóa thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    private function updateProductRating($productId)
    {
        $avg = ProductReview::where('product_id', $productId)
            ->where('status', 'approved')
            ->avg('rating');

        Product::where('id', $productId)->update([
            'average_rating' => round($avg, 1),
        ]);
    }
}
