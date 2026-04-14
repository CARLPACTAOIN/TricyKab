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

        return view('admin.fares.index', compact('sharedMatrix', 'specialMatrix'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ride_type' => 'required|in:shared,special',
            'base_fare' => 'required|numeric|min:0',
            'per_km_rate' => 'required|numeric|min:0',
            'multiplier' => 'required_if:ride_type,special|numeric|min:0.1',
            'min_fare' => 'required|numeric|min:0',
            'max_fare' => 'required|numeric|gte:min_fare',
            'effective_date' => 'required|date',
        ]);

        if ($validated['ride_type'] === 'shared') {
            $validated['multiplier'] = 1.0;
        }

        FareMatrix::create($validated);

        return redirect()->back()->with('success', 'Fare rule for ' . ucfirst($validated['ride_type']) . ' rides saved successfully.');
    }
}
