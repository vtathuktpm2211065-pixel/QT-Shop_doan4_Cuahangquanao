<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Str;

class AdminCategoryController extends Controller
{
   public function index()
{
    $categories = Category::withCount('products')->paginate(10);
    return view('admin.categories.index', compact('categories'));
}
    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

 public function destroy(Category $category)
{
   
    $defaultCategory = Category::firstOrCreate(
        ['slug' => 'chua-phan-loai'],
        ['name' => 'Chưa phân loại']
    );
    $category->products()->update(['category_id' => $defaultCategory->id]);

    $category->delete();

    return redirect()->route('categories.index')
        ->with('success', 'Danh mục đã bị xóa, sản phẩm được chuyển về "Chưa phân loại".');
}

}
