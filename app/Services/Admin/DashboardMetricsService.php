<?php

namespace App\Services\Admin;

use App\Models\Barangay;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * PRD §17 / §20 — Dashboard KPI aggregation shared by web view and PDF export.
 */
class DashboardMetricsService
{
    /**
     * @return array{
     *     range: string,
     *     toda_id: ?int,
     *     barangay_id: ?int,
     *     admin_role_label: string,
     *     scope_note: ?string,
     *     avg_wait_minutes: float,
     *     booking_to_accept_rate: int,
     *     completion_rate: int,
     *     active_drivers: int,
     *     online_drivers: int,
     *     drivers_on_trip: int,
     *     driver_availability_rate: int,
     *     trips_today: int,
     *     total_bookings: int,
     *     booking_chart: array{categories: array<int, string>, bookings: array<int, int>, completed: array<int, int>},
     *     wait_time_chart: array{categories: array<int, string>, values: array<int, float>},
     *     trips_per_barangay: array<int, array{name: string, origin_count: int, destination_count: int, total: int}>,
     *     pickup_heatmap_points: array<int, array{lat: float, lng: float, weight: int, reference: string}>,
     *     destination_heatmap_points: array<int, array{lat: float, lng: float, weight: int, reference: string}>,
     *     latest_bookings: \Illuminate\Support\Collection<int, Booking>
     * }
     */
    public function build(User $user, string $range, ?int $todaId, ?int $barangayId): array
    {
        $range = in_array($range, ['7d', '30d', 'month'], true) ? $range : '7d';

        $filteredQuery = $this->applyBookingFilters(
            Booking::query()->with(['passenger', 'driver', 'originBarangay', 'destinationBarangay']),
            $range,
            $todaId,
            $barangayId
        );

        $filteredBookings = (clone $filteredQuery)->get();
        $latestBookings = (clone $filteredQuery)->latest('created_at')->limit(10)->get();

        $acceptedBookings = $filteredBookings->filter(fn (Booking $booking) => $booking->accepted_at !== null);
        $assignedBookings = $filteredBookings->filter(fn (Booking $booking) => $booking->driver_id !== null);
        $completedBookings = $filteredBookings->where('status', Booking::STATUS_COMPLETED);

        $driverQuery = Driver::query();
        if ($todaId) {
            $driverQuery->where('toda_id', $todaId);
        }

        $activeDrivers = (clone $driverQuery)->where('status', 'active')->count();
        $onlineDrivers = (clone $driverQuery)->where('status', 'active')->where('availability_status', 'ONLINE')->count();
        $driversOnTrip = (clone $driverQuery)
            ->where('status', 'active')
            ->whereHas('bookings', function (Builder $bookingQuery) {
                $bookingQuery->whereIn('status', [
                    Booking::STATUS_DRIVER_ASSIGNED,
                    Booking::STATUS_DRIVER_ON_THE_WAY,
                    Booking::STATUS_DRIVER_ARRIVED,
                    Booking::STATUS_TRIP_IN_PROGRESS,
                ]);
            })
            ->count();

        $chartWindow = $this->buildChartWindow($range);

        return [
            'range' => $range,
            'toda_id' => $todaId,
            'barangay_id' => $barangayId,
            'admin_role_label' => $user->adminRoleLabel(),
            'scope_note' => $user->isTodaAdmin()
                ? 'Showing data for '.$user->toda?->name.' only.'
                : ($user->isTmuAdmin()
                    ? 'TMU operational view — municipality-wide visibility.'
                    : 'LGU operational view — municipality-wide visibility.'),
            'avg_wait_minutes' => round((float) ($acceptedBookings->avg(
                fn (Booking $booking) => $booking->created_at?->diffInMinutes($booking->accepted_at) ?? 0
            ) ?? 0), 1),
            'booking_to_accept_rate' => $filteredBookings->isNotEmpty()
                ? (int) round(($acceptedBookings->count() / $filteredBookings->count()) * 100)
                : 0,
            'completion_rate' => $assignedBookings->isNotEmpty()
                ? (int) round(($completedBookings->count() / $assignedBookings->count()) * 100)
                : 0,
            'active_drivers' => $activeDrivers,
            'online_drivers' => $onlineDrivers,
            'drivers_on_trip' => $driversOnTrip,
            'driver_availability_rate' => $activeDrivers > 0
                ? (int) round(($onlineDrivers / $activeDrivers) * 100)
                : 0,
            'trips_today' => $completedBookings->filter(fn (Booking $booking) => $booking->completed_at?->isToday())->count(),
            'total_bookings' => $filteredBookings->count(),
            'booking_chart' => [
                'categories' => $chartWindow->pluck('label')->all(),
                'bookings' => $chartWindow->map(function (array $entry) use ($filteredBookings) {
                    return $filteredBookings->filter(
                        fn (Booking $booking) => $booking->created_at?->isSameDay($entry['date'])
                    )->count();
                })->all(),
                'completed' => $chartWindow->map(function (array $entry) use ($filteredBookings) {
                    return $filteredBookings->filter(
                        fn (Booking $booking) => $booking->completed_at?->isSameDay($entry['date'])
                    )->count();
                })->all(),
            ],
            'wait_time_chart' => [
                'categories' => $chartWindow->pluck('label')->all(),
                'values' => $chartWindow->map(function (array $entry) use ($acceptedBookings) {
                    return round((float) ($acceptedBookings->filter(
                        fn (Booking $booking) => $booking->created_at?->isSameDay($entry['date'])
                    )->avg(fn (Booking $booking) => $booking->created_at?->diffInMinutes($booking->accepted_at) ?? 0) ?? 0), 1);
                })->all(),
            ],
            'trips_per_barangay' => $this->buildTripsPerBarangay($filteredBookings),
            'pickup_heatmap_points' => $filteredBookings
                ->filter(fn (Booking $booking) => $booking->pickup_lat !== null && $booking->pickup_lng !== null)
                ->map(fn (Booking $booking) => [
                    'lat' => (float) $booking->pickup_lat,
                    'lng' => (float) $booking->pickup_lng,
                    'weight' => 1,
                    'reference' => $booking->booking_reference,
                ])
                ->values()
                ->all(),
            'destination_heatmap_points' => $filteredBookings
                ->filter(fn (Booking $booking) => $booking->destination_lat !== null && $booking->destination_lng !== null)
                ->map(fn (Booking $booking) => [
                    'lat' => (float) $booking->destination_lat,
                    'lng' => (float) $booking->destination_lng,
                    'weight' => 1,
                    'reference' => $booking->booking_reference,
                ])
                ->values()
                ->all(),
            'latest_bookings' => $latestBookings,
        ];
    }

