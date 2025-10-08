<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;


class AdminReviewController extends Controller
{
public function index(Request $request)
{
    $query = ProductReview::with(['product', 'user'])->latest();

    if ($request->filled('product')) {
        $query->whereHas('product', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->product . '%');
        });
    }

    if ($request->filled('user')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->user . '%');
        });
    }

    $reviews = $query->paginate(10);

    return view('admin.reviews.index', compact('reviews')); // không cần truyền $request
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