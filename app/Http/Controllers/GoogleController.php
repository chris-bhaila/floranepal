<?php

namespace App\Http\Controllers;

use App\Models\Nursery;
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
            // Match on google_id first so we never overwrite an account that merely shares the email
            $user  = User::where('google_id', $googleUser->getId())->first();
            $isNew = false;

            if (!$user) {
                $byEmail = User::where('email', $googleUser->getEmail())->first();

                // Email belongs to a different Google account — reject
                if ($byEmail && $byEmail->google_id !== null) {
                    \Log::warning('Google OAuth email conflict', ['email' => $googleUser->getEmail()]);
                    return $isMobile
                        ? redirect('floranepal://auth?error=email_conflict')
                        : redirect('/?error=email_conflict');
                }

                if ($byEmail) {
                    // Existing account without a google_id — link it, preserve verification state
                    $byEmail->update([
                        'google_id' => $googleUser->getId(),
                        'name'      => $googleUser->getName(),
                        'avatar'    => $googleUser->getAvatar(),
                    ]);
                    $user = $byEmail;
                } else {
                    // Brand new account — do not mark email as verified yet
                    $user = User::create([
                        'email'    => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'name'     => $googleUser->getName(),
                        'avatar'   => $googleUser->getAvatar(),
                        'password' => bcrypt(Str::random(32)),
                    ]);
                    $isNew = true;
                    $user->sendEmailVerificationNotification();
                }
            } else {
                // Returning user — keep name and avatar in sync, never touch email_verified_at
                $user->update([
                    'name'   => $googleUser->getName(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            if ($isNew) {
                Nursery::where('google_id', $googleUser->getId())
                    ->whereNull('user_id')
                    ->update(['user_id' => $user->id]);
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

        // Block unverified users — resend the email every attempt so they always have a fresh link
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice');
        }

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
