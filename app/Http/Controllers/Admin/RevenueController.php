<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Revenue;
use App\Models\ProductVariant;

class RevenueController extends Controller
{
    public function stockRevenue(Request $request)
{
    $date = $request->input('date', now()->toDateString());

    $revenues = Revenue::whereDate('date', $date)->get();

    $totalRevenue = $revenues->sum('total');

    return view('admin.revenue.stock_revenue', compact('revenues', 'totalRevenue', 'date'));
}
   public function daily(Request $request)
{
    $date = $request->input('date', now()->toDateString());
    $allowedStatuses = ['delivered'];

    // 1️⃣ Doanh thu bán hàng trong ngày
    $orderRevenue = Order::whereDate('created_at', $date)
        ->whereIn('status', $allowedStatuses)
        ->sum('total_amount');

    // 2️⃣ Doanh thu xuất kho thủ công trong ngày
    $exportRevenue = Revenue::whereDate('date', $date)
        ->where('type', 'export')
        ->sum('total');

    // Tổng doanh thu
    $revenue = $orderRevenue + $exportRevenue;

    // 3️⃣ Lấy top sản phẩm từ bán hàng
    $orderItems = OrderItem::whereHas('order', function ($q) use ($date, $allowedStatuses) {
            $q->whereDate('created_at', $date)
              ->whereIn('status', $allowedStatuses);
        })
        ->select('product_id', DB::raw('SUM(quantity) as sold'))
        ->groupBy('product_id')
        ->get();

    // 4️⃣ Lấy top sản phẩm từ xuất kho thủ công
    $exportItems = Revenue::whereDate('date', $date)
        ->where('type', 'export')
        ->select('variant_id', DB::raw('SUM(quantity) as sold'))
        ->groupBy('variant_id')
        ->get()
        ->map(function ($item) {
            $variant = ProductVariant::with('product')->find($item->variant_id);
            return [
                'product_id' => $variant?->product?->id,
                'sold' => $item->sold
            ];
        })
        ->filter(fn($i) => $i['product_id'] !== null);

    // 5️⃣ Gộp hai nguồn
    $merged = $orderItems->concat($exportItems)
        ->groupBy('product_id')
        ->map(function ($items) {
            return [
                'sold' => $items->sum('sold')
            ];
        })
        ->sortByDesc('sold')
        ->take(5);

    $products = Product::whereIn('id', $merged->keys())->get()->keyBy('id');

    $topProducts = $merged->map(function ($data, $productId) use ($products) {
        $product = $products[$productId] ?? null;
        if (!$product) return null;

        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->image,
            'sold' => $data['sold'],
            'change_percent' => rand(-10, 30),
        ];
    })->filter();

    $chartLabels = $topProducts->pluck('name');
    $chartData = $topProducts->pluck('sold');

    return view('admin.revenue.daily', compact(
        'revenue', 'date', 'topProducts', 'chartLabels', 'chartData'
    ));
}


