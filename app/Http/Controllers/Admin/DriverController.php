<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Toda;
use App\Models\Tricycle;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Driver::with(['toda', 'tricycle']);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('license_number', 'like', "%{$term}%")
                    ->orWhere('contact_number', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }

        $drivers = $query->latest()->paginate(10)->withQueryString();
        $todas = Toda::where('status', 'active')->get();
        $tricycles = Tricycle::where('status', 'active')
            ->doesntHave('driver')
            ->get();
        return view('admin.drivers.index', compact('drivers', 'todas', 'tricycles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $todas = Toda::where('status', 'active')->get();
        $tricycles = Tricycle::where('status', 'active')
            ->doesntHave('driver')
            ->get();
        return view('admin.drivers.create', compact('todas', 'tricycles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:drivers,license_number',
            'contact_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'toda_id' => 'nullable|exists:todas,id',
            'tricycle_id' => 'nullable|exists:tricycles,id',
            'status' => 'required|in:active,inactive',
        ]);

        Driver::create($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $driver = Driver::findOrFail($id);
        $todas = Toda::where('status', 'active')->get();
        $tricycles = Tricycle::where('status', 'active')
            ->where(function ($query) use ($driver) {
                $query->doesntHave('driver')
                    ->orWhere('id', $driver->tricycle_id);
            })
            ->get();
        return view('admin.drivers.edit', compact('driver', 'todas', 'tricycles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:drivers,license_number,' . $id,
            'contact_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'toda_id' => 'nullable|exists:todas,id',
            'tricycle_id' => 'nullable|exists:tricycles,id',
            'status' => 'required|in:active,inactive',
        ]);

        $driver = Driver::findOrFail($id);
        $driver->update($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully.');
    }
}
