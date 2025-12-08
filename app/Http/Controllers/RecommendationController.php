<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index($user, $product)
    {
        return response()->json([
            'user' => $user,
            'product' => $product,
            'recommendations' => [],
        ]);
    }
}
