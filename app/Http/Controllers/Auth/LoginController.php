<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = \App\Models\User::where('email', $request->login)
                 ->orWhere('username', $request->login)->first();

        if ($user && $user->banned) {
            return redirect()->back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'Tài khoản của bạn đã bị chặn. Vui lòng liên hệ quản trị viên.']);
        }

        return redirect()->back()
            ->withInput($request->only('login'))
            ->withErrors(['login' => trans('auth.failed')]);
    }

    public function username()
    {
        return 'login';
    }

    protected function attemptLogin(Request $request)
    {
        $login = $request->input('login');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return $this->guard()->attempt(
            [
                $field => $login,
                'password' => $request->input('password'),
                'banned' => false, // ✅ CHẶN tài khoản bị khóa
            ],
            $request->filled('remember')
        );
    }
}
