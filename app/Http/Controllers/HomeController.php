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

        if (!empty($recentViews)) {
            $recentProducts = Product::whereIn('id', $recentViews)
                ->orderByRaw("FIELD(id, " . implode(',', $recentViews) . ")")
                ->get();

            
            foreach ($recentProducts as $p) {
                $p->source = 'recent';
            }
        }

        
        $behaviorBased = $this->recService->getRecommendations(Auth::id(), 8);
        foreach ($behaviorBased as $p) {
            $p->source = 'behavior';
        }

        $categoryBased = $this->recService->getCategoryBasedRecommendations(8);
        foreach ($categoryBased as $p) {
            $p->source = 'category';
        }

        $popularProducts = $this->recService->getPopularProducts(6);
        foreach ($popularProducts as $p) {
            $p->source = 'popular';
        }

        $mostRated = $this->recService->getTopRatedProducts(6);
        foreach ($mostRated as $p) {
            $p->source = 'top_rated';
        }

        $collaborative = $this->recService->getCollaborativeRecommendations(Auth::id(), 8);
        foreach ($collaborative as $p) {
            $p->source = 'collab';
        }

        $combined = collect()
            ->merge($recentProducts)
            ->merge($behaviorBased)
            ->merge($categoryBased)
            ->merge($popularProducts)
            ->merge($mostRated)
            ->merge($collaborative)
            ->unique('id') 
            ->take(20);   

       return view('home', [
    'recentProducts' => $recentProducts,
    'behaviorBased'  => $behaviorBased,
    'categoryBased'  => $categoryBased,
    'popularProducts' => $popularProducts,
    'mostRated' => $mostRated,
    'collaborative' => $collaborative,
    'combined' => $combined,
]);

    }
}
