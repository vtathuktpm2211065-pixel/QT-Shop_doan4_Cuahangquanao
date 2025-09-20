<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function toggleBanUser($id)
    {
        $user = User::findOrFail($id);
        $user->banned = !$user->banned;
        $user->save();

        return redirect()->back()->with('status', $user->banned ? 'Đã chặn người dùng.' : 'Đã mở chặn người dùng.');
    }
public function editUserInfo($id)
{
    $user = User::findOrFail($id);
    return view('admin.users.edit_user_info', compact('user'));
}

public function updateUserInfo(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'password' => 'nullable|min:6',
    ]);

    $user->name = $validated['name'];
    $user->email = $validated['email'];

    if (!empty($validated['password'])) {
        $user->password = bcrypt($validated['password']);
    }
    $user->save();

    return redirect()->route('admin.phanquyen')->with('status', 'Cập nhật thông tin thành công.');
}

    // Thêm các hàm khác nếu cần
}