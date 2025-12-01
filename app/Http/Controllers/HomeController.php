<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Services\RecommendationService;

class HomeController extends Controller
{
    protected $recService;

    public function __construct(RecommendationService $recService)
    {
        $this->recService = $recService;
    }

    public function index()
    {
     $recentViews = session()->get('recent_views', []);
    $recentProducts = collect();

    if(!empty($recentViews)) {
        $recentProducts = Product::whereIn('id', $recentViews)
            ->orderByRaw("FIELD(id, " . implode(',', $recentViews) . ")") // giữ đúng thứ tự
            ->get();
    }
        // --- 3. Các danh sách khác nếu muốn hiển thị ---
        $behaviorBased = $this->recService->getRecommendations(Auth::id(), 8);
        $categoryBased  = $this->recService->getCategoryBasedRecommendations(8);
        $popularProducts = $this->recService->getPopularProducts(6);
        $mostRated       = $this->recService->getTopRatedProducts(6);

        return view('home', compact(
             'recentProducts',
            'behaviorBased',
            'categoryBased',
            'popularProducts',
            'mostRated'
        ));
    }
}
