<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\View;
class PermissionController extends Controller
{

public function updateUserRole(Request $request, $id)
{
    $user = User::findOrFail($id);

    if ($user->hasRole('admin')) {
        return back()->withErrors(['error' => 'Không thể thay đổi vai trò hoặc quyền của tài khoản admin.']);
    }

    $user->syncRoles([$request->role]);
    $user->syncPermissions($request->permissions ?? []);

    return redirect()->route('admin.phanquyen')->with('success', 'Cập nhật quyền thành công!');
}



public function editUserRole($id)
{
    $user = User::findOrFail($id);
    $roles = Role::all();
    $permissions = Permission::all();

    // Định nghĩa quyền theo vai trò
    $permissionsByRole = [
        'admin' => $permissions->pluck('name')->toArray(),
        'editor' => ['edit products', 'view orders'],
        'user'
    ];

    return view('admin.users.edit_role', compact('user', 'roles', 'permissions', 'permissionsByRole'));
}


public function storeUser(Request $request)
{
    $validated = $request->validate([
        'username' => 'required|string|max:255|unique:users',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|string|exists:roles,name',
    ]);

    $user = User::create([
        'username' => $validated['username'],
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    $user->assignRole($validated['role']);
    $user->syncPermissions($request->permissions ?? []);

    return redirect()->route('admin.phanquyen')->with('success', 'Tạo tài khoản thành công!');
}


   public function index(Request $request)
{
    $query = User::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    }

    return view('admin.partials.phanquyen', [
        'users' => $query->paginate(10)->withQueryString(),
        'roles' => Role::all(),
        'permissions' => Permission::all(),
        'bannedUsers' => User::where('banned', true)->get(),
    ]);
}


   public function assignRole(Request $request, User $user)
{
    if ($user->hasRole('admin')) {
        return back()->withErrors(['error' => 'Không thể thay đổi vai trò của tài khoản admin.']);
    }

    $user->syncRoles([$request->role]); 
    return back()->with('success', 'Đã gán vai trò thành công!');
}

public function assignPermission(Request $request, User $user)
{
    if ($user->hasRole('admin')) {
        return back()->withErrors(['error' => 'Không thể thay đổi quyền của tài khoản admin.']);
    }

    $user->syncPermissions($request->permissions);
    return back()->with('success', 'Đã gán quyền thành công!');
}

    // Tạo tài khoản
public function create()
{
    $roles = Role::all();
    $permissions = Permission::all();

    $permissionsByRole = [
        'admin' => $permissions->pluck('name')->toArray(),
        'editor' => ['edit products', 'view orders'],
        'user' => [], // người dùng thường không có quyền
    ];

    return view('admin.users.create_user', compact('roles', 'permissions', 'permissionsByRole'));
}

// Chặn tài khoản
public function banUser($id)
{
    $user = User::findOrFail($id);

    if ($user->hasRole('admin')) {
        return back()->withErrors(['error' => 'Không thể chặn tài khoản admin.']);
    }

    $user->update(['banned' => true]);
    return back()->with('success', 'Đã chặn tài khoản thành công.');
}

public function unbanUser($id)
{
    $user = User::findOrFail($id);
    $user->update(['banned' => false]);

    return back()->with('success', 'Đã mở chặn tài khoản.');
}
public function danhSachBiChan()
{
    $bannedUsers = \App\Models\User::where('banned', true)->get();
    return view('admin.users.banned_user', compact('bannedUsers'));
}


// Xóa tài khoản
public function deleteUser($id)
{
    $user = User::findOrFail($id);

    if ($user->hasRole('admin')) {
        return back()->withErrors(['error' => 'Không thể xóa tài khoản admin.']);
    }

    $user->delete();
    return back()->with('success', 'Đã xóa tài khoản.');
}

public function phanQuyen()
{
    $users = User::paginate(10); 
    $roles = Role::all();
    $permissions = Permission::all();
    $bannedUsers = User::where('banned', true)->get();

    return view('admin.partials.phanquyen', compact('users', 'roles', 'permissions', 'bannedUsers'));
}




}

