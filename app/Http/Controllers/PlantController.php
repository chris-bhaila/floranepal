<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\PlantOption;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function create()
    {
        $nursery = Auth::user()->nursery;

        if (!$nursery) {
            return redirect()->route('nurseries.create')
                ->with('error', 'You need to create a nursery first.');
        }

        $options = PlantOption::all()->groupBy('type');
        return dashboardView('nurseries.plants.create', ['nursery' => $nursery, 'options' => $options]);
    }

    public function store(Request $request)
    {
        $user    = Auth::id();
        $nursery = Auth::user()->nursery;

        if (!$nursery) {
            return redirect()->route('nurseries.create')
                ->with('error', 'You need to create a nursery first.');
        }

        if (Auth::user()->subscription_type === 'general' && $nursery->plants()->count() >= 5) {
            return redirect()->back()
                ->with('error', 'Free accounts are limited to 5 plants. Upgrade to premium to add more.');
        }

        $categories           = PlantOption::where('type', 'category')->pluck('value')->toArray();
        $seasons              = PlantOption::where('type', 'best_season')->pluck('value')->toArray();
        $locations            = PlantOption::where('type', 'location')->pluck('value')->toArray();
        $sunlightRequirements = PlantOption::where('type', 'sunlight_requirement')->pluck('value')->toArray();
        $waterRequirements    = PlantOption::where('type', 'water_requirement')->pluck('value')->toArray();

        $request->validate([
            'name'                 => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'description'          => ['nullable', 'string', 'max:1000'],
            'plant_image'          => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'category'             => ['required', 'in:' . implode(',', $categories)],
            'offer_price'          => ['required', 'numeric', 'min:0', 'max:999999'],
            'selling_price'        => ['required', 'numeric', 'min:0', 'max:999999'],
            'stock_quantity'       => ['required', 'integer', 'min:0', 'max:99999'],
            'best_season'          => ['nullable', 'in:' . implode(',', $seasons)],
            'scientific_name'      => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-\.]+$/u'],
            'location'             => ['required', 'in:' . implode(',', $locations)],
            'sunlight_requirement' => ['nullable', 'in:' . implode(',', $sunlightRequirements)],
            'water_requirement'    => ['nullable', 'in:' . implode(',', $waterRequirements)],
        ], [
            'name.regex'            => 'Plant name may only contain letters, spaces, and hyphens.',
            'scientific_name.regex' => 'Scientific name may only contain letters, spaces, hyphens, and periods.',
            'offer_price.min'       => 'Price cannot be negative.',
            'selling_price.min'     => 'Price cannot be negative.',
            'stock_quantity.min'    => 'Stock quantity cannot be negative.',
        ]);

        $plantImgName = null;

        if ($request->hasFile('plant_image')) {
            $file         = $request->file('plant_image');
            $plantImgName = time() . '_plant.' . $file->getClientOriginalExtension();
            $file->storeAs('plants', $plantImgName, 'public');
        }

        $nursery->plants()->create([
            'name'                 => $request->name,
            'description'          => $request->description,
            'category'             => $request->category,
            'offer_price'          => $request->offer_price,
            'selling_price'        => $request->selling_price,
            'stock_quantity'       => $request->stock_quantity,
            'best_season'          => $request->best_season,
            'scientific_name'      => $request->scientific_name,
            'location'             => $request->location,
            'sunlight_requirement' => $request->sunlight_requirement,
            'water_requirement'    => $request->water_requirement,
            'image'                => $plantImgName,
        ]);

        return redirect()
            ->route('nursery.show')
            ->with('success', 'Plant added successfully!');
    }

    public function show(Plant $plant)
    {
        $options = PlantOption::all()->groupBy('type');

        if (request()->header('X-Dashboard-Navigate')) {
            return view('pages.dashboard.nurseries.plants.show', compact('plant', 'options'));
        }

        return view('pages.dashboard.sidebar', [
            'page'    => 'nurseries.plants.show',
            'plant'   => $plant,
            'options' => $options,
        ]);
    }

    public function update(Request $request, Plant $plant)
    {
        if ($plant->nursery->user_id !== Auth::id()) {
            abort(403);
        }

        $categories           = PlantOption::where('type', 'category')->pluck('value')->toArray();
        $seasons              = PlantOption::where('type', 'best_season')->pluck('value')->toArray();
        $locations            = PlantOption::where('type', 'location')->pluck('value')->toArray();
        $sunlightRequirements = PlantOption::where('type', 'sunlight_requirement')->pluck('value')->toArray();
        $waterRequirements    = PlantOption::where('type', 'water_requirement')->pluck('value')->toArray();

        $request->validate([
            'name'                 => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'description'          => ['nullable', 'string', 'max:1000'],
            'plant_image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'category'             => ['required', 'in:' . implode(',', $categories)],
            'offer_price'          => ['required', 'numeric', 'min:0', 'max:999999'],
            'selling_price'        => ['required', 'numeric', 'min:0', 'max:999999'],
            'stock_quantity'       => ['required', 'integer', 'min:0', 'max:99999'],
            'best_season'          => ['nullable', 'in:' . implode(',', $seasons)],
            'scientific_name'      => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-\.]+$/u'],
            'location'             => ['required', 'in:' . implode(',', $locations)],
            'sunlight_requirement' => ['nullable', 'in:' . implode(',', $sunlightRequirements)],
            'water_requirement'    => ['nullable', 'in:' . implode(',', $waterRequirements)],
        ]);

        $data = $request->except('plant_image');

        if ($request->hasFile('plant_image')) {
            if ($plant->image) {
                Storage::disk('public')->delete('plants/' . $plant->image);
            }
            $file = $request->file('plant_image');
            $plantImgName = time() . '_plant.' . $file->getClientOriginalExtension();
            $file->storeAs('plants', $plantImgName, 'public');
            $data['image'] = $plantImgName;
        }

        $plant->update($data);

        return redirect()->route('plants.show', $plant)->with('success', 'Plant updated.');
    }

    public function destroy(Plant $plant)
    {
        if ($plant->nursery->user_id !== Auth::id()) {
            abort(403);
        }

        if ($plant->image) {
            Storage::disk('public')->delete('plants/' . $plant->image);
        }

        $plant->delete();

        return redirect()->route('nursery.show')->with('success', 'Plant deleted.');
    }
}
