<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($slug)
    {
        // Lấy dữ liệu category theo slug, ví dụ:
        // $category = Category::where('slug', $slug)->firstOrFail();

        // Trả về view, truyền dữ liệu nếu cần
        return view('category.show', compact('slug'));
    }
    public function index(Request $request)
{
    $query = Category::withCount('products');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('slug', 'like', '%' . $search . '%');
        });
    }

    $categories = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('admin.categories.index', compact('categories'));
}

}
