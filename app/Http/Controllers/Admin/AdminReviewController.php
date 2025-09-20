<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;


class AdminReviewController extends Controller
{
public function index()
{
    $reviews = ProductReview::with('product', 'user')->latest()->paginate(10);
    return view('admin.reviews.index', compact('reviews'));
}

public function destroy($id)
{
    $review = ProductReview::findOrFail($id);
    $review->delete();

    return redirect()->back()->with('success', 'Đã xóa đánh giá.');
}

public function reply(Request $request, $id)
{
    $request->validate([
        'admin_reply' => 'required|string|max:2000'
    ]);

    $review = ProductReview::findOrFail($id);
    $review->admin_reply = $request->admin_reply;
    $review->save();

    return redirect()->back()->with('success', 'Đã phản hồi đánh giá.');
}


}