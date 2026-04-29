<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Dispute;
use App\Models\Driver;
use App\Models\Payment;
use App\Models\Toda;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->string('range')->toString() ?: '7d';
        $range = in_array($range, ['7d', '30d', 'month'], true) ? $range : '7d';
        $todaId = $request->filled('toda_id') ? (int) $request->input('toda_id') : null;
        $start = $this->resolveRangeStart($range);

        $bookingsQuery = Booking::query()->with('driver')->where('created_at', '>=', $start);
        if ($todaId) {
            $bookingsQuery->whereHas('driver', fn ($q) => $q->where('toda_id', $todaId));
        }
        $bookings = $bookingsQuery->get();

        $acceptedCount = $bookings->whereNotNull('accepted_at')->count();
        $assignedCount = $bookings->whereNotNull('driver_id')->count();
        $completedCount = $bookings->where('status', Booking::STATUS_COMPLETED)->count();
        $waitMinutes = round((float) ($bookings->whereNotNull('accepted_at')->avg(
            fn (Booking $booking) => $booking->created_at?->diffInMinutes($booking->accepted_at) ?? 0
        ) ?? 0), 1);

        $paymentsQuery = Payment::query()->where('created_at', '>=', $start);
        if ($todaId) {
            $paymentsQuery->whereHas('booking.driver', fn ($q) => $q->where('toda_id', $todaId));
        }
        $totalEarnings = (float) $paymentsQuery->sum('amount');

        $driverQuery = Driver::query()->with(['toda', 'bookings.payment', 'disputes']);
        if ($todaId) {
            $driverQuery->where('toda_id', $todaId);
        }
        $drivers = $driverQuery->get();

        $topDrivers = $drivers
            ->map(function (Driver $driver) use ($start) {
                $rangeBookings = $driver->bookings->where('created_at', '>=', $start);
                $accepted = $rangeBookings->whereNotNull('accepted_at')->count();
                $completed = $rangeBookings->where('status', Booking::STATUS_COMPLETED)->count();
                $earnings = (float) $rangeBookings
                    ->map(fn (Booking $booking) => $booking->payment?->amount)
                    ->filter()
                    ->sum();

                return [
                    'driver' => $driver,
                    'accepted' => $accepted,
                    'completed' => $completed,
                    'earnings' => $earnings,
                ];
            })
            ->sortByDesc(fn (array $entry) => ($entry['completed'] * 1000) + $entry['earnings'])
            ->take(5)
            ->values();

        $flaggedDrivers = $drivers
            ->map(function (Driver $driver) use ($start) {
                $complaints = $driver->disputes
                    ->whereIn('status', ['OPEN', 'UNDER_REVIEW'])
                    ->where('created_at', '>=', $start)
                    ->count();

                return [
                    'driver' => $driver,
                    'complaints' => $complaints,
                    'latest_disputes' => $driver->disputes->sortByDesc('created_at')->take(3),
                ];
            })
            ->where('complaints', '>', 0)
            ->sortByDesc('complaints')
            ->take(5)
            ->values();

        $tripsByRideType = [
            'shared' => $bookings->where('ride_type', 'shared')->count(),
            'special' => $bookings->where('ride_type', 'special')->count(),
        ];

        $tripsPerToda = Toda::query()
            ->withCount(['drivers as trips_count' => function ($driverQuery) use ($start) {
                $driverQuery->whereHas('bookings', function ($bookingQuery) use ($start) {
                    $bookingQuery->where('created_at', '>=', $start);
                });
            }])
            ->orderByDesc('trips_count')
            ->get()
            ->map(fn (Toda $toda) => ['name' => $toda->name, 'count' => $toda->trips_count]);

        return view('admin.analytics.index', [
            'selectedRange' => $range,
            'selectedTodaId' => $todaId,
            'todas' => Toda::query()->orderBy('name')->get(),
            'kpis' => [
                'avg_wait_time' => $waitMinutes,
                'booking_to_accept_rate' => $bookings->count() > 0 ? round(($acceptedCount / $bookings->count()) * 100) : 0,
                'completion_rate' => $assignedCount > 0 ? round(($completedCount / $assignedCount) * 100) : 0,
                'active_drivers' => $drivers->where('status', 'active')->count(),
                'total_trips' => $bookings->count(),
                'total_earnings' => $totalEarnings,
            ],
            'tripsByRideType' => $tripsByRideType,
            'tripsPerToda' => $tripsPerToda,
            'topDrivers' => $topDrivers,
            'flaggedDrivers' => $flaggedDrivers,
            'disputeTotals' => [
                'open' => Dispute::query()->where('status', 'OPEN')->count(),
                'under_review' => Dispute::query()->where('status', 'UNDER_REVIEW')->count(),
            ],
        ]);
    }

    private function resolveRangeStart(string $range)
    {
        return match ($range) {
            '30d' => now()->startOfDay()->subDays(29),
            'month' => now()->startOfMonth(),
            default => now()->startOfDay()->subDays(6),
        };
    }
}
