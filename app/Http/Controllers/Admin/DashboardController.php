<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Cart;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\OrderItem;
use App\Models\ProductVariant;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Biến cơ bản
            $products = Product::count();
            $vouchers = Voucher::count();
            $carts = Cart::where('status', 'active')->count();
            $orders = Order::count();
                $stocks = ProductVariant::sum('stock_quantity');
       \Log::info('Basic counts: products=' . $products . ', vouchers=' . $vouchers . ', carts=' . $carts . ', orders=' . $orders . ', stocks=' . $stocks);

            $allowedStatuses = ['pending', 'processing', 'shipped', 'completed'];
            $currentMonth = Carbon::now()->format('Y-m'); // 2025-07
            $currentYear = Carbon::now()->year; // 2025

            // Top 5 sản phẩm
            // Make date filtering driver-aware (sqlite doesn't support YEAR()/MONTH())
            $driver = DB::getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);

            $topProductsQuery = OrderItem::whereHas('order', function ($query) use ($currentMonth, $allowedStatuses, $driver) {
                if ($driver === 'sqlite') {
                    $year = substr($currentMonth, 0, 4);
                    $month = substr($currentMonth, 5, 2);
                    $query->whereRaw("strftime('%Y', created_at) = ?", [$year])
                          ->whereRaw("strftime('%m', created_at) = ?", [$month])
                          ->whereIn('status', $allowedStatuses);
                } else {
                    $query->whereYear('created_at', substr($currentMonth, 0, 4))
                          ->whereMonth('created_at', substr($currentMonth, 5, 2))
                          ->whereIn('status', $allowedStatuses);
                }
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('products')
                      ->whereColumn('products.id', 'order_items.product_id');
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id');

            $topProducts = $topProductsQuery->get(); // Đảm bảo gán giá trị
            Log::info('Raw topProducts query result:', $topProducts->toArray());

            if ($topProducts->isEmpty()) {
                $topProducts = collect([]); // Gán Collection rỗng nếu không có dữ liệu
            }

            $topProducts = $topProducts->map(function ($item) {
                $product = Product::find($item->product_id);
                $price = $product ? ((float)$product->price > 0 ? (float)$product->price : 0) : 0;
                return [
                    'name' => $product ? $product->name : 'Sản phẩm không xác định',
                    'revenue' => $price * $item->total_sold,
                ];
            })
            ->filter(function ($item) {
                return $item['revenue'] > 0;
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

            $chartLabels = $topProducts->isNotEmpty() ? $topProducts->pluck('name')->all() : [];
            $chartData = $topProducts->isNotEmpty() ? $topProducts->pluck('revenue')->all() : [];
            Log::info('Chart data: labels=' . json_encode($chartLabels) . ', data=' . json_encode($chartData));

            // Doanh thu theo tháng (driver-aware)
            if (isset($driver) && $driver === 'sqlite') {
                // SQLite: use strftime for year/month
                $monthlyData = Order::whereRaw("strftime('%Y', created_at) = ?", [$currentYear])
                    ->whereIn('status', $allowedStatuses)
                    ->selectRaw("strftime('%m', created_at) as month, SUM(total_amount) as total")
                    ->groupBy(DB::raw("strftime('%m', created_at)"))
                    ->pluck('total', 'month')
                    ->toArray();
            } else {
                // MySQL / others: use MONTH()
                $monthlyData = Order::whereYear('created_at', $currentYear)
                    ->whereIn('status', $allowedStatuses)
                    ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
                    ->groupBy(DB::raw('MONTH(created_at)'))
                    ->pluck('total', 'month')
                    ->toArray();
            }
            Log::info('Monthly data:', $monthlyData);

            $yearlySales = array_fill(1, 12, 0);
            foreach ($monthlyData as $month => $total) {
                $yearlySales[$month] = (float)$total;
            }
            Log::info('Yearly sales:', $yearlySales);

            return view('admin.dashboard', compact(
                'products', 'vouchers', 'carts', 'orders',
                'chartLabels', 'chartData', 'yearlySales','stocks'
            ));
        } catch (\Exception $e) {
            Log::error('Error in DashboardController: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            dd($e->getMessage(), $e->getTraceAsString());
        }
    }
}