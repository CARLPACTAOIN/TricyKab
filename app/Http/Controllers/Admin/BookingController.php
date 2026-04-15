<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Toda;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $selectedRideType = $request->string('ride_type')->toString() ?: null;
        $selectedRideType = in_array($selectedRideType, ['shared', 'special'], true) ? $selectedRideType : null;
        $selectedTodaId = $request->filled('toda_id') ? (int) $request->input('toda_id') : null;
        $selectedDateScope = $request->string('date_scope')->toString() ?: 'today';
        $selectedDateScope = in_array($selectedDateScope, ['today', '7d', 'all'], true) ? $selectedDateScope : 'today';
        $statusTabs = $this->statusTabs();
        $selectedTab = $request->string('tab')->toString() ?: 'all';
        $selectedTab = array_key_exists($selectedTab, $statusTabs) ? $selectedTab : 'all';

        $summaryQuery = $this->applyFilters(
            Booking::query()->with(['passenger', 'driver.toda']),
            $search,
            $selectedRideType,
            $selectedTodaId,
            $selectedDateScope
        );

        $summaryBookings = (clone $summaryQuery)->get();
        $bookingsQuery = clone $summaryQuery;

        if ($statusTabs[$selectedTab]['statuses']) {
            $bookingsQuery->whereIn('status', $statusTabs[$selectedTab]['statuses']);
        }

        $bookings = $bookingsQuery
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        $activeStatuses = [
            Booking::STATUS_CREATED,
            Booking::STATUS_SEARCHING_DRIVER,
            Booking::STATUS_DRIVER_ASSIGNED,
            Booking::STATUS_DRIVER_ON_THE_WAY,
            Booking::STATUS_DRIVER_ARRIVED,
            Booking::STATUS_TRIP_IN_PROGRESS,
        ];

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'todas' => Toda::query()->orderBy('name')->get(),
            'statusTabs' => collect($statusTabs)->map(function (array $tab) use ($summaryBookings) {
                $statuses = $tab['statuses'];
                $tab['count'] = $statuses
                    ? $summaryBookings->whereIn('status', $statuses)->count()
                    : $summaryBookings->count();

                return $tab;
            })->all(),
            'selectedTab' => $selectedTab,
            'selectedRideType' => $selectedRideType,
            'selectedTodaId' => $selectedTodaId,
            'selectedDateScope' => $selectedDateScope,
            'search' => $search,
            'summary' => [
                'total' => $summaryBookings->count(),
                'active' => $summaryBookings->whereIn('status', $activeStatuses)->count(),
                'completed_today' => $summaryBookings
                    ->where('status', Booking::STATUS_COMPLETED)
                    ->filter(fn (Booking $booking) => $booking->completed_at?->isToday())
                    ->count(),
                'cancelled' => $summaryBookings->filter(fn (Booking $booking) => $booking->isCancelled())->count(),
            ],
        ]);
    }

    public function show(string $reference)
    {
        $booking = Booking::query()
            ->with(['passenger', 'driver.toda', 'driver.tricycle', 'tricycle', 'payment', 'originBarangay', 'destinationBarangay'])
            ->where('booking_reference', $reference)
            ->firstOrFail();

        $estimatedDurationMinutes = $booking->started_at && $booking->completed_at
            ? $booking->started_at->diffInMinutes($booking->completed_at)
            : max((int) round(((float) ($booking->distance_km ?? 0)) * 4), 1);

        $dispatchAttempts = [[
            'attempt' => 1,
            'radius' => '1,000m',
            'candidates' => $booking->driver_id ? 1 : 0,
            'winner' => $booking->driver?->full_name ?? '—',
            'status' => $booking->driver_id ? 'ASSIGNED' : $booking->status,
            'duration' => $booking->accepted_at
                ? $booking->created_at?->diffInSeconds($booking->accepted_at) . 's'
                : '—',
        ]];

        return view('admin.bookings.show', [
            'booking' => $booking,
            'timeline' => $this->buildTimeline($booking),
            'dispatchAttempts' => $dispatchAttempts,
            'routeMapPayload' => [
                'reference' => $booking->booking_reference,
                'pickup' => [
                    'lat' => (float) $booking->pickup_lat,
                    'lng' => (float) $booking->pickup_lng,
                    'address' => $booking->pickup_address,
                ],
                'destination' => [
                    'lat' => (float) $booking->destination_lat,
                    'lng' => (float) $booking->destination_lng,
                    'address' => $booking->destination_address,
                ],
                'distanceKm' => (float) ($booking->distance_km ?? 0),
                'durationMinutes' => $estimatedDurationMinutes,
            ],
            'routeSummary' => [
                'distance_meters' => $booking->distance_km ? (int) round($booking->distance_km * 1000) : null,
                'duration_minutes' => $estimatedDurationMinutes,
                'receipt_reference' => $booking->payment
                    ? sprintf('RCT-%s-%06d', $booking->created_at?->format('Y') ?? now()->format('Y'), $booking->payment->id)
                    : null,
            ],
        ]);
    }

    private function applyFilters(
        Builder $query,
        string $search,
        ?string $rideType,
        ?int $todaId,
        string $dateScope
    ): Builder {
        if ($search !== '') {
            $query->where(function (Builder $bookingQuery) use ($search) {
                $bookingQuery
                    ->where('booking_reference', 'like', '%' . $search . '%')
                    ->orWhere('pickup_address', 'like', '%' . $search . '%')
                    ->orWhere('destination_address', 'like', '%' . $search . '%')
                    ->orWhereHas('passenger', function (Builder $passengerQuery) use ($search) {
                        $passengerQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('driver', function (Builder $driverQuery) use ($search) {
                        $driverQuery
                            ->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('license_number', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($rideType) {
            $query->where('ride_type', $rideType);
        }

        if ($todaId) {
            $query->whereHas('driver', function (Builder $driverQuery) use ($todaId) {
                $driverQuery->where('toda_id', $todaId);
            });
        }

        if ($dateScope === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($dateScope === '7d') {
            $query->where('created_at', '>=', now()->startOfDay()->subDays(6));
        }

        return $query;
    }

    private function buildTimeline(Booking $booking): array
    {
        $statusRank = [
            Booking::STATUS_CREATED => 0,
            Booking::STATUS_SEARCHING_DRIVER => 1,
            Booking::STATUS_DRIVER_ASSIGNED => 2,
            Booking::STATUS_DRIVER_ON_THE_WAY => 3,
            Booking::STATUS_DRIVER_ARRIVED => 4,
            Booking::STATUS_TRIP_IN_PROGRESS => 5,
            Booking::STATUS_COMPLETED => 6,
            Booking::STATUS_CANCELLED_BY_PASSENGER => 1,
            Booking::STATUS_CANCELLED_BY_DRIVER => 2,
            Booking::STATUS_CANCELLED_NO_DRIVER => 1,
            Booking::STATUS_NO_SHOW_PASSENGER => 4,
            Booking::STATUS_NO_SHOW_DRIVER => 3,
        ];

        $currentRank = $statusRank[$booking->status] ?? 0;

        return [
            ['label' => 'CREATED', 'time' => $booking->created_at?->format('H:i:s'), 'done' => true],
            ['label' => 'SEARCHING', 'time' => $booking->created_at?->format('H:i:s'), 'done' => $currentRank >= 1],
            ['label' => 'ASSIGNED', 'time' => $booking->accepted_at?->format('H:i:s'), 'done' => $currentRank >= 2],
            ['label' => 'ON THE WAY', 'time' => $booking->accepted_at?->format('H:i:s'), 'done' => $currentRank >= 3],
            ['label' => 'ARRIVED', 'time' => $booking->accepted_at?->format('H:i:s'), 'done' => $currentRank >= 4],
            ['label' => 'IN PROGRESS', 'time' => $booking->started_at?->format('H:i:s'), 'done' => $currentRank >= 5],
            ['label' => 'COMPLETED', 'time' => $booking->completed_at?->format('H:i:s'), 'done' => $currentRank >= 6],
        ];
    }

    private function statusTabs(): array
    {
        return [
            'all' => ['label' => 'All', 'statuses' => null],
            'searching' => ['label' => 'Searching', 'statuses' => [Booking::STATUS_CREATED, Booking::STATUS_SEARCHING_DRIVER]],
            'assigned' => ['label' => 'Assigned', 'statuses' => [Booking::STATUS_DRIVER_ASSIGNED, Booking::STATUS_DRIVER_ON_THE_WAY, Booking::STATUS_DRIVER_ARRIVED]],
            'in_progress' => ['label' => 'In Progress', 'statuses' => [Booking::STATUS_TRIP_IN_PROGRESS]],
            'completed' => ['label' => 'Completed', 'statuses' => [Booking::STATUS_COMPLETED]],
            'cancelled' => ['label' => 'Cancelled', 'statuses' => [Booking::STATUS_CANCELLED_BY_PASSENGER, Booking::STATUS_CANCELLED_BY_DRIVER, Booking::STATUS_CANCELLED_NO_DRIVER]],
            'no_show' => ['label' => 'No-Show', 'statuses' => [Booking::STATUS_NO_SHOW_PASSENGER, Booking::STATUS_NO_SHOW_DRIVER]],
        ];
    }
}
