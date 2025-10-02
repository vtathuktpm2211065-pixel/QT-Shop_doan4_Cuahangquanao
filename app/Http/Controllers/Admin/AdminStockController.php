<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Revenue;
use App\Models\ProductVariant;
use App\Models\Product; // hoặc Model liên quan kho
use App\Models\Stock;   // nếu có model quản lý tồn kho
class AdminStockController extends Controller
{
    // Trang chính quản lý kho: xem tồn kho, cảnh báo tồn kho thấp,...
 public function index(Request $request)
{
    $query = ProductVariant::with('product');

    $productName = $color = $size = null;

    if ($request->filled('keyword')) {
        $keyword = trim(strtolower($request->keyword));
        $parts = explode('/', $keyword);

        if (count($parts) === 2) {
            $productPart = trim($parts[0]);
            $variantPart = trim($parts[1]);

            if (str_contains($variantPart, '.')) {
                [$color, $size] = array_map('trim', explode('.', $variantPart));
            } elseif (strlen($variantPart) <= 3) {
                $size = trim($variantPart);
            } else {
                $color = trim($variantPart);
            }

            $productName = trim($productPart);
        } else {
            if (str_contains($keyword, '.')) {
                [$color, $size] = array_map('trim', explode('.', $keyword));
            } elseif (strlen($keyword) <= 3) {
                $size = trim($keyword);
            } else {
                $productName = trim($keyword); // Ưu tiên tìm theo tên sản phẩm
            }
        }

        $query->where(function ($q) use ($productName, $color, $size) {
            if ($productName) {
                $q->whereHas('product', function ($q2) use ($productName) {
                    $q2->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($productName) . '%']);
                });
            }

            if ($color) {
                $q->whereRaw('LOWER(TRIM(color)) = ?', [trim(strtolower($color))]);
            }

            if ($size) {
                $q->whereRaw('LOWER(TRIM(size)) = ?', [trim(strtolower($size))]);
            }
        });
    }

    // Các filter bổ sung nếu có
    if ($request->filled('product_id')) {
        $query->where('product_id', $request->product_id);
    }

    if ($request->filled('color')) {
        $query->whereRaw('LOWER(TRIM(color)) = ?', [trim(strtolower($request->color))]);
    }

    if ($request->filled('size')) {
        $query->whereRaw('LOWER(TRIM(size)) = ?', [trim(strtolower($request->size))]);
    }

    // 🟡 Đưa lọc theo danh mục lên TRƯỚC khi paginate
    if ($request->filled('category_id')) {
        $query->whereHas('product', function ($q) use ($request) {
            $q->where('category_id', $request->category_id);
        });
    }

    $variants = $query->orderByDesc('updated_at')->paginate(20);

    // Dữ liệu cho filter dropdown
    $allProducts = Product::orderBy('name')->get();
    $allColors = ProductVariant::selectRaw('DISTINCT TRIM(color) as color')->pluck('color');
    $allSizes  = ProductVariant::selectRaw('DISTINCT TRIM(size) as size')->pluck('size');

   $categories = Category::orderBy('name')->get();

return view('admin.stock.index', compact(
    'variants', 'allProducts', 'allColors', 'allSizes', 'categories'
));

}

public function storeExport(Request $request)
{
    $exports = $request->input('exports', []);

    DB::beginTransaction();
    try {
        foreach ($exports as $variantId => $data) {
            $quantity = (int) $data['quantity'];
            $note = $data['note'] ?? null;

            if ($quantity <= 0) continue;

            $variant = ProductVariant::find($variantId);
            if (!$variant) continue;

            if ($variant->stock_quantity < $quantity) {
                return back()->with('error', "Biến thể {$variant->product->name} ({$variant->color}/{$variant->size}) không đủ tồn kho.");
            }

            // Trừ kho
            $variant->stock_quantity -= $quantity;
            $variant->save();

            // ✅ Lưu doanh thu
            Revenue::create([
                'variant_id' => $variant->id,
                'quantity'   => $quantity,
                'price'      => $variant->price, // hoặc $variant->product->price
                'total'      => $variant->price * $quantity,
                'type'       => 'export',
                'admin_id'   => auth()->id(),
                'date'       => now()->toDateString()
            ]);

            // Lưu lịch sử xuất kho
            Stock::create([
                'variant_id' => $variantId,
                'type'       => 'export',
                'quantity'   => $quantity,
                'note'       => $note,
                'admin_id'   => auth()->id(),
            ]);
        }

        DB::commit();
        return redirect()->route('admin.stock.index')->with('success', 'Xuất kho thành công.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Xuất kho lỗi: ' . $e->getMessage());
        return back()->with('error', 'Đã xảy ra lỗi khi xuất kho.');
    }
}