public function monthly(Request $request)
{
    $month = $request->input('month', now()->format('Y-m'));
    $start = \Carbon\Carbon::parse($month)->startOfMonth();
    $end = \Carbon\Carbon::parse($month)->endOfMonth();
    $allowedStatuses = ['delivered'];

    /**
     * 1️⃣ Doanh thu bán hàng theo ngày
     */
    $orderDaily = Order::whereBetween('created_at', [$start, $end])
        ->whereIn('status', $allowedStatuses)
        ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
        ->groupBy(DB::raw('DATE(created_at)'))
        ->pluck('total', 'date');

    /**
     * 2️⃣ Doanh thu xuất kho thủ công theo ngày
     */
    $exportDaily = Revenue::whereBetween('date', [$start, $end])
        ->where('type', 'export')
        ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(total) as total'))
        ->groupBy(DB::raw('DATE(date)'))
        ->pluck('total', 'date');

    /**
     * 3️⃣ Gộp doanh thu theo ngày
     */
    $dates = collect($orderDaily->keys())->merge($exportDaily->keys())->unique()->sort();
    $dailyRevenueData = $dates->map(function ($day) use ($orderDaily, $exportDaily) {
        return [
            'date' => $day,
            'total' => ($orderDaily[$day] ?? 0) + ($exportDaily[$day] ?? 0),
        ];
    });

    $totalRevenue = $dailyRevenueData->sum('total');

    $dailyLabels = $dailyRevenueData->pluck('date')->map(function ($d) {
        return \Carbon\Carbon::parse($d)->format('d/m');
    });
    $dailyValues = $dailyRevenueData->pluck('total');

    /**
     * 4️⃣ Lấy top sản phẩm bán chạy trong tháng
     */
    // Từ bán hàng
    $orderItems = OrderItem::whereHas('order', function ($q) use ($start, $end, $allowedStatuses) {
            $q->whereBetween('created_at', [$start, $end])
              ->whereIn('status', $allowedStatuses);
        })
        ->select('product_id', DB::raw('SUM(quantity) as sold'), DB::raw('AVG(unit_price) as avg_price'))
        ->groupBy('product_id')
        ->get()
        ->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'sold' => $item->sold,
                'revenue' => $item->avg_price * $item->sold
            ];
        });

    // Từ xuất kho thủ công
    $exportItems = Revenue::whereBetween('date', [$start, $end])
        ->where('type', 'export')
        ->select('variant_id', DB::raw('SUM(quantity) as sold'), DB::raw('AVG(price) as avg_price'))
        ->groupBy('variant_id')
        ->get()
        ->map(function ($item) {
            $variant = ProductVariant::with('product')->find($item->variant_id);
            return [
                'product_id' => $variant?->product?->id,
                'sold' => $item->sold,
                'revenue' => $item->avg_price * $item->sold
            ];
        })
        ->filter(fn($i) => $i['product_id'] !== null);

    /**
     * 5️⃣ Gộp top sản phẩm
     */
    $merged = $orderItems->concat($exportItems)
        ->groupBy('product_id')
        ->map(function ($items) {
            return [
                'sold' => $items->sum('sold'),
                'revenue' => $items->sum('revenue')
            ];
        })
        ->sortByDesc('revenue')
        ->take(10);

    $products = Product::whereIn('id', $merged->keys())->get()->keyBy('id');

    $topProducts = $merged->map(function ($data, $productId) use ($products, $totalRevenue) {
        $product = $products[$productId] ?? null;
        if (!$product) return null;

        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->image,
            'sold' => $data['sold'],
            'revenue' => $data['revenue'],
            'percent' => $totalRevenue > 0 ? round(($data['revenue'] / $totalRevenue) * 100, 2) : 0,
            'stock' => method_exists($product, 'variants')
                ? $product->variants()->sum('stock_quantity')
                : ($product->stock_quantity ?? null)
        ];
    })->filter()->values();

    /**
     * 6️⃣ Dữ liệu biểu đồ
     */
    $chartLabels = $topProducts->pluck('name');
    $chartRevenue = $topProducts->pluck('revenue');
    $chartSold = $topProducts->pluck('sold');
    $quantityChartLabels = $topProducts->pluck('name');
    $quantityChartData = $topProducts->pluck('sold');

    return view('admin.revenue.monthly', compact(
        'month',
        'totalRevenue',
        'topProducts',
        'chartLabels',
        'chartRevenue',
        'chartSold',
        'quantityChartLabels',
        'quantityChartData',
        'dailyRevenueData',
        'dailyLabels',
        'dailyValues'
    ));
}

