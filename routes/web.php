<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SanPhamNoiBatController;
use App\Http\Controllers\San_pham_nuController;
use App\Http\Controllers\SanPhamNamController;
use App\Http\Controllers\SanPhamTreEmController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminCartController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminProductVariantController;
use App\Http\Controllers\Admin\AdminStockController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminProductContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\AdminTeamController;
use App\Models\TeamMember;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\HoSoController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\MomoController; 
Auth::routes();
Route::get('/', function () {
    return redirect('/home');
});

// ✅ Trang home KHÔNG yêu cầu đăng nhập
Route::get('/home', [HomeController::class, 'index'])->name('home');

// ✅ Đăng xuất
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');



// Tìm kiếm sản phẩm 
Route::get('/search', [ProductController::class, 'search'])->name('search');

// Trang giới thiệu 

Route::get('/gioi-thieu', function () {
    
    $members = TeamMember::all();
    return view('about', compact('members'));      
})->name('gioithieu');



    Route::get('/hoso', [UserController::class, 'profile'])->name('hoso.index');
    Route::put('/hoso', [UserController::class, 'updateProfile'])->name('hoso.update');
    Route::put('/hoso/password', [UserController::class, 'changePassword'])->name('hoso.changePassword');


Route::get('/support', [SupportController::class, 'index'])->name('support.index');
Route::get('/support/form', [SupportController::class, 'createForm'])->name('support.form');
Route::post('/support/submit', [SupportController::class, 'submit'])->name('support.submit');
Route::delete('/support/{id}', [SupportController::class, 'delete'])->name('support.delete');
Route::post('/support/send', [SupportController::class, 'submit'])->name('support.send');
Route::post('/support/{id}/reply', [SupportController::class, 'sendReply'])->name('support.reply');
Route::get('/support/unread/check', [SupportController::class, 'checkUnread'])->name('support.unread.check');
Route::post('/support/{id}/mark-read', [SupportController::class, 'markAsRead'])->name('support.mark.read');
Route::get('/support/{id}/messages', [SupportController::class, 'getNewMessages'])->name('support.messages');
Route::post('/support/save-fcm-token', [SupportController::class, 'saveFCMToken'])->name('support.save-fcm-token');
// Danh mục sản phẩm
Route::get('/san-pham/noi-bat', [SanPhamNoiBatController::class, 'index'])->name('san-pham.noi-bat');
Route::get('/san-pham/cho-nu', [San_pham_nuController::class, 'index'])->name('san-pham.cho-nu');
Route::get('/san-pham/cho-nam', [SanPhamNamController::class, 'index'])->name('san-pham.cho-nam');
Route::get('/san-pham/cho-tre-em', [SanPhamTreEmController::class, 'index'])->name('san-pham.cho-tre-em');
Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/{slug}', [ProductController::class, 'chi_tiet'])->name('chi_tiet');

// Trang tĩnh
Route::view('/new-arrivals', 'new-arrivals')->name('new-arrivals');
Route::view('/contact', 'contact')->name('contact');