    /**
     * @return array{range: string, toda_id: ?int, barangay_id: ?int}
     */
    public function resolveFiltersFromRequest(User $user, string $range, ?int $requestTodaId, ?int $barangayId): array
    {
        $range = in_array($range, ['7d', '30d', 'month'], true) ? $range : '7d';

        if ($user->isTodaAdmin()) {
            $todaId = $user->toda_id;
        } else {
            $todaId = $requestTodaId;
        }

        return [
            'range' => $range,
            'toda_id' => $todaId,
            'barangay_id' => $barangayId,
        ];
    }

    public function rangeLabel(string $range): string
    {
        return match ($range) {
            '30d' => 'Last 30 days',
            'month' => 'This month',
            default => 'Last 7 days',
        };
    }

    public function filteredBookingsQuery(string $range, ?int $todaId, ?int $barangayId): Builder
    {
        return $this->applyBookingFilters(
            Booking::query()->with(['passenger', 'driver']),
            $range,
            $todaId,
            $barangayId
        );
    }

    /**
     * @return array<int, array{name: string, origin_count: int, destination_count: int, total: int}>
     */
    private function buildTripsPerBarangay(Collection $bookings): array
    {
        $barangayNames = Barangay::query()->pluck('name', 'id');

        $counts = [];

        foreach ($bookings as $booking) {
            if ($booking->origin_barangay_id) {
                $counts[$booking->origin_barangay_id]['origin'] = ($counts[$booking->origin_barangay_id]['origin'] ?? 0) + 1;
            }
            if ($booking->destination_barangay_id) {
                $counts[$booking->destination_barangay_id]['destination'] = ($counts[$booking->destination_barangay_id]['destination'] ?? 0) + 1;
            }
        }

        return collect($counts)
            ->map(function (array $entry, int $barangayId) use ($barangayNames) {
                $origin = (int) ($entry['origin'] ?? 0);
                $destination = (int) ($entry['destination'] ?? 0);

                return [
                    'name' => $barangayNames[$barangayId] ?? 'Unknown',
                    'origin_count' => $origin,
                    'destination_count' => $destination,
                    'total' => $origin + $destination,
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->take(10)
            ->all();
    }

    private function applyBookingFilters(Builder $query, string $range, ?int $todaId, ?int $barangayId): Builder
    {
        $query->where('created_at', '>=', $this->resolveRangeStart($range));

        if ($todaId) {
            $query->whereHas('driver', function (Builder $driverQuery) use ($todaId) {
                $driverQuery->where('toda_id', $todaId);
            });
        }

        if ($barangayId) {
            $query->where(function (Builder $bookingQuery) use ($barangayId) {
                $bookingQuery
                    ->where('origin_barangay_id', $barangayId)
                    ->orWhere('destination_barangay_id', $barangayId);
            });
        }

        return $query;
    }

    private function resolveRangeStart(string $range): CarbonImmutable
    {
        return match ($range) {
            '30d' => CarbonImmutable::now()->startOfDay()->subDays(29),
            'month' => CarbonImmutable::now()->startOfMonth(),
            default => CarbonImmutable::now()->startOfDay()->subDays(6),
        };
    }

    /**
     * @return Collection<int, array{date: CarbonInterface, label: string}>
     */
    private function buildChartWindow(string $range): Collection
    {
        $dates = collect();
        $cursor = $this->resolveRangeStart($range)->copy();
        $end = CarbonImmutable::now()->endOfDay();

        while ($cursor->lte($end)) {
            $dates->push($cursor->copy());
            $cursor = $cursor->addDay();
        }

        return $dates->map(fn (CarbonInterface $date) => [
            'date' => $date,
            'label' => $date->format('M d'),
        ]);
    }
}
