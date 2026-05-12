<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['state' => 'mobile'])
            ->stateless()
            ->redirect();
    }

    public function callback(Request $request)
    {
        $isMobile = true; // Hardcoded as per team decision

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            \Log::error('Google OAuth failed: ' . $e->getMessage());
            return redirect('floranepal://auth?error=google_failed');
        }

        \Log::info('Google user retrieved', [
            'email'     => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'name'      => $googleUser->getName(),
            'is_mobile' => $isMobile,
        ]);

        if (!$googleUser->getEmail()) {
            \Log::error('No email from Google');
            return redirect('floranepal://auth?error=no_email');
        }

        try {
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'google_id' => $googleUser->getId(),
                    'name'      => $googleUser->getName(),
                    'avatar'    => $googleUser->getAvatar(),
                    'password'  => bcrypt(Str::random(32)),
                    'email_verified_at' => now(),
                ]
            );
            \Log::info('User upserted', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            \Log::error('User upsert failed: ' . $e->getMessage());
            return redirect('floranepal://auth?error=server_error');
        }

        $token = $user->createToken('android-app')->plainTextToken;
        \Log::info('Token created', ['user_id' => $user->id, 'is_mobile' => $isMobile]);

        return redirect('floranepal://auth?token=' . urlencode($token));
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}