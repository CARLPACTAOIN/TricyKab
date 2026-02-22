<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TricycleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tricycles = \App\Models\Tricycle::with('toda')->latest()->paginate(10);
        $todas = \App\Models\Toda::all();
        return view('admin.tricycles.index', compact('tricycles', 'todas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $todas = \App\Models\Toda::all();
        return view('admin.tricycles.create', compact('todas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'body_number' => 'required|string|max:255|unique:tricycles,body_number',
            'plate_number' => 'required|string|max:255|unique:tricycles,plate_number',
            'toda_id' => 'required|exists:todas,id',
            'make_model' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        \App\Models\Tricycle::create($validated);

        return redirect()->route('tricycles.index')->with('success', 'Tricycle created successfully.');
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
        $tricycle = \App\Models\Tricycle::findOrFail($id);
        $todas = \App\Models\Toda::all();
        return view('admin.tricycles.edit', compact('tricycle', 'todas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'body_number' => 'required|string|max:255|unique:tricycles,body_number,' . $id,
            'plate_number' => 'required|string|max:255|unique:tricycles,plate_number,' . $id,
            'toda_id' => 'required|exists:todas,id',
            'make_model' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $tricycle = \App\Models\Tricycle::findOrFail($id);
        $tricycle->update($validated);

        return redirect()->route('tricycles.index')->with('success', 'Tricycle updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tricycle = \App\Models\Tricycle::findOrFail($id);
        $tricycle->delete();

        return redirect()->route('tricycles.index')->with('success', 'Tricycle deleted successfully.');
    }
}