public function storeBulkExport(Request $request)
{
    $data = $request->validate([
        'exports' => 'required|array',
        'exports.*.variant_id' => 'required|exists:product_variants,id',
        'exports.*.quantity' => 'nullable|integer|min:0',
        'note' => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();
    try {
        foreach ($data['exports'] as $export) {
            // Bỏ qua sản phẩm không chọn
            if (empty($export['variant_id']) || ($export['quantity'] ?? 0) <= 0) {
                continue;
            }

            $variant = ProductVariant::findOrFail($export['variant_id']);

            // Tính tồn kho hiện tại
            $imported = $variant->stocks()->where('type', 'import')->sum('quantity');
            $exported = $variant->stocks()->where('type', 'export')->sum('quantity');
            $currentStock = $imported - $exported;

            if ($export['quantity'] > $currentStock) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', "Xuất vượt quá tồn kho cho {$variant->product->name} ({$variant->color}/{$variant->size})");
            }

            // Lưu phiếu xuất
            Stock::create([
                'variant_id' => $variant->id,
                'quantity'   => $export['quantity'],
                'type'       => 'export',
                'note'       => $data['note'] ?? null,
                'admin_id'   => auth()->id(),
            ]);

            // Lưu doanh thu
            Revenue::create([
                'variant_id' => $variant->id,
                'quantity'   => $export['quantity'],
                'price'      => $variant->price,
                'total'      => $variant->price * $export['quantity'],
                'type'       => 'export',
                'admin_id'   => auth()->id(),
                'date'       => now()->toDateString()
            ]);

            // Cập nhật tồn kho
            $variant->stock_quantity = $currentStock - $export['quantity'];
            $variant->save();
        }

        DB::commit();
        return redirect()->route('admin.stock.index')->with('success', 'Xuất kho nhiều biến thể thành công.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Đã xảy ra lỗi khi xuất kho: ' . $e->getMessage());
    }
}

public function storeImport(Request $request)
{
    $data = $request->validate([
        'variant_id' => 'required|exists:product_variants,id',
        'quantity'   => 'required|integer|min:1',
        'note'       => 'nullable|string|max:255',
    ]);

    // Ghi nhận nhập kho
    Stock::create([
        'variant_id' => $data['variant_id'],
        'quantity'   => $data['quantity'],
        'type'       => 'import',
        'note'       => $data['note'] ?? null,
    ]);

    // Cập nhật tồn kho
    $variant = ProductVariant::find($data['variant_id']);
    $imported = $variant->stocks()->where('type', 'import')->sum('quantity');
    $exported = $variant->stocks()->where('type', 'export')->sum('quantity');
    $variant->stock_quantity = $imported - $exported;
    $variant->save();

    return redirect()->route('admin.stock.index')->with('success', 'Nhập kho thành công.');
}

public function storeBulkImport(Request $request)
{
    $imports = $request->input('imports');

    if (!is_array($imports) || !array_key_exists(0, $imports)) {
    $imports = [$imports];
}
$request->merge(['imports' => $imports]);

    $request->merge(['imports' => $imports]);

    $data = $request->validate([
        'imports' => 'required|array',
        'imports.*.variant_id' => 'required|exists:product_variants,id',
        'imports.*.quantity' => 'required|integer|min:1',
        'note' => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();

    try {
        foreach ($data['imports'] as $import) {
            $variant = ProductVariant::findOrFail($import['variant_id']);

            Stock::create([
                'variant_id' => $variant->id,
                'type' => 'import',
                'quantity' => $import['quantity'],
                'note' => $data['note'] ?? null,
                'admin_id' => auth()->id(),
            ]);

            $variant->stock_quantity += $import['quantity'];
            $variant->save();
           $quantity = $import['quantity'];
$price = $variant->price;
$total = $price * $quantity;

Revenue::create([
    'variant_id' => $variant->id,
    'quantity' => $quantity,
    'price' => $price,
    'total' => $total,
    'type' => 'import', // lưu lại là nhập kho
    'admin_id' => auth()->id(),
    'date' => now(),
]);



        }

        DB::commit();
        return redirect()->route('admin.stock.index')->with('success', 'Nhập kho thành công.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
    }
}

 public function import(Request $request)
{
    $query = ProductVariant::with('product');

    $productName = $color = $size = null;

    if ($request->filled('keyword')) {
        $keyword = trim(strtolower($request->keyword));
        $parts = explode('/', $keyword);

        if (count($parts) === 2) {
            $productName = trim($parts[0]);
            $variantPart = trim($parts[1]);

            if (str_contains($variantPart, '.')) {
                [$color, $size] = array_map('trim', explode('.', $variantPart));
            } elseif (strlen($variantPart) <= 3) {
                $size = $variantPart;
            } else {
                $color = $variantPart;
            }
        } elseif (str_contains($keyword, '.')) {
            // chỉ có màu.size
            [$color, $size] = array_map('trim', explode('.', $keyword));
        } elseif (strlen($keyword) <= 3) {
            $size = trim($keyword);
        } else {
            $productName = trim($keyword); // đây là điểm sửa chính: từ khóa dài được gán là tên sản phẩm
        }

        $query->where(function ($q) use ($productName, $color, $size) {
            if ($productName) {
                $q->whereHas('product', function ($q2) use ($productName) {
                    $q2->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($productName) . '%']);
                });
            }

            if ($color) {
                $q->whereRaw('LOWER(TRIM(color)) = ?', [strtolower(trim($color))]);
            }

            if ($size) {
                $q->whereRaw('LOWER(TRIM(size)) = ?', [strtolower(trim($size))]);
            }
        });
    }

    // Filter riêng qua dropdown
    if ($request->filled('product_id')) {
        $query->where('product_id', $request->product_id);
    }
    if ($request->filled('color')) {
        $query->whereRaw('LOWER(TRIM(color)) = ?', [strtolower(trim($request->color))]);
    }
    if ($request->filled('size')) {
        $query->whereRaw('LOWER(TRIM(size)) = ?', [strtolower(trim($request->size))]);
    }
    if ($request->filled('category_id')) {
        $query->whereHas('product', function ($q) use ($request) {
            $q->where('category_id', $request->category_id);
        });
    }

    $variants = $query->get();

    $allProducts = Product::orderBy('name')->get();
    $allColors = ProductVariant::selectRaw('DISTINCT TRIM(color) as color')->pluck('color');
    $allSizes  = ProductVariant::selectRaw('DISTINCT TRIM(size) as size')->pluck('size');
        
$categories = Category::orderBy('name')->get();

return view('admin.stock.import', compact(
    'variants', 'allProducts', 'allColors', 'allSizes', 'categories'
));

   
}


 

public function sync()
{
    $variants = ProductVariant::with('stocks')->get();

    foreach ($variants as $variant) {
        $imported = $variant->stocks()->where('type', 'import')->sum('quantity');
        $exported = $variant->stocks()->where('type', 'export')->sum('quantity');
        $variant->stock_quantity = $imported - $exported;
        $variant->save();
    }

    return redirect()->route('admin.stock.index')->with('success', 'Đồng bộ tồn kho thành công.');
}
public function export(Request $request)
{
    $query = ProductVariant::with('product');

    $productName = $color = $size = null;

    if ($request->filled('keyword')) {
        $keyword = trim(strtolower($request->keyword));
        $parts = explode('/', $keyword);

        if (count($parts) === 2) {
            $productPart = trim($parts[0]);
            $variantPart = trim($parts[1]);

            if (str_contains($variantPart, '.')) {
                [$color, $size] = array_map('trim', explode('.', $variantPart));
            } elseif (strlen($variantPart) <= 3) {
                $size = trim($variantPart);
            } else {
                $color = trim($variantPart);
            }

            $productName = trim($productPart);
        } elseif (str_contains($keyword, '.')) {
            [$color, $size] = array_map('trim', explode('.', $keyword));
        } elseif (strlen($keyword) <= 3) {
            $size = trim($keyword);
        } else {
            $productName = trim($keyword);
        }

        $query->where(function ($q) use ($productName, $color, $size) {
            if ($productName) {
                $q->whereHas('product', function ($q2) use ($productName) {
                    $q2->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($productName) . '%']);
                });
            }
            if ($color) {
                $q->whereRaw('LOWER(TRIM(color)) = ?', [trim(strtolower($color))]);
            }
            if ($size) {
                $q->whereRaw('LOWER(TRIM(size)) = ?', [trim(strtolower($size))]);
            }
        });
    }

    // Lọc riêng
    if ($request->filled('product_id')) {
        $query->where('product_id', $request->product_id);
    }

    if ($request->filled('color')) {
        $query->whereRaw('LOWER(TRIM(color)) = ?', [trim(strtolower($request->color))]);
    }

    if ($request->filled('size')) {
        $query->whereRaw('LOWER(TRIM(size)) = ?', [trim(strtolower($request->size))]);
    }

    if ($request->filled('category_id')) {
        $query->whereHas('product', function ($q) use ($request) {
            $q->where('category_id', $request->category_id);
        });
    }

    // Lấy dữ liệu
    $variants = $query->orderByDesc('updated_at')->get();

    // Dữ liệu cho dropdown nếu cần
    $allProducts = Product::orderBy('name')->get();
    $allColors   = ProductVariant::selectRaw('DISTINCT TRIM(color) as color')->pluck('color');
    $allSizes    = ProductVariant::selectRaw('DISTINCT TRIM(size) as size')->pluck('size');

   $categories = Category::orderBy('name')->get();

return view('admin.stock.export', compact(
    'variants', 'allProducts', 'allColors', 'allSizes', 'categories'
));
    
}
   



}
