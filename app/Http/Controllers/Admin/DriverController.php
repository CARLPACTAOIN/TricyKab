<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Dispute;
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
            ->where('registration_status', 'ACTIVE')
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
            ->where('registration_status', 'ACTIVE')
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

        if (! empty($validated['tricycle_id'])) {
            $isValidTricycle = Tricycle::query()
                ->whereKey($validated['tricycle_id'])
                ->where('status', 'active')
                ->where('registration_status', 'ACTIVE')
                ->exists();
            if (! $isValidTricycle) {
                return back()->withErrors([
                    'tricycle_id' => 'Assigned tricycle must be active and LTO registration must be ACTIVE.',
                ])->withInput();
            }
        }

        Driver::create($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $driver = Driver::query()
            ->with(['toda', 'tricycle', 'bookings.payment', 'disputes.booking'])
            ->findOrFail($id);

        $range = request()->string('range')->toString() ?: 'today';
        $range = in_array($range, ['today', 'week', 'month'], true) ? $range : 'today';
        $start = match ($range) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfDay(),
        };

        $rangeBookings = $driver->bookings
            ->where('created_at', '>=', $start)
            ->sortByDesc('created_at')
            ->values();

        $acceptedTrips = $rangeBookings->whereNotNull('accepted_at')->count();
        $assignedTrips = $rangeBookings->whereNotNull('driver_id')->count();
        $completedTrips = $rangeBookings->where('status', Booking::STATUS_COMPLETED)->count();
        $cancelledTrips = $rangeBookings->filter(fn (Booking $booking) => $booking->isCancelled())->count();
        $totalTransactions = $rangeBookings->filter(fn (Booking $booking) => $booking->payment !== null)->count();
        $totalEarnings = (float) $rangeBookings
            ->map(fn (Booking $booking) => $booking->payment?->amount)
            ->filter()
            ->sum();
        $avgWaitMinutes = round((float) ($rangeBookings->whereNotNull('accepted_at')->avg(
            fn (Booking $booking) => $booking->created_at?->diffInMinutes($booking->accepted_at) ?? 0
        ) ?? 0), 1);

        $rangeDisputes = $driver->disputes
            ->where('created_at', '>=', $start)
            ->sortByDesc('created_at')
            ->values();

        $feedbacks = $rangeDisputes->map(function (Dispute $dispute) {
            return [
                'type' => $dispute->dispute_type,
                'status' => $dispute->status,
                'description' => $dispute->description,
                'reported_by' => $dispute->reported_by_name ?: $dispute->reported_by_role,
                'at' => $dispute->created_at,
            ];
        });

        return view('admin.drivers.show', [
            'driver' => $driver,
            'selectedRange' => $range,
            'metrics' => [
                'accepted_trips' => $acceptedTrips,
                'assigned_bookings' => $assignedTrips,
                'completed_trips' => $completedTrips,
                'cancelled_or_noshow' => $cancelledTrips,
                'transactions' => $totalTransactions,
                'earnings' => $totalEarnings,
                'avg_wait_minutes' => $avgWaitMinutes,
                'complaint_count' => $rangeDisputes->count(),
                'accumulated_rating' => (float) $driver->rating,
            ],
            'bookings' => $rangeBookings->take(25),
            'feedbacks' => $feedbacks->take(20),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $driver = Driver::findOrFail($id);
        $todas = Toda::where('status', 'active')->get();
        $tricycles = Tricycle::where('status', 'active')
            ->where('registration_status', 'ACTIVE')
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

        if (! empty($validated['tricycle_id'])) {
            $isValidTricycle = Tricycle::query()
                ->whereKey($validated['tricycle_id'])
                ->where('status', 'active')
                ->where('registration_status', 'ACTIVE')
                ->exists();
            if (! $isValidTricycle) {
                return back()->withErrors([
                    'tricycle_id' => 'Assigned tricycle must be active and LTO registration must be ACTIVE.',
                ])->withInput();
            }
        }

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
