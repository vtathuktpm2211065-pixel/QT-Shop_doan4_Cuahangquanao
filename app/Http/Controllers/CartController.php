<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
   use App\Models\ProductVariant; 
class CartController extends Controller
{
    public function index()
{
    $userId = auth()->check() ? auth()->id() : session()->getId();

    $cartItems = CartItem::with(['product', 'variant'])
        ->where('user_id', $userId)
        ->get();

    $totalQuantity = CartItem::where('user_id', $userId)->sum('quantity');

    return view('cart.index', compact('cartItems', 'totalQuantity'));
}

    public function remove($id)
    {
        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json(['error' => 'Sản phẩm không tồn tại trong giỏ hàng.'], 404);
        }

        $cartItem->delete();

        $totalQuantity = CartItem::where('user_id', auth()->id())->sum('quantity');

        return response()->json([
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            'totalQuantity' => $totalQuantity
        ]);
    }

public function updateQuantity(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    $cartItem = CartItem::find($id);

    if (!$cartItem) {
        return response()->json(['error' => 'Sản phẩm không tồn tại trong giỏ hàng.'], 404);
    }

    // Lấy biến thể từ variant_id
    $variant = ProductVariant::find($cartItem->variant_id);

    if (!$variant) {
        return response()->json(['error' => 'Không tìm thấy biến thể sản phẩm.'], 422);
    }

    // ✅ So sánh số lượng yêu cầu với tồn kho
    if ($request->quantity > $variant->stock_quantity) {
        return response()->json([
            'error' => 'Số lượng vượt quá tồn kho. Chỉ còn ' . $variant->stock_quantity . ' sản phẩm.'
        ], 422);
    }

    $cartItem->quantity = $request->quantity;
    $cartItem->save();

    $totalQuantity = CartItem::where('user_id', auth()->id())->sum('quantity');

    return response()->json([
        'message' => 'Cập nhật số lượng thành công.',
        'totalQuantity' => $totalQuantity
    ]);
}

public function addToCart(Request $request, $slug)
{
    try {
        // ✅ Kiểm tra đăng nhập
       $userId = Auth::check() ? Auth::id() : session()->getId();

        if (!$userId) {
            return response()->json(['error' => 'Bạn cần đăng nhập để mua hàng'], 401);
        }

        // ✅ Lấy thông tin sản phẩm và biến thể
        $product = Product::with('variants')->where('slug', $slug)->firstOrFail();
        $size = $request->input('size');
        $color = $request->input('color');
        $quantity = (int) $request->input('quantity', 1);

        if (empty($size) || empty($color)) {
            return response()->json(['error' => 'Vui lòng chọn kích thước và màu sắc'], 422);
        }

        $variant = $product->variants()
            ->where('size', $size)
            ->where('color', $color)
            ->first();

        if (!$variant) {
            return response()->json(['error' => 'Biến thể sản phẩm không tồn tại'], 404);
        }

        if ($variant->stock_quantity < $quantity) {
            return response()->json(['error' => 'Số lượng tồn kho không đủ'], 422);
        }

        // ✅ Tạo hoặc lấy giỏ hàng active của user
        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
            'status' => 'active',
        ]);

        // ✅ Tạo hoặc lấy item trong giỏ
        $cartItem = CartItem::firstOrCreate(
            [
                'user_id'    => $userId,
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'size'       => $size,
                'color'      => $color,
                 'price' => $variant->price, 
            ],
            [
                'quantity'  => 0,
                'added_at'  => now(),
            ]
        );

        // ✅ Gán variant_id nếu chưa có
        if (!$cartItem->variant_id || $cartItem->variant_id != $variant->id) {
            $cartItem->variant_id = $variant->id;
        }
        // ✅ Kiểm tra số lượng vượt tồn kho
        if ($variant->stock_quantity < ($cartItem->quantity + $quantity)) {
            return response()->json([
                'error' => 'Tổng số lượng vượt quá tồn kho. Chỉ còn ' . $variant->stock_quantity . ' sản phẩm.',
            ], 422);
        }

        // ✅ Cộng thêm số lượng và lưu
        $cartItem->quantity += $quantity;
        
        $cartItem->save();

        // ✅ Trả về tổng số lượng giỏ hàng
        $totalQuantity = CartItem::where('user_id', $userId)->sum('quantity');

        return response()->json([
            'message'       => 'Đã thêm vào giỏ hàng',
            'totalQuantity' => $totalQuantity,
        ]);
    } catch (\Exception $e) {
        Log::error('❌ Lỗi khi thêm vào giỏ hàng: ' . $e->getMessage());
        return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại sau.'], 500);
    }
}

