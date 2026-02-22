<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Toda;
use App\Models\Driver;
use App\Models\Tricycle;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search across TODAs, Drivers, and Tricycles.
     */
    public function index(Request $request)
    {
        $q = $request->get('q', '');
        $q = trim($q);

        $todas = collect();
        $drivers = collect();
        $tricycles = collect();

        if (strlen($q) >= 2) {
            $term = '%' . $q . '%';
            $todas = Toda::where(function ($query) use ($term) {
                $query->where('name', 'like', $term)->orWhere('area_coverage', 'like', $term);
            })->limit(5)->get();
            $drivers = Driver::with(['toda', 'tricycle'])
                ->where(function ($query) use ($term) {
                    $query->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('license_number', 'like', $term);
                })
                ->limit(5)
                ->get();
            $tricycles = Tricycle::with(['toda', 'driver'])
                ->where(function ($query) use ($term) {
                    $query->where('body_number', 'like', $term)
                        ->orWhere('plate_number', 'like', $term)
                        ->orWhere('make_model', 'like', $term);
                })
                ->limit(5)
                ->get();
        }

        return view('admin.search.index', compact('q', 'todas', 'drivers', 'tricycles'));
    }
}