// Giỏ hàng (không bắt buộc login)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{slug}', [CartController::class, 'addBySlug'])->name('cart.add.slug');
Route::get('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::post('/cart/update-variant/{id}', [CartController::class, 'updateVariant']);
Route::post('/checkout/shipping-fee', [OrderController::class, 'getShippingFee'])->name('checkout.shipping_fee');
Route::post('/calculate-shipping', [OrderController::class, 'calculateShippingAjax'])->name('calculate.shipping');
Route::post('/calculate-shipping-ajax', [OrderController::class, 'calculateShippingAjax'])
    ->name('calculate-shipping-ajax');
    Route::post('/voucher/apply', [OrderController::class, 'applyVoucher'])->name('apply.voucher');

// Đặt hàng (không bắt buộc login)
Route::get('/checkout', [OrderController::class, 'showCheckout'])->name('checkout');
Route::post('/checkout', [OrderController::class, 'processCheckout'])->name('checkout.process');
Route::post('/order/place', [OrderController::class, 'placeOrder'])->name('order.place');


// Mua ngay
Route::post('/buy-now/{slug}', [CartController::class, 'buyNow'])->name('cart.buyNow');

// Giao diện và địa chỉ
Route::get('/api/provinces', [LocationController::class, 'getProvinces']);
Route::get('/api/districts', [LocationController::class, 'getDistricts']);
Route::get('/api/wards', [LocationController::class, 'getWards']);
Route::get('/api/provinces/{code}', [LocationController::class, 'getDistrictsByProvinceCode']);
Route::get('/api/districts/{code}', [LocationController::class, 'getWardsByDistrictCode']);
Route::get('/api/addresses/{id}', [AddressController::class, 'show'])->middleware('auth');

// Đặt lại đơn
Route::post('/orders/{id}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');

// Form tra cứu
Route::get('/tra-cuu-don-hang', [\App\Http\Controllers\OrderTrackingController::class, 'form'])->name('guest.track_order_form');
Route::post('/tra-cuu-don-hang/{order}/cancel', [\App\Http\Controllers\OrderTrackingController::class, 'cancel'])
     ->name('guest.cancel_order');
// Xử lý tra cứu
Route::post('/tra-cuu-don-hang', [\App\Http\Controllers\OrderTrackingController::class, 'lookup'])->name('guest.track_order');

// Quản lý đơn hàng - chỉ người dùng đã đăng nhập
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

// Admin
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Quản lý người dùng
    Route::get('/tao-tai-khoan', [UserController::class, 'create'])->name('createUser');
    Route::post('/tao-tai-khoan', [UserController::class, 'storeUser'])->name('users.store');
    Route::get('/phan-quyen', [PermissionController::class, 'phanQuyen'])->name('phanquyen');
    Route::patch('/chan-tai-khoan/{id}', [PermissionController::class, 'banUser'])->name('banUser');
    Route::patch('/mo-chan/{id}', [PermissionController::class, 'unbanUser'])->name('unbanUser');
    Route::delete('/xoa-tai-khoan/{id}', [PermissionController::class, 'deleteUser'])->name('deleteUser');
    Route::get('/bi-chan', [PermissionController::class, 'danhSachBiChan'])->name('bannedList');
    Route::patch('/nguoi-dung/{id}/chan-hoac-mo', [AdminController::class, 'toggleBanUser'])->name('chanMoNguoiDung');
    Route::get('/nguoi-dung/{id}/sua-thong-tin', [AdminController::class, 'editUserInfo'])->name('editUserInfo');
    Route::put('/nguoi-dung/{id}/cap-nhat-thong-tin', [AdminController::class, 'updateUserInfo'])->name('updateUserInfo');

    // Phân quyền & vai trò
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::get('/permissions/{id}/functions', [PermissionController::class, 'functions'])->name('permissions.functions');
    Route::get('/sua-quyen/{id}', [PermissionController::class, 'editUserRole'])->name('editUserRole');
    Route::put('/cap-nhat-quyen/{id}', [PermissionController::class, 'updateUserRole'])->name('updateUserRole');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles');

  
        Route::get('users', [PermissionController::class, 'index'])->name('users');
        Route::post('users/{user}/assign-role', [PermissionController::class, 'assignRole'])->name('assignRole');
        Route::post('users/{user}/assign-permission', [PermissionController::class, 'assignPermission'])->name('assignPermission');

    // Báo cáo doanh thu
    Route::prefix('revenue')->name('revenue.')->group(function () {
        Route::get('/daily', [RevenueController::class, 'daily'])->name('daily');
        Route::get('/monthly', [RevenueController::class, 'monthly'])->name('monthly');
        Route::get('/yearly', [RevenueController::class, 'yearly'])->name('yearly');

    });
   
Route::get('/admin/revenue-stock', [App\Http\Controllers\Admin\AdminStockController::class, 'revenueStock'])
    ->name('admin.revenue.stock');


    // Quản lý giỏ hàng
    Route::get('/gio-hang', [AdminCartController::class, 'index'])->name('carts.index');
    Route::get('/gio-hang/{id}', [AdminCartController::class, 'show'])->name('carts.show');

    Route::get('/carts/thong-ke', [AdminCartController::class, 'statistics'])->name('carts.statistics');


    // Quản lý sản phẩm
    Route::resource('san-pham', AdminProductController::class);

    // Biến thể sản phẩm
    Route::get('/san-pham/{id}/bien-the', [AdminProductVariantController::class, 'index'])->name('san-pham.bien-the');
    Route::post('/san-pham/{id}/bien-the', [AdminProductVariantController::class, 'store'])->name('san-pham.bien-the.store');
    Route::put('/bien-the/{id}', [AdminProductVariantController::class, 'update'])->name('bien-the.update');
    Route::delete('/bien-the/{id}', [AdminProductVariantController::class, 'destroy'])->name('bien-the.destroy');
    Route::get('/bien-the', [AdminProductVariantController::class, 'all'])->name('product_variants.index');
    Route::post('/bien-the/xoa-nhieu', [AdminProductVariantController::class, 'bulkDelete'])->name('bien-the.bulkDelete');
    Route::get('/bien-the/{id}/edit', [AdminProductVariantController::class, 'edit'])->name('bien-the.edit');

    // Quản lý danh mục
    Route::resource('danh-muc', AdminCategoryController::class);

 Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::put('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

    // Quản lý kho
    Route::get('/kho', [AdminStockController::class, 'index'])->name('stock.index');
    Route::get('/kho/nhap', [AdminStockController::class, 'import'])->name('stock.import');
    Route::get('/kho/xuat', [AdminStockController::class, 'export'])->name('stock.export');
    Route::get('/kho/dong-bo', [AdminStockController::class, 'sync'])->name('stock.sync');
    Route::post('/kho/nhap', [AdminStockController::class, 'storeImport'])->name('stock.storeImport');
    Route::post('/kho/xuat', [AdminStockController::class, 'storeExport'])->name('stock.storeExport');
Route::post('/kho/bulk-import', [AdminStockController::class, 'storeBulkImport'])->name('stock.storeBulkImport');

Route::post('/admin/stock/export/bulk', [AdminStockController::class, 'storeBulkExport'])->name('stock.storeBulkExport');

 
   

  
    // Mô tả sản phẩm
    Route::get('/san-pham/noi-dung', [AdminProductContentController::class, 'description'])->name('products.description');
    Route::get('/san-pham/seo', [AdminProductContentController::class, 'seo'])->name('products.seo');
    Route::get('/san-pham/media', [AdminProductContentController::class, 'media'])->name('products.media');

 Route::resource('vouchers', VoucherController::class);


    // Quản lý yêu cầu hỗ trợ (chỉ admin)

       
    });

    // Quản lý đánh giá (admin + staff)
   Route::prefix('admin')
    ->middleware(['auth', 'role:admin|staff']) // ✅ chỉ admin + staff mới vào
    ->name('admin.')
    ->group(function () {

        // QUẢN LÝ ĐÁNH GIÁ
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{id}/reply', [AdminReviewController::class, 'reply'])->name('reviews.reply');

        // HỖ TRỢ
       
         Route::get('/team-members', [AdminTeamController::class, 'index'])->name('team.index');
    Route::get('/team-members/create', [AdminTeamController::class, 'create'])->name('team.create');
    Route::post('/team-members', [AdminTeamController::class, 'store'])->name('team.store');
    Route::get('/team/{id}/edit', [AdminTeamController::class, 'edit'])->name('team.edit');
    Route::put('/team-members/{id}', [AdminTeamController::class, 'update'])->name('team.update');
    Route::delete('/team-members/{id}', [AdminTeamController::class, 'destroy'])->name('team.destroy');
    Route::get('/team/{id}/edit-role', [AdminTeamController::class, 'editRole'])->name('team.editRole');
    Route::put('/team/{id}/update-role', [AdminTeamController::class, 'updateRole'])->name('team.updateRole');
    Route::patch('/team/{id}/toggle-ban', [AdminTeamController::class, 'toggleBan'])->name('team.toggleBan');
        // YÊU CẦU (REQUESTS)
        Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/{id}/chat', [RequestController::class, 'chat'])->name('requests.chat');
        Route::post('/requests/{id}/reply', [RequestController::class, 'reply'])->name('requests.reply');
    });

