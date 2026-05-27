<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        \Log::info('OAuth redirect', [
            'client_param' => $request->query('client'),
            'full_url' => $request->fullUrl(),
        ]);

        $isMobile = $request->query('client') === 'mobile';

        $state = base64_encode(json_encode([
            'client' => $isMobile ? 'mobile' : 'web',
        ]));

        return Socialite::driver('google')
            ->with(['state' => $state])
            ->stateless()
            ->redirect();
    }

    public function callback(Request $request)
    {
        $rawState = $request->query('state');
        $decoded = $rawState ? json_decode(base64_decode($rawState), true) : null;

        \Log::info('OAuth callback', [
            'decoded_state' => $decoded,
            'client_resolved' => ($decoded['client'] ?? 'web'),
        ]);

        if ($rawState) {
            $decoded = json_decode(base64_decode($rawState), true);
            $isMobile = ($decoded['client'] ?? 'web') === 'mobile';
        }

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            \Log::error('Google OAuth failed: ' . $e->getMessage());
            return $isMobile
                ? redirect('floranepal://auth?error=google_failed')
                : redirect('/login?error=google_failed');
        }

        if (!$googleUser->getEmail()) {
            \Log::error('No email from Google');
            return $isMobile
                ? redirect('floranepal://auth?error=no_email')
                : redirect('/login?error=no_email');
        }

        try {
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'google_id'          => $googleUser->getId(),
                    'name'               => $googleUser->getName(),
                    'avatar'             => $googleUser->getAvatar(),
                    'email_verified_at'  => now(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $user->password = bcrypt(Str::random(32));
                $user->save();
            }
        } catch (\Exception $e) {
            \Log::error('User upsert failed: ' . $e->getMessage());
            return $isMobile
                ? redirect('floranepal://auth?error=server_error')
                : redirect('/login?error=server_error');
        }

        // Branch: mobile gets a token, web gets a session
        if ($isMobile) {
            $token = $user->createToken('mobile-app')->plainTextToken;
            \Log::info('Mobile login', ['user_id' => $user->id]);
            return redirect('floranepal://auth?token=' . urlencode($token));
        }

        // Web session login — no token involved
        Auth::login($user);
        $request->session()->regenerate();
        \Log::info('Web login', ['user_id' => $user->id, 'role' => $user->subscription_type]);

        return $user->subscription_type === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect('/dashboard');
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
