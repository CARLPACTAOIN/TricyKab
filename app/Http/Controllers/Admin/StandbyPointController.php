<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
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

    public function create()
    {
        return view('admin.standby-points.create', [
            'todas' => Toda::query()->orderBy('name')->get(),
            'barangays' => Barangay::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        StandbyPoint::create($data);

        return redirect()
            ->route('admin.standby-points')
            ->with('success', 'Standby point created.');
    }

    public function edit(StandbyPoint $standbyPoint)
    {
        return view('admin.standby-points.edit', [
            'standbyPoint' => $standbyPoint->load(['toda', 'barangay']),
            'todas' => Toda::query()->orderBy('name')->get(),
            'barangays' => Barangay::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, StandbyPoint $standbyPoint)
    {
        $data = $this->validatedData($request);

        $standbyPoint->update($data);

        return redirect()
            ->route('admin.standby-points')
            ->with('success', 'Standby point updated.');
    }

    public function destroy(StandbyPoint $standbyPoint)
    {
        $standbyPoint->delete();

        return redirect()
            ->route('admin.standby-points')
            ->with('success', 'Standby point deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'toda_id' => ['nullable', 'integer', 'exists:todas,id'],
            'barangay_id' => ['nullable', 'integer', 'exists:barangays,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:1', 'max:2000'],
            'priority_weight' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'string', 'in:ACTIVE,INACTIVE'],
        ], [
            'toda_id.exists' => 'Selected TODA does not exist.',
            'barangay_id.exists' => 'Selected barangay does not exist.',
        ]);
    }
}