Route::prefix('admin')->middleware(['auth', 'role:admin|staff'])->name('admin.')->group(function () {
    Route::get('/support', [AdminSupportController::class, 'index'])->name('support.index');
    Route::get('/support/advanced', [AdminSupportController::class, 'advancedList'])->name('support.advanced');
    Route::get('/support/{id}/chat', [AdminSupportController::class, 'chat'])->name('support.chat');
    Route::post('/support/{id}/reply', [AdminSupportController::class, 'reply'])->name('support.reply');
    Route::post('/support/quick-reply', [AdminSupportController::class, 'quickReply'])->name('support.quick-reply');
    Route::post('/support/{id}/mark-read', [AdminSupportController::class, 'markAsRead'])->name('support.mark-read');
    Route::post('/support/{id}/status', [AdminSupportController::class, 'updateStatus'])->name('support.update-status');
    Route::delete('/support/{id}', [AdminSupportController::class, 'destroy'])->name('support.delete');
    Route::get('/support/stats', [AdminSupportController::class, 'getStats'])->name('support.stats');
    Route::get('/support-analytics', [AdminSupportController::class, 'analytics'])->name('support.analytics');
    Route::post('/support/{id}/priority', [AdminSupportController::class, 'updatePriority'])->name('support.update-priority');
    Route::post('/support/{id}/send-location', [AdminSupportController::class, 'sendQuickLocation'])->name('support.send-location');
    Route::get('/api/shop-locations/nearby', [AdminSupportController::class, 'getNearbyShops'])->name('api.shop-locations.nearby');
    Route::get('/{id}/messages', [AdminSupportController::class, 'getNewMessages'])->name('support.messages');
    Route::get('/unread-count', [AdminSupportController::class, 'getUnreadCount'])->name('support.unread-count');

});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::resource('categories',AdminCategoryController::class);
});

Route::post('/vnpay_payment', [PaymentController::class, 'vnpay_payment'])
    ->name('vnpay.payment');
    Route::post('/momo_payment', [MomoController::class, 'momo_payment'])
    ->name('momo_payment');

// Callback từ VNPay (luôn là GET)
Route::get('/vnpay/return', [PaymentController::class, 'vnpayReturn'])
    ->name('vnpay.return');

    Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
});
