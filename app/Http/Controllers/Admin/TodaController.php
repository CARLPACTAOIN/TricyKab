<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Toda;

class TodaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todas = Toda::withCount(['tricycles', 'drivers'])->latest()->paginate(10);
        return view('admin.todas.index', compact('todas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.todas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:todas,name',
            'area_coverage' => 'nullable|string|max:255',
            'operating_hours' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Toda::create($validated);

        return redirect()->route('todas.index')->with('success', 'TODA created successfully.');
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
        $toda = Toda::findOrFail($id);
        return view('admin.todas.edit', compact('toda'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:todas,name,' . $id,
            'area_coverage' => 'nullable|string|max:255',
            'operating_hours' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $toda = Toda::findOrFail($id);
        $toda->update($validated);

        return redirect()->route('todas.index')->with('success', 'TODA updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $toda = Toda::findOrFail($id);
        $toda->delete();

        return redirect()->route('todas.index')->with('success', 'TODA deleted successfully.');
    }
}
