<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StandbyPoint;
use App\Models\Toda;
use Illuminate\Http\Request;

class StandbyPointController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $selectedTodaId = $request->filled('toda_id') ? (int) $request->input('toda_id') : null;
        $selectedStatus = strtoupper((string) $request->input('status', ''));
        $selectedStatus = in_array($selectedStatus, ['ACTIVE', 'INACTIVE'], true) ? $selectedStatus : null;

        $query = StandbyPoint::query()
            ->with(['toda', 'barangay'])
            ->orderByRaw("CASE WHEN status = 'ACTIVE' THEN 0 ELSE 1 END")
            ->orderByDesc('priority_weight')
            ->orderBy('name');

        if ($search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($selectedTodaId) {
            $query->where('toda_id', $selectedTodaId);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        $standbyPoints = $query->get();

        return view('admin.standby-points.index', [
            'standbyPoints' => $standbyPoints,
            'todas' => Toda::query()->orderBy('name')->get(),
            'selectedTodaId' => $selectedTodaId,
            'selectedStatus' => $selectedStatus,
            'search' => $search,
            'mapPayload' => [
                'points' => $standbyPoints->map(fn (StandbyPoint $point) => [
                    'name' => $point->name,
                    'lat' => (float) $point->latitude,
                    'lng' => (float) $point->longitude,
                    'radiusMeters' => (int) $point->radius_meters,
                    'status' => $point->status,
                ])->values()->all(),
            ],
            'activePointCount' => $standbyPoints->where('status', 'ACTIVE')->count(),
        ]);
    }
}
