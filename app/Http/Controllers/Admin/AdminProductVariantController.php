<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\CartItem;

class AdminProductVariantController extends Controller
{
    public function index($productId)
{
    $product = Product::findOrFail($productId);
    $variants = ProductVariant::where('product_id', $productId)->get();
    return view('admin.product_variants.index', compact('product', 'variants'));
}
public function all()
{
    $variants = ProductVariant::with('product')->get();
    return view('admin.product_variants.all', compact('variants'));
}


public function store(Request $request, $productId)
{
    $request->validate([
        'color' => 'required|string|max:50',
        'size' => 'required|string|max:50',
        'price' => 'required|numeric',
        'stock_quantity' => 'required|integer'
    ]);

    ProductVariant::create([
        'product_id' => $productId,
        'color' => $request->color,
        'size' => $request->size,
        'price' => $request->price,
        'stock_quantity' => $request->stock_quantity
    ]);

    return redirect()->route('admin.san-pham.bien-the', $productId)->with('success', 'Thêm biến thể thành công!');
}

public function update(Request $request, $id)
{
    $request->validate([
        'color' => 'required|string|max:50',
        'size' => 'required|string|max:50',
        'price' => 'required|numeric',
        'stock_quantity' => 'required|integer'
    ]);

    $variant = ProductVariant::findOrFail($id);

    // ✅ Kiểm tra trùng màu + size nhưng bỏ qua biến thể hiện tại
    $exists = ProductVariant::where('product_id', $variant->product_id)
        ->where('color', $request->color)
        ->where('size', $request->size)
        ->where('id', '!=', $id)
        ->exists();

    if ($exists) {
        return back()->withErrors(['size' => 'Biến thể với màu này và size này đã tồn tại.'])->withInput();
    }

    // Cập nhật biến thể
    $variant->update($request->only(['color', 'size', 'price', 'stock_quantity']));

    // ✅ Cập nhật lại cart_items nếu variant này đang trong giỏ
    CartItem::where('variant_id', $variant->id)->update([
        'size' => $variant->size,
        'color' => $variant->color,
        'price' => $variant->price
    ]);

    return redirect()
        ->route('admin.san-pham.bien-the', $variant->product_id)
        ->with('success', 'Cập nhật thành công và đồng bộ giỏ hàng!');
}

public function edit($id)
{
    $variant = ProductVariant::findOrFail($id);
    $product = Product::findOrFail($variant->product_id);
    return view('admin.product_variants.edit', compact('variant', 'product'));
}

public function destroy($id)
{
    $variant = ProductVariant::findOrFail($id);
    $variant->delete();
    return redirect()->route('admin.san-pham.bien-the', $variant->product_id)->with('success', 'Xoá biến thể thành công!');
}
public function bulkDelete(Request $request)
{
    $ids = $request->input('variant_ids', []);

    if (!empty($ids)) {
        ProductVariant::whereIn('id', $ids)->delete();
        return back()->with('success', 'Đã xoá các biến thể được chọn.');
    }

    return back()->with('error', 'Bạn chưa chọn biến thể nào để xoá.');
}

}

