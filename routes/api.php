<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    
    // Get authenticated user
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

});

Route::post('/auth/mobile/login', function(Request $request) {
    \Log::info('Mobile login hit', ['token' => substr($request->token, 0, 10)]);
    
    $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->token);
    if ($accessToken) {
        $user = $accessToken->tokenable;
        Auth::login($user, true);
        \Log::info('Mobile login success', ['user_id' => $user->id]);
        return response()->json(['success' => true]);
    }
    return response()->json(['error' => 'Invalid token'], 401);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);