public function count()
{
    $totalQuantity = 0;

    if (Auth::check()) {
        $totalQuantity = CartItem::where('user_id', Auth::id())->sum('quantity');
    }

    return response()->json(['totalQuantity' => $totalQuantity]);
}
public function addBySlug(Request $request, $slug)
{
    try {
        \Log::info('Request data', $request->all());

        // ✅ Bổ sung đoạn này để tránh lỗi biến $userId chưa được khai báo
       $userId = Auth::check() ? Auth::id() : session()->getId();

        if (!$userId) {
            \Log::warning('User not authenticated');
            return response()->json(['error' => 'Bạn cần đăng nhập để mua hàng'], 401);
        }

        // ✅ Tạo hoặc lấy giỏ hàng active
        $cart = \App\Models\Cart::firstOrCreate([
            'user_id' => $userId,
            'status' => 'active',
        ]);

        $product = Product::with('variants')->where('slug', $slug)->firstOrFail();
        $size = $request->input('size');
        $color = $request->input('color');
        $quantity = (int) $request->input('quantity', 1);

        \Log::info('Parsed data', ['size' => $size, 'color' => $color, 'quantity' => $quantity]);

        if (empty($size) || empty($color)) {
            return response()->json(['error' => 'Vui lòng chọn kích thước và màu sắc'], 422);
        }

        $variant = $product->variants()
            ->where('size', $size)
            ->where('color', $color)
            ->first();

        if (!$variant) {
            return response()->json(['error' => 'Biến thể sản phẩm không tồn tại'], 404);
        }

        if ($variant->stock_quantity < $quantity) {
            return response()->json(['error' => 'Số lượng tồn kho không đủ'], 422);
        }

        $cartItem = CartItem::firstOrCreate(
            [
                'user_id'    => $userId,
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'color'      => $color,
                'size'       => $size,
                 'price'     => $variant->price,
            ],
            [
                'quantity'  => 0,
                'added_at'  => now()
            ]
        );

        $cartItem->variant_id = $variant->id;

        if ($variant->stock_quantity < ($cartItem->quantity + $quantity)) {
            return response()->json(['error' => 'Tổng số lượng vượt quá tồn kho. Chỉ còn ' . $variant->stock_quantity], 422);
        }

        $cartItem->quantity += $quantity;
        $cartItem->save();

        $totalQuantity = CartItem::where('user_id', $userId)->sum('quantity');

        return response()->json([
            'message' => 'Đã thêm vào giỏ hàng',
            'totalQuantity' => $totalQuantity,
        ]);
    } catch (\Exception $e) {
        \Log::error('Lỗi khi thêm vào giỏ hàng: ' . $e->getMessage());
        return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại'], 500);
    }
}

public function updateVariant(Request $request, $id)
{
    $item = CartItem::findOrFail($id);

    $variant = ProductVariant::where('product_id', $item->product_id)
        ->where('size', $request->size)
        ->where('color', $request->color)
        ->first();

    if (!$variant) {
        return response()->json(['error' => 'Không tìm thấy biến thể phù hợp.'], 422);
    }

    // Kiểm tra tồn kho
    if ($item->quantity > $variant->stock_quantity) {
        return response()->json([
            'error' => "Chỉ còn {$variant->stock_quantity} sản phẩm size {$request->size}, màu {$request->color}"
        ], 422);
    }

    // Cập nhật tất cả thông tin liên quan
    $item->variant_id = $variant->id;
    $item->size = $variant->size;
    $item->color = $variant->color;
    $item->price = $variant->price;
    $item->save();

    return response()->json(['message' => 'Cập nhật biến thể thành công.']);
}

public function buyNow(Request $request, $slug)
{
   $userId = Auth::check() ? Auth::id() : session()->getId();
$selectedIds = session('checkout_ids', []);
$cartItems = CartItem::whereIn('id', $selectedIds)
    ->where('user_id', $userId)
    ->get();

    $product = Product::where('slug', $slug)->first();
    if (!$product) {
        return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
    }

    $variant = $product->variants()
        ->where('size', $request->size)
        ->where('color', $request->color)
        ->first();

    if (!$variant || $variant->stock_quantity < $request->quantity) {
        return response()->json(['error' => 'Không đủ hàng tồn kho'], 422);
    }

    // Tạo bản ghi CartItem tạm (cho mục đích checkout ngay)
   $cartItem = CartItem::create([
    'user_id'    => $userId,
    'product_id' => $product->id,
    'variant_id' => $variant->id,
    'size'       => $request->size,
    'color'      => $request->color,
    'quantity'   => $request->quantity,
    'price'      => $product->price,
]);
$cartItem->save();
    // Lưu vào session để hiển thị trong trang checkout
    session([
        'checkout_items' => collect([$cartItem->load('product')]),
        'checkout_total' => $product->price * $request->quantity,
        'checkout_ids' => [$cartItem->id],
    ]);

    return response()->json(['redirect' => route('checkout')]);
}



}