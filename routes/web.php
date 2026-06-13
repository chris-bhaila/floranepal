<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\NurseryController;
use App\Http\Controllers\PlantController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EsewaController;
use App\Http\Controllers\AdminController;
use App\Models\Nursery;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

function dashboardView(string $page, array $data = [])
{
    $data['nursery'] = $data['nursery'] ?? Auth::user()->nursery;
    $data['user']    = $data['user']    ?? Auth::user();

    if (request()->header('X-Dashboard-Navigate')) {
        return view('pages.dashboard.' . $page, $data);
    }

    return view('pages.dashboard.sidebar', array_merge($data, ['page' => $page]));
}

// Public Routes
Route::get('/', function () {
    $nurseries = Nursery::with('plants')->whereNotNull('user_id')->get();
    return view('login', compact('nurseries'));
})->name('login');

Route::get('/nurseries/{nursery}', function (Nursery $nursery) {
    return view('nursery-plants', ['nursery' => $nursery->load('plants')]);
})->name('nursery.public');

Route::get('/plants/{plant}', function (App\Models\Plant $plant) {
    return view('plant-detail', ['plant' => $plant->load('nursery')]);
})->name('plant.public');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::get('/mobile-login', function () {
    $token = request()->query('token');
    if (!$token) {
        return redirect('/');
    }
    return view('mobile.token');
});

// Email Verification — web guard only (Android never hits this page)
Route::get('/email/verify', function () {
    return view('pages.dashboard.settings.verification');
})->middleware('auth:web')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        $user->update(['verification_status' => 'verified']);
    }

    return response()->view('pages.verified');
})->middleware('signed')->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth:web', 'throttle:6,1'])->name('verification.send');

Route::get('/auth/check-verification', function (Request $request) {
    return response()->json([
        'verified' => $request->user() && $request->user()->hasVerifiedEmail()
    ]);
})->middleware('auth:sanctum,web');

Route::post('/auth/mobile/login', function (Request $request) {
    $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->token);

    if ($accessToken) {
        $user = $accessToken->tokenable;
        Auth::login($user);
        $request->session()->regenerate();
        return response()->json(['success' => true]);
    }

    return response()->json(['error' => 'Invalid token'], 401);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Protected Routes for user
Route::middleware(['auth:sanctum', 'prevent.back'])->group(function () {

    Route::get('/dashboard/additionalInfo', [ProfileController::class, 'addInfo'])->name('addInfo');
    Route::post('/dashboard/additionalInfo', [ProfileController::class, 'storeAdditionalInfo'])->name('addInfo.store');

    Route::get('/dashboard/nurseries/show', function () {
        $nursery = Auth::user()->nursery;
        if (!$nursery) {
            return redirect()->route('nurseries.create')
                ->with('error', 'You do not have a nursery yet.');
        }
        return dashboardView('nurseries.nursery', ['nursery' => $nursery->load('plants')]);
    })->name('nursery.show');

    Route::get('/dashboard/nurseries/create', [NurseryController::class, 'create'])->name('nurseries.create');
    Route::post('/dashboard/nurseries', [NurseryController::class, 'store'])->name('dashboard.nurseries.store');

    Route::get('/dashboard/nurseries/plants/create', [PlantController::class, 'create'])->name('plants.create');
    Route::post('/dashboard/nurseries/plants', [PlantController::class, 'store'])->name('plants.store');
    Route::get('/nursery/plants/{plant}', [PlantController::class, 'show'])->name('plants.show');
    Route::put('/nursery/plants/{plant}', [PlantController::class, 'update'])->name('plants.update');
    Route::delete('/nursery/plants/{plant}', [PlantController::class, 'destroy'])->name('plants.destroy');

    Route::get('/file/{userId}/{filename}', function ($userId, $filename) {
        if (Auth::id() != $userId) {
            abort(403);
        }
        $path = $userId . '/' . $filename;
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }
        return response()->file(Storage::disk('local')->path($path));
    })->name('file.view');

    Route::get('/dashboard/settings', function () {
        return dashboardView('settings');
    })->name('settings');

    Route::get('/dashboard/settings/editProfile', [ProfileController::class, 'edit'])->name('editProfile');
    Route::post('/dashboard/settings/editProfile', [ProfileController::class, 'update'])->name('editProfile.update');

    Route::get('/dashboard/settings/security', function () {
        return dashboardView('settings.security');
    })->name('security');

    Route::get('/dashboard/settings/security/loginhistory', function () {
        return dashboardView('settings.security.loginHistory');
    })->name('settings.loginHistory');

    Route::get('/dashboard/settings/purchasehistory', function () {
        return dashboardView('settings.purchaseHistory');
    })->name('purchaseHistory');

    Route::get('/dashboard/payment/subscription', function () {
        return dashboardView('payment.subscription');
    })->name('subscription');

    Route::post('/esewa/initiate', [EsewaController::class, 'initiate'])->name('esewa.initiate');
    Route::get('/esewa/verify', [EsewaController::class, 'verify'])->name('esewa.verify');

    Route::get('/dashboard/payment/checkout', function () {
        $plan = request('plan', 'biennial');
        return dashboardView('payment.checkout', ['plan' => $plan]);
    })->name('checkout');

    Route::get('/dashboard/{page?}', function ($page = 'dashboard') {
        return dashboardView($page);
    })->name('dashboard');

    Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');
});

// Protected routes for admin
Route::middleware(['auth:sanctum', 'admin', 'prevent.back'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    Route::get('/nurseries', [AdminController::class, 'nurseries'])->name('nurseries');
    Route::get('/nurseries/{nursery}', [AdminController::class, 'showNursery'])->name('nurseries.show');
    Route::put('/nurseries/{nursery}', [AdminController::class, 'updateNursery'])->name('nurseries.update');
    Route::delete('/nurseries/{nursery}', [AdminController::class, 'destroyNursery'])->name('nurseries.destroy');

    Route::get('/file/{userId}/{filename}', function ($userId, $filename) {
        $path = $userId . '/' . $filename;
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }
        return response()->file(Storage::disk('local')->path($path));
    })->name('file.view');

    Route::get('/nurseries/{nursery}/plants/{plant}', [AdminController::class, 'showPlant'])->name('nurseries.plants.show');
    Route::put('/nurseries/{nursery}/plants/{plant}', [AdminController::class, 'updatePlant'])->name('nurseries.plants.update');
    Route::delete('/nurseries/{nursery}/plants/{plant}', [AdminController::class, 'destroyPlant'])->name('nurseries.plants.destroy');

    Route::get('/plant-options', [AdminController::class, 'plantOptions'])->name('plant-options');
    Route::post('/plant-options', [AdminController::class, 'storePlantOption'])->name('plant-options.store');
    Route::delete('/plant-options/{plantOption}', [AdminController::class, 'destroyPlantOption'])->name('plant-options.destroy');
});
