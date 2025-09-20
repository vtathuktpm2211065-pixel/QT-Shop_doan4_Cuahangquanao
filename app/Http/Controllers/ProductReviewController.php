<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;



class ProductReviewController extends Controller
{
 public function store(Request $request)
{
    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string',
        'product_id' => 'required|exists:products,id',
        'order_id' => 'required|exists:orders,id',
        'image' => 'nullable|image|max:2048',
    ]);

    $review = new ProductReview();
    $review->user_id = Auth::id();
    $review->order_id = $request->order_id;
    $review->product_id = $request->product_id;
    $review->rating = $request->rating;
    $review->comment = $request->comment;

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('review_images', 'public');
        $review->image = $path;
    }

    $review->save();

    return redirect()->back()->with('success', 'Đánh giá thành công!');
}

public function showReviews($productId)
{
    $product = Product::with('reviews.user')->findOrFail($productId);
    return view('reviews.show', compact('product'));
}

}
