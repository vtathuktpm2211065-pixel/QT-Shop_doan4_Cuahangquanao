<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->paginate(10);
        return view('admin.vouchers.index', compact('vouchers'));
    }
    public function edit($id)
{
    $voucher = Voucher::findOrFail($id);
    return view('admin.vouchers.edit', compact('voucher'));
}

    public function create()
    {
        return view('admin.vouchers.create');
    }
public function update(Request $request, $id)
{
    $voucher = Voucher::findOrFail($id);

    $validated = $request->validate([
        'code' => 'required|string|unique:vouchers,code,' . $voucher->id,
        'description' => 'nullable|string',
        'discount_type' => 'required|in:percent,fixed',
        'discount_value' => 'required|numeric|min:1',
        'min_order_amount' => 'nullable|numeric|min:0',
        'order_amount' => 'nullable|numeric|min:0',
        'usage_limit' => 'nullable|numeric|min:0',
        'only_new_users' => 'nullable|boolean',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|boolean',
    ]);

    $voucher->update([
        'code' => strtoupper($request->code),
        'description' => $request->description,
        'discount_type' => $request->discount_type,
        'discount_value' => $request->discount_value,
        'min_order_amount' => $request->min_order_amount ?? 0,
        'order_amount' => $request->order_amount ?? 0,
        'usage_limit' => $request->usage_limit ?? 0,
        'only_new_users' => $request->only_new_users ?? false,
        'start_date' => $request->start_date,
        'expires_at' => $request->expires_at,
        'status' => $request->status,
    ]);

    return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật thành công');
}

    public function store(Request $request)
    {
        
        $validated = $request->validate([
        'code' => 'required|string|unique:vouchers,code',
        'description' => 'nullable|string',
        'discount_type' => 'required|in:percent,fixed',
        'discount_value' => 'required|numeric|min:1',
        'min_order_amount' => 'nullable|numeric|min:0',
        'order_amount' => 'nullable|numeric|min:0',
        'usage_limit' => 'nullable|numeric|min:0',
        'only_new_users' => 'nullable|boolean',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|boolean',
    ]);

      $voucher = Voucher::create([
    'code' => strtoupper($request->code),
    'description' => $request->description,
    'discount_type' => $request->discount_type,
    'discount_value' => $request->discount_value,
    'min_order_amount' => $request->min_order_amount ?? 0,
    'order_amount' => $request->order_amount ?? 0,

    'usage_limit' => $request->usage_limit ?? 0,
    'only_new_users' => $request->only_new_users ?? false,
    'start_date' => $request->start_date,
    'expires_at' => $request->expires_at, 
    'used_count' => 0,
]);



       return redirect()->route('admin.vouchers.index')
        ->with('success', 'Tạo voucher thành công! Bạn có thể tạo tiếp.');
}

   public function destroy($id)
{
    $voucher = Voucher::findOrFail($id);
    $voucher->delete();

    return redirect()->route('admin.vouchers.index')->with('success', 'Xóa mã giảm giá thành công.');
}

}
