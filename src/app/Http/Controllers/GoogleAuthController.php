<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
            ]
        );

        $user->forceFill([
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        if (! $user->hasRole('super_admin') && ! $user->hasRole('user')) {
            $user->assignRole('user');
        }

        Auth::login($user);

        if ($user->hasRole('super_admin') && ! request()->session()->has('url.intended')) {
            return redirect('/admin');
        }

        return redirect()->intended('/dashboard');
    }
}