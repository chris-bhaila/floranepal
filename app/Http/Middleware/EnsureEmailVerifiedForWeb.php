<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;

class EnsureEmailVerifiedForWeb
{
    public function handle(Request $request, Closure $next)
    {
        // Bearer token = mobile app — skip enforcement, mobile handles its own flow
        if ($request->bearerToken()) {
            return $next($request);
        }

        $user = $request->user();

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
