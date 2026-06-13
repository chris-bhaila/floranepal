<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Nursery;
use App\Models\Plant;
use App\Models\PlantOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    private function adminView(string $page, array $data = [])
    {
        $allowed = [
            'dashboard',
            'users.index',
            'users.show',
            'nurseries.index',
            'nurseries.show',
            'nurseries.plants.show',
            'plant-options',
        ];

        if (!in_array($page, $allowed, true)) {
            abort(404);
        }

        if (request()->header('X-Dashboard-Navigate')) {
            return view('pages.admin.' . str_replace('.', '/', $page), $data);
        }

        return view('pages.admin.sidebar', array_merge(['page' => $page], $data));
    }

    public function dashboard()
    {
        $data = [
            'totalUsers'           => User::count(),
            'totalNurseries'       => Nursery::count(),
            'pendingVerifications' => User::where('verification_status', 'unverified')->count(),
        ];

        return $this->adminView('dashboard', $data);
    }
    public function users()
    {
        return $this->adminView('users.index', [
            'users' => User::latest()->get(),
        ]);
    }

    public function showUser(User $user)
    {
        return $this->adminView('users.show', [
            'user' => $user,
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email,' . $user->id,
            'phone'               => 'nullable|string|max:20',
            'address'             => 'nullable|string|max:255',
            'avatar'              => 'nullable|image|max:2048',
            'verification_status' => 'required|in:unverified,verified',
            'subscription_type'   => 'required|in:general,premium,admin',
        ]);

        $data = $request->only([
            'name',
            'email',
            'phone',
            'address',
            'verification_status',
            'subscription_type',
        ]);

        if ($request->boolean('clear_avatar')) {
            $data['avatar'] = null;
        } elseif ($request->hasFile('avatar')) {
            $oldFiles = Storage::disk('local')->files($user->id);
            foreach ($oldFiles as $file) {
                if (str_contains($file, '_avatar.')) {
                    Storage::disk('local')->delete($file);
                }
            }
            $file = $request->file('avatar');
            $avatarName = $user->id . '_avatar.' . $file->guessExtension();
            $file->storeAs($user->id, $avatarName, 'local');
            $data['avatar'] = $avatarName; // 👈 just the filename
        }

        $user->update($data);

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted.');
    }

    //Nursery Section
    public function nurseries()
    {
        return $this->adminView('nurseries.index', [
            'nurseries' => Nursery::with('user')->latest()->get(),
        ]);
    }

    public function showNursery(Nursery $nursery)
    {
        return $this->adminView('nurseries.show', [
            'nursery' => $nursery->load('plants', 'user'),
        ]);
    }

    public function updateNursery(Request $request, Nursery $nursery)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'location'      => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            // 'is_active'  => 'required|boolean', 👈 remove this
            'reg_cer'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pan_cer'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->only([
            'name',
            'location',
            'description',
            'contact_phone',
            'contact_email',
            // 'is_active',
        ]);

        if ($request->hasFile('reg_cer')) {
            $userId = $nursery->user_id;
            // Delete old file
            $oldFiles = Storage::disk('local')->files($userId);
            foreach ($oldFiles as $file) {
                if (str_contains($file, '_reg_cer.')) {
                    Storage::disk('local')->delete($file);
                }
            }
            $file = $request->file('reg_cer');
            $filename = $userId . '_reg_cer.' . $file->guessExtension();
            $file->storeAs($userId, $filename, 'local');
            $data['reg_cer'] = $filename;
        }

        if ($request->hasFile('pan_cer')) {
            $userId = $nursery->user_id;
            // Delete old file
            $oldFiles = Storage::disk('local')->files($userId);
            foreach ($oldFiles as $file) {
                if (str_contains($file, '_pan_cer.')) {
                    Storage::disk('local')->delete($file);
                }
            }
            $file = $request->file('pan_cer');
            $filename = $userId . '_pan_cer.' . $file->guessExtension();
            $file->storeAs($userId, $filename, 'local');
            $data['pan_cer'] = $filename;
        }
        $nursery->update($data);

        return redirect()->route('admin.nurseries.show', $nursery)->with('success', 'Nursery updated.');
    }

    public function destroyNursery(Nursery $nursery)
    {
        $nursery->delete();

        return redirect()->route('admin.nurseries')->with('success', 'Nursery deleted.');
    }

    public function verifyNurseryOwner(Nursery $nursery)
    {
        if (!$nursery->user) {
            return redirect()->route('admin.nurseries.show', $nursery)
                ->with('error', 'This nursery has no linked user account to verify.');
        }

        $nursery->user->update(['verification_status' => 'verified']);

        return redirect()->route('admin.nurseries.show', $nursery)
            ->with('success', "{$nursery->user->name} has been verified.");
    }

    //Plants Section
    public function showPlant(Nursery $nursery, Plant $plant)
    {
        $options = PlantOption::all()->groupBy('type');
        return $this->adminView('nurseries.plants.show', [
            'nursery' => $nursery,
            'plant'   => $plant,
            'options' => $options,
        ]);
    }

    public function updatePlant(Request $request, Nursery $nursery, Plant $plant)
    {
        $request->validate([
            'name'                 => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'description'          => ['nullable', 'string', 'max:1000'],
            'plant_image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'category'             => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'offer_price'          => ['required', 'numeric', 'min:0', 'max:999999'],
            'selling_price'        => ['required', 'numeric', 'min:0', 'max:999999'],
            'stock_quantity'       => ['required', 'integer', 'min:0', 'max:99999'],
            'best_season'          => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'scientific_name'      => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.]+$/u'],
            'location'             => ['required', 'string', 'max:255'],
            'sunlight_requirement' => ['nullable', 'string', 'max:255'],
            'water_requirement'    => ['nullable', 'string', 'max:255'],
        ]);

        $data = $request->only([
            'name',
            'description',
            'category',
            'offer_price',
            'selling_price',
            'stock_quantity',
            'best_season',
            'scientific_name',
            'location',
            'sunlight_requirement',
            'water_requirement',
        ]);

        if ($request->hasFile('plant_image')) {
            if ($plant->image) {
                Storage::disk('public')->delete('plants/' . $plant->image);
            }
            $file = $request->file('plant_image');
            $plantImgName = time() . '_plant.' . $file->guessExtension();
            $file->storeAs('plants', $plantImgName, 'public');
            $data['image'] = $plantImgName;
        }

        $plant->update($data);

        return redirect()->route('admin.nurseries.plants.show', [$nursery, $plant])->with('success', 'Plant updated.');
    }

    public function destroyPlant(Nursery $nursery, Plant $plant)
    {
        if ($plant->image) {
            Storage::disk('public')->delete('plants/' . $plant->image);
        }

        $plant->delete();

        return redirect()->route('admin.nurseries.show', $nursery)->with('success', 'Plant deleted.');
    }

    //Plant Options Section
    public function plantOptions()
    {
        $options = PlantOption::all()->groupBy('type');
        return $this->adminView('plant-options', ['options' => $options]);
    }

    public function storePlantOption(Request $request)
    {
        $request->validate([
            'type'  => 'required|in:category,best_season,location,sunlight_requirement,water_requirement',
            'value' => 'required|string|max:255',
        ]);

        PlantOption::create([
            'type'  => $request->type,
            'value' => strtolower(str_replace(' ', '_', $request->value)),
        ]);

        return redirect()->route('admin.plant-options')->with('success', 'Option added.');
    }

    public function destroyPlantOption(PlantOption $plantOption)
    {
        $plantOption->delete();
        return redirect()->route('admin.plant-options')->with('success', 'Option deleted.');
    }
}
