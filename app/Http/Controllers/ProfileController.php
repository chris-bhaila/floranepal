<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        return dashboardView('settings.editProfile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'    => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'phone'   => ['nullable', 'regex:/^\+?[0-9]{7,15}$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'avatar'  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && str_contains($user->avatar, 'file.view')) {
                $files = Storage::disk('local')->files($user->id);
                foreach ($files as $file) {
                    if (str_contains($file, '_avatar.')) {
                        Storage::disk('local')->delete($file);
                    }
                }
            }

            $file = $request->file('avatar');
            $avatarName = $user->id . '_avatar.' . $file->guessExtension();
            $file->storeAs($user->id, $avatarName, 'local');
            $user->avatar = $avatarName;
        }

        $user->update([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'address' => $request->address,
            'avatar'  => $user->avatar,
        ]);

        return redirect()->route('editProfile')->with('success', 'Profile updated successfully!');
    }

    public function addInfo()
    {
        if (!empty(Auth::user()->phone) && !empty(Auth::user()->address)) {
            return redirect()->route('dashboard');
        }

        // Check if it's a fetch navigation request
        if (request()->header('X-Dashboard-Navigate')) {
            return view('pages.dashboard.settings.additionalInfo');
        }

        return view('pages.dashboard.sidebar', [
            'page' => 'settings.additionalInfo',
        ]);
    }

    public function storeAdditionalInfo(Request $request)
    {
        if (!empty(Auth::user()->phone) && !empty(Auth::user()->address)) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'phone'   => ['required', 'regex:/^\+?[0-9]{7,15}$/'],
            'address' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\,\.\-]+$/'],
        ], [
            'phone.regex'   => 'Please enter a valid phone number (e.g. +9779800000000).',
            'address.regex' => 'Address may only contain letters, numbers, spaces, commas, periods, and hyphens.',
        ]);

        Auth::user()->update([
            'phone'               => $request->phone,
            'address'             => $request->address,
        ]);

        return redirect()->route('dashboard')->with('success', 'Account updated successfully!');
    }
}
