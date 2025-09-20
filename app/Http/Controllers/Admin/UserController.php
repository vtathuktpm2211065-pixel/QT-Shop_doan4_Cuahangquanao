<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rules\Password as PasswordRule;

class UserController extends Controller
{
public function create()
{
    $roles = Role::all();
    $permissions = Permission::all();

    $permissionsByRole = [];
    foreach ($roles as $role) {
        $permissionsByRole[$role->name] = $role->permissions->pluck('name')->toArray();
    }


    return view('admin.users.create_user', compact('roles', 'permissions', 'permissionsByRole'));
}

public function storeUser(Request $request)
{
    $validated = $request->validate([
        'username' => [
            'required',
            'string',
            'max:255',
            'unique:users',
            'regex:/^[a-zA-Z0-9_]+$/'
        ],
        'name' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) {
                if (!preg_match('/^[\p{L}\s]+$/u', $value)) {
                    $fail('Họ tên chỉ được chứa chữ cái và khoảng trắng, không được chứa số hoặc ký tự đặc biệt.');
                }
            },
        ],
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            'unique:users',
            'regex:/@gmail\.com$/i'
        ],
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
        ],
        'role' => ['required', 'exists:roles,name'],
    ], [
        'username.regex' => 'Username chỉ gồm chữ cái, số và dấu gạch dưới (_), không chứa khoảng trắng.',
        'email.regex' => 'Email phải có đuôi @gmail.com.',
        'password.regex' => 'Mật khẩu phải chứa ít nhất một chữ hoa, một chữ thường, một số và một ký tự đặc biệt.',
        'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        'role.required' => 'Vui lòng chọn vai trò cho người dùng.',
        'role.exists' => 'Vai trò không hợp lệ.',
    ]);

    // Tạo tài khoản người dùng
    $user = User::create([
        'username' => $validated['username'],
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    $user->assignRole($validated['role']);

  return redirect()->route('admin.phanquyen')->with('success', 'Tạo tài khoản thành công.');

}

// Hiển thị hồ sơ người dùng
public function profile()
{
    $user = Auth::user();
    return view('hoso.index', compact('user'));
}

// Xử lý cập nhật hồ sơ
public function updateProfile(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        'email' => 'required|email|unique:users,email,' . $user->id,
        'phone' => 'required|regex:/^0[0-9]{9}$/',
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ], [
        'phone.required' => 'Vui lòng nhập số điện thoại.',
        'phone.regex' => 'Số điện thoại phải có 10 chữ số và bắt đầu bằng số 0.',
        'phone.regex' => 'Số điện thoại phải là 10 chữ số và bắt đầu bằng số 0 (không chứa chữ cái).',

    ]);

    if ($request->hasFile('avatar')) {
        if ($user->avatar && file_exists(public_path('storage/' . $user->avatar))) {
            unlink(public_path('storage/' . $user->avatar));
        }
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $validated['avatar'] = $avatarPath;
    }

    $user->update($validated);

    return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
}



public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required'],
        'new_password' => [
            'required',
            'confirmed',
            PasswordRule::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised(),
        ],
    ], [
        'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
        'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
        'new_password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        'new_password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
    ]);

    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
    }

    $user->update([
        'password' => Hash::make($request->new_password),
    ]);

    return back()->with('success', 'Thay đổi mật khẩu thành công!');
}





}
