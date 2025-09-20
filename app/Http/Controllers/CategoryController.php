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
}
