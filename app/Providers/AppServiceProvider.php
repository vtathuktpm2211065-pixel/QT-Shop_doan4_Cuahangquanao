<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\CartItem;
use App\Models\SupportReply;
use App\Models\SupportRequest;
use App\Models\ShopLocation;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // 👉 Gộp tất cả logic dùng cho mọi view vào 1 composer
        View::composer('*', function ($view) {
            // 🛒 Giỏ hàng
            $userId = Auth::check() ? Auth::id() : Session::getId();
            $totalQuantity = CartItem::where('user_id', $userId)->sum('quantity');
            $view->with('totalQuantity', $totalQuantity);

            // 💬 Số lượng phản hồi chưa đọc (chung)
            $unreadCount = 0;
            if (Auth::check()) {
                $unreadCount = SupportReply::whereHas('request', function ($q) {
                    $q->where('user_id', Auth::id());
                })->where('is_read', false)->count();
            }
            $view->with('unreadCount', $unreadCount);

            // 💌 Số lượng phản hồi từ admin chưa đọc (riêng support)
            $supportUnreadCount = 0;
            if (Auth::check()) {
                $supportRequest = SupportRequest::where('user_id', Auth::id())->first();
                if ($supportRequest) {
                    $supportUnreadCount = $supportRequest->replies()
                        ->where('is_admin', true)
                        ->where('is_read', false)
                        ->count();
                }
            }
            $view->with('supportUnreadCount', $supportUnreadCount);

            // 🏬 Vị trí cửa hàng (cho layout app.blade.php)
            try {
                $shopLocations = ShopLocation::all();
            } catch (\Exception $e) {
                $shopLocations = collect(); // tránh lỗi nếu bảng chưa có
            }
            $view->with('shopLocations', $shopLocations);
        });

        // 👉 Composer riêng cho sidebar admin
        View::composer('admin.partials.sidebar', function ($view) {
            $req = null;
            if (Auth::check()) {
                $req = SupportRequest::where('user_id', Auth::id())
                    ->with('replies')
                    ->latest()
                    ->first();
            }
            $view->with('req', $req);
        });
    }
}
