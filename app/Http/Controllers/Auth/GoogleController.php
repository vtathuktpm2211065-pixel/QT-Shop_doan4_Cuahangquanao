<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            Log::info('Starting Google redirect');
            return Socialite::driver('google')->redirect();
        } catch (Exception $e) {
            Log::error('Google redirect error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Không thể kết nối đến Google');
        }
    }

    public function handleGoogleCallback()
    {
        try {
            Log::info('=== GOOGLE CALLBACK STARTED ===');
            
            // 🔥 THÊM TRY-CATCH RIÊNG CHO SOCIALITE
            try {
                $googleUser = Socialite::driver('google')->user();
            } catch (Exception $e) {
                Log::error('Socialite user fetch failed: ' . $e->getMessage());
                return redirect('/login')->with('error', 'Không thể lấy thông tin từ Google');
            }
            
            Log::info('Google user data received', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'id' => $googleUser->getId()
            ]);

            if (!$googleUser->getEmail()) {
                return redirect('/login')->with('error', 'Không thể lấy email từ Google');
            }

            // Tìm hoặc tạo user
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?? 'Google User',
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                ]);
                Log::info('New user created', ['id' => $user->id]);
            } else {
                Log::info('Existing user found', ['id' => $user->id]);
            }

            // Đăng nhập
            Auth::login($user, true);
            session()->save(); // 🔥 Force save session

            Log::info('Login successful', [
                'user_id' => $user->id,
                'authenticated' => Auth::check()
            ]);

            return redirect('/home')->with('success', 'Đăng nhập thành công!');

        } catch (Exception $e) {
            Log::error('Google callback overall error: ' . $e->getMessage());
            Log::error('Full trace: ' . $e->getTraceAsString());
            return redirect('/login')->with('error', 'Đăng nhập thất bại: ' . $e->getMessage());
        }
    }
}