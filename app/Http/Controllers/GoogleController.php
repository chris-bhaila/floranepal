<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    //Redirecting user to google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    //Handling google callback
    public function callback()
    {
        // Step 1: Get Google user
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            \Log::error('Google OAuth failed: ' . $e->getMessage());
            return response()->json(['error' => 'Google authentication failed'], 401);
        }

        // Step 2: Upsert user by email (stable identifier)
        try {
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'google_id' => $googleUser->getId(),
                    'name'      => $googleUser->getName(),
                    'avatar'    => $googleUser->getAvatar(),
                    'password'  => bcrypt(Str::random(32)),
                ]
            );
        } catch (\Exception $e) {
            \Log::error('User upsert failed during Google callback: ' . $e->getMessage());
            return response()->json(['error' => 'Could not authenticate user'], 500);
        }

        if (!$user) {
            \Log::error('updateOrCreate returned null for email: ' . $googleUser->getEmail());
            return response()->json(['error' => 'User creation failed'], 500);
        }

        // Step 3: Login and issue Sanctum token
        Auth::login($user);
        $token = $user->createToken('nmc-token')->plainTextToken;

        // Step 4: Detect mobile source
        $isMobile = request()->query('source') === 'mobile';

        if ($isMobile) {
            return redirect('myapp://auth/google/callback?token=' . urlencode($token));
        }

        // Step 5: Web flow — admin check
        if ($user->subscription_type === 'admin') {
            return redirect()->route('admin.dashboard')
                ->cookie('auth_token', $token, 60 * 24);
        }

        // Step 6: Email verification check
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice')
                ->cookie('auth_token', $token, 60 * 24);
        }

        return redirect('/dashboard')
            ->cookie('auth_token', $token, 60 * 24);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // revoke all tokens for this user
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
