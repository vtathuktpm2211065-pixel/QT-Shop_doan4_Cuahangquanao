<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();

        // Tìm hoặc tạo user trong database
        $user = User::firstOrCreate(
            ['facebook_id' => $facebookUser->id],
            [
                'name' => $facebookUser->name,
                'email' => $facebookUser->email ?? $facebookUser->id . '@facebook.com',
                'password' => bcrypt('facebooklogin') // password mặc định, không dùng
            ]
        );

        Auth::login($user);

        return redirect('/home'); // hoặc trang bạn muốn
    }
}