public function yearly(Request $request)
{
    $year = $request->input('year', Carbon::now()->format('Y'));
    $allowedStatuses = ['delivered'];

    /**
     * 1️⃣ Lấy doanh thu bán hàng theo tháng (Orders)
     */
    $orderMonthly = Order::whereYear('created_at', $year)
        ->whereIn('status', $allowedStatuses)
        ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_amount) as total'))
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->pluck('total', 'month');

    /**
     * 2️⃣ Lấy doanh thu xuất kho thủ công theo tháng (Revenues)
     */
    $exportMonthly = Revenue::whereYear('date', $year)
        ->where('type', 'export')
        ->select(DB::raw('MONTH(date) as month'), DB::raw('SUM(total) as total'))
        ->groupBy(DB::raw('MONTH(date)'))
        ->pluck('total', 'month');

    /**
     * 3️⃣ Gộp hai nguồn lại theo tháng
     */
    $monthlyRevenue = collect(range(1, 12))->mapWithKeys(function ($m) use ($orderMonthly, $exportMonthly) {
        $orderTotal = $orderMonthly[$m] ?? 0;
        $exportTotal = $exportMonthly[$m] ?? 0;
        return [$m => $orderTotal + $exportTotal];
    });

    $totalRevenue = $monthlyRevenue->sum();

    $chartLabels = collect(range(1, 12))->map(fn($m) => 'Tháng ' . $m);
    $chartRevenue = $monthlyRevenue->values();

    /**
     * 4️⃣ Chuẩn bị dữ liệu cho bảng & biểu đồ phụ (dailyChart trong view -> giờ là monthlyChart)
     */
    $dailyRevenueData = collect(range(1, 12))->map(function ($m) use ($monthlyRevenue) {
        return [
            'date' => sprintf('%02d', $m) . '/'. Carbon::now()->year, // ví dụ: 01/2025
            'total' => $monthlyRevenue[$m] ?? 0
        ];
    });

    $dailyLabels = collect(range(1, 12))->map(fn($m) => "Tháng $m");
    $dailyValues = $monthlyRevenue->values();

    /**
     * 5️⃣ Lấy top sản phẩm từ cả Orders + Revenues
     */
    // Top từ bán hàng
    $orderItems = OrderItem::whereHas('order', function ($query) use ($year, $allowedStatuses) {
            $query->whereYear('created_at', $year)
                  ->whereIn('status', $allowedStatuses);
        })
        ->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('AVG(unit_price) as avg_price'))
        ->groupBy('product_id')
        ->get()
        ->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'sold' => $item->total_sold,
                'revenue' => $item->avg_price * $item->total_sold
            ];
        });

    // Top từ xuất kho thủ công
    $exportItems = Revenue::whereYear('date', $year)
        ->where('type', 'export')
        ->select('variant_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('AVG(price) as avg_price'))
        ->groupBy('variant_id')
        ->get()
        ->map(function ($item) {
            $variant = ProductVariant::with('product')->find($item->variant_id);
            return [
                'product_id' => $variant?->product?->id,
                'sold' => $item->total_sold,
                'revenue' => $item->avg_price * $item->total_sold
            ];
        })
        ->filter(fn($i) => $i['product_id'] !== null);

    /**
     * 6️⃣ Gộp top sản phẩm từ cả hai nguồn
     */
    $mergedProducts = $orderItems->concat($exportItems)
        ->groupBy('product_id')
        ->map(function ($items) {
            $sold = $items->sum('sold');
            $revenue = $items->sum('revenue');
            return [
                'sold' => $sold,
                'revenue' => $revenue
            ];
        });

    $products = Product::whereIn('id', $mergedProducts->keys())->get()->keyBy('id');

    $topProducts = $mergedProducts->map(function ($data, $productId) use ($products, $totalRevenue) {
        $product = $products[$productId] ?? null;
        if (!$product) return null;

        $percent = $totalRevenue > 0 ? round(($data['revenue'] / $totalRevenue) * 100, 1) : 0;
        $stock = method_exists($product, 'variants')
            ? $product->variants()->sum('stock_quantity')
            : ($product->stock_quantity ?? null);

        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->image,
            'sold' => $data['sold'],
            'revenue' => $data['revenue'],
            'percent' => $percent,
            'stock' => $stock,
        ];
    })->filter()->sortByDesc('revenue')->values();

    $quantityChartLabels = $topProducts->pluck('name');
    $quantityChartData = $topProducts->pluck('sold');
    $productRevenueData = $topProducts->pluck('revenue');

    /**
     * 7️⃣ Trả dữ liệu ra view
     */
    return view('admin.revenue.yearly', compact(
        'year',
        'totalRevenue',
        'chartLabels',
        'chartRevenue',
        'topProducts',
        'quantityChartLabels',
        'quantityChartData',
        'productRevenueData',
        'dailyRevenueData',
        'dailyLabels',
        'dailyValues'
    ));
}

}
