<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
public function index()
{
    $products = Product::all();
    $sanPhamNoiBat = Product::where('noi_bat', true)->take(8)->get();
    $sanPhamPhoBien = Product::where('pho_bien', true)->take(8)->get();
    $mostRated = Product::inRandomOrder()->take(10)->get();

    return view('home', compact('products', 'sanPhamNoiBat', 'sanPhamPhoBien', 'mostRated'));
}

}
