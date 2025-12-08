<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
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
            $totalQuantity = 0;
            if (Schema::hasTable('cart_items')) {
                try {
                    $totalQuantity = CartItem::where('user_id', $userId)->sum('quantity');
                } catch (\Exception $e) {
                    $totalQuantity = 0; // Guard if DB isn't ready
                }
            }
            $view->with('totalQuantity', $totalQuantity);

            // 💬 Số lượng phản hồi chưa đọc (chung)
            $unreadCount = 0;
            if (Auth::check() && Schema::hasTable('support_replies') && Schema::hasTable('support_requests')) {
                try {
                    $unreadCount = SupportReply::whereHas('request', function ($q) {
                        $q->where('user_id', Auth::id());
                    })->where('is_read', false)->count();
                } catch (\Exception $e) {
                    $unreadCount = 0;
                }
            }
            $view->with('unreadCount', $unreadCount);

            // 💌 Số lượng phản hồi từ admin chưa đọc (riêng support)
            $supportUnreadCount = 0;
            if (Auth::check() && Schema::hasTable('support_requests') && Schema::hasTable('support_replies')) {
                try {
                    $supportRequest = SupportRequest::where('user_id', Auth::id())->first();
                    if ($supportRequest) {
                        $supportUnreadCount = $supportRequest->replies()
                            ->where('is_admin', true)
                            ->where('is_read', false)
                            ->count();
                    }
                } catch (\Exception $e) {
                    $supportUnreadCount = 0;
                }
            }
            $view->with('supportUnreadCount', $supportUnreadCount);

            // 🏬 Vị trí cửa hàng (cho layout app.blade.php)
            $shopLocations = collect();
            if (Schema::hasTable('shop_locations')) {
                try {
                    $shopLocations = ShopLocation::all();
                } catch (\Exception $e) {
                    $shopLocations = collect();
                }
            }
            $view->with('shopLocations', $shopLocations);
        });

        // 👉 Composer riêng cho sidebar admin
        View::composer('admin.partials.sidebar', function ($view) {
            $req = null;
            if (Auth::check() && Schema::hasTable('support_requests')) {
                try {
                    $req = SupportRequest::where('user_id', Auth::id())
                        ->with('replies')
                        ->latest()
                        ->first();
                } catch (\Exception $e) {
                    $req = null;
                }
            }
            $view->with('req', $req);
        });
    }
}
