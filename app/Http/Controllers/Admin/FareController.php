<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FareMatrix;

class FareController extends Controller
{
    public function index()
    {
        $matrices = FareMatrix::latest('effective_date')->get()->groupBy('ride_type');
        $sharedMatrix = $matrices->get('shared', collect())->first();
        $specialMatrix = $matrices->get('special', collect())->first();
        $cargoMatrix = $matrices->get('cargo', collect())->first();

        return view('admin.fares.index', compact('sharedMatrix', 'specialMatrix', 'cargoMatrix'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ride_type' => 'required|in:shared,special,cargo',
            'base_fare' => 'required|numeric|min:0',
            'per_km_rate' => 'required|numeric|min:0',
            'minimum_distance' => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'per_passenger_addon' => 'nullable|numeric|min:0',
            'rush_hour_surcharge' => 'nullable|numeric|min:0',
            'night_diff_percentage' => 'nullable|numeric|min:0|max:100',
            'effective_date' => 'required|date',
        ]);

        FareMatrix::create($validated);

        return redirect()->back()->with('success', 'Fare rule for ' . ucfirst($validated['ride_type']) . ' rides saved successfully.');
    }
}
