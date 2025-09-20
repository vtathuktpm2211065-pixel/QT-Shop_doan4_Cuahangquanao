<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Category;
class AdminProductController extends Controller
{
    public function getVariants($id)
    {
         $query = Product::with('variants');
        $variants = ProductVariant::where('product_id', $id)
            ->select('id', 'color', 'size', 'price')
            ->get();

        return response()->json($variants);
    }
   public function index(Request $request)
{
    $query = Product::query();

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    if ($request->filled('price_min')) {
        $query->where('price', '>=', $request->price_min);
    }

    if ($request->filled('price_max')) {
        $query->where('price', '<=', $request->price_max);
    }

    $products = $query->with('variants')->latest()->get();

    $categories = Category::all();

    return view('admin.products.index', compact('products', 'categories'));
}


public function edit($id)
{
    $product = Product::findOrFail($id);
    $categories = Category::all(); // truyền danh sách danh mục nếu có
    return view('admin.products.edit', compact('product', 'categories'));
}

public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $data = $request->only(['name', 'slug', 'description', 'price', 'gioi_tinh', 'category_id']);
    $data['noi_bat'] = $request->has('noi_bat');
    $data['pho_bien'] = $request->has('pho_bien');

    // Cập nhật ảnh nếu có ảnh mới
    if ($request->hasFile('image')) {
        $filename = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $filename);
        $data['image_url'] = $filename;
    }

    $product->update($data);

    return redirect()->route('admin.san-pham.index')->with('success', 'Cập nhật sản phẩm thành công!');
}

    
    public function create()
{
    $categories = Category::all();
    return view('admin.products.create', compact('categories'));
}
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|mimes:jpg,jpeg,png',
    ]);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('products', 'public');
        $validated['image_url'] = $path;
    }

    $validated['description'] = $request->input('description');
    $validated['hien_thi'] = $request->has('hien_thi');
    $validated['tieu_bieu'] = $request->has('tieu_bieu');

    Product::create($validated);

    return redirect()->route('admin.san-pham.index')->with('success', 'Thêm sản phẩm thành công!');
}

}
