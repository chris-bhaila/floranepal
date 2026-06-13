<?php

namespace App\Http\Controllers;

use App\Models\Nursery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NurseryController extends Controller
{
    public function create()
    {
        if (Auth::user()->nursery) {
            return redirect()->route('nursery.show')
                ->with('error', 'You already have a nursery.');
        }

        if (request()->header('X-Dashboard-Navigate')) {
            return view('pages.dashboard.nurseries.create');
        }

        return view('pages.dashboard.sidebar', [
            'page' => 'nurseries.create',
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::id();
        if (Auth::user()->nursery) {
            return redirect()->back()
                ->with('error', 'You already have a nursery and cannot create another.');
        }

        $request->validate([
            'name'        => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'phone'       => ['required', 'regex:/^\+?[0-9]{7,15}$/'],
            'email'       => ['required', 'string', 'email', 'max:255'],
            'location'    => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\,\.\-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'reg-cer'     => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'pan-cer'     => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'name.regex'     => 'Nursery name may only contain letters, spaces, and hyphens.',
            'phone.regex'    => 'Please enter a valid phone number (e.g. +9779800000000).',
            'location.regex' => 'Location may only contain letters, numbers, spaces, commas, periods, and hyphens.',
            'reg-cer.required' => 'Registration certificate is required.',
            'pan-cer.required' => 'PAN certificate is required.',
        ]);

        $regCerName = null;
        if ($request->hasFile('reg-cer')) {
            $regCerFile = $request->file('reg-cer');
            $regCerName = $user . '_reg_cer.' . $regCerFile->guessExtension();
            $regCerFile->storeAs($user, $regCerName, 'local');
        }

        $panCerName = null;
        if ($request->hasFile('pan-cer')) {
            $panCerFile = $request->file('pan-cer');
            $panCerName = $user . '_pan_cer.' . $panCerFile->guessExtension();
            $panCerFile->storeAs($user, $panCerName, 'local');
        }

        try {
            Nursery::create([
                'user_id'       => Auth::id(),
                'google_id'     => Auth::user()->google_id,
                'name'          => $request->name,
                'contact_phone' => $request->phone,
                'contact_email' => $request->email,
                'location'      => $request->location,
                'description'   => $request->description,
                'reg_cer'       => $regCerName,
                'pan_cer'       => $panCerName,
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return redirect()->back()
                ->with('error', 'You already have a nursery and cannot create another.');
        }

        return redirect()->route('nursery.show')
            ->with('success', 'Nursery created successfully!');
    }

    public function viewFile($filename)
    {
        $user = Auth::id();
        $path = $user . '/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($path));
    }
}
