<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\CartItem;
use App\Models\SupportReply;
use App\Models\SupportRequest;

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
            // Giỏ hàng
            $userId = Auth::check() ? Auth::id() : Session::getId();
            $totalQuantity = CartItem::where('user_id', $userId)->sum('quantity');
            $view->with('totalQuantity', $totalQuantity);

            // Số lượng phản hồi chưa đọc
            if (Auth::check()) {
                $unreadCount = SupportReply::whereHas('request', function($q) {
                    $q->where('user_id', Auth::id());
                })->where('is_read', false)->count();
                $view->with('unreadCount', $unreadCount);
            }
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
