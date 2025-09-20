<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminProductContentController extends Controller
{
    public function index()
    {
        return view('admin.products.description'); // Hoặc đường dẫn Blade tùy bạn
    }
}
