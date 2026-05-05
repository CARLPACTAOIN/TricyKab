<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Toda;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedRange = $request->string('range')->toString() ?: '7d';
        $selectedRange = in_array($selectedRange, ['7d', '30d', 'month'], true) ? $selectedRange : '7d';
        if ($request->user()->isTodaAdmin()) {
            $selectedTodaId = $request->user()->toda_id;
        } else {
            $selectedTodaId = $request->filled('toda_id') ? (int) $request->input('toda_id') : null;
        }
        $selectedBarangayId = $request->filled('barangay_id') ? (int) $request->input('barangay_id') : null;

        $filteredQuery = $this->applyFilters(
            Booking::query()->with(['passenger', 'driver']),
            $selectedRange,
            $selectedTodaId,
            $selectedBarangayId
        );

        $filteredBookings = (clone $filteredQuery)->get();
        $latestBookings = (clone $filteredQuery)->latest('created_at')->limit(10)->get();

        $pickupHeatmapPoints = $filteredBookings
            ->filter(fn (Booking $booking) => $booking->pickup_lat !== null && $booking->pickup_lng !== null)
            ->map(fn (Booking $booking) => [
                'lat' => (float) $booking->pickup_lat,
                'lng' => (float) $booking->pickup_lng,
                'weight' => 1,
                'reference' => $booking->booking_reference,
            ])
            ->values()
            ->all();

        $destinationHeatmapPoints = $filteredBookings
            ->filter(fn (Booking $booking) => $booking->destination_lat !== null && $booking->destination_lng !== null)
            ->map(fn (Booking $booking) => [
                'lat' => (float) $booking->destination_lat,
                'lng' => (float) $booking->destination_lng,
                'weight' => 1,
                'reference' => $booking->booking_reference,
            ])
            ->values()
            ->all();

        $acceptedBookings = $filteredBookings->filter(fn (Booking $booking) => $booking->accepted_at !== null);
        $assignedBookings = $filteredBookings->filter(fn (Booking $booking) => $booking->driver_id !== null);
        $completedBookings = $filteredBookings->where('status', Booking::STATUS_COMPLETED);

        $avgWaitMinutes = round((float) ($acceptedBookings->avg(
            fn (Booking $booking) => $booking->created_at?->diffInMinutes($booking->accepted_at) ?? 0
        ) ?? 0), 1);

        $bookingToAcceptRate = $filteredBookings->isNotEmpty()
            ? round(($acceptedBookings->count() / $filteredBookings->count()) * 100)
            : 0;

        $completionRate = $assignedBookings->isNotEmpty()
            ? round(($completedBookings->count() / $assignedBookings->count()) * 100)
            : 0;

        $driverQuery = Driver::query();
        if ($request->user()->isTodaAdmin()) {
            $driverQuery->where('toda_id', $request->user()->toda_id);
        }

        $activeDrivers = (clone $driverQuery)->where('status', 'active')->count();
        $totalDrivers = (clone $driverQuery)->count();
        $driverAvailabilityRate = $totalDrivers > 0
            ? round(($activeDrivers / $totalDrivers) * 100)
            : 0;

        $chartWindow = $this->buildChartWindow($selectedRange);
        $bookingChart = [
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
        ];

        $waitTimeChart = [
            'categories' => $chartWindow->pluck('label')->all(),
            'values' => $chartWindow->map(function (array $entry) use ($acceptedBookings) {
                return round((float) ($acceptedBookings->filter(
                    fn (Booking $booking) => $booking->created_at?->isSameDay($entry['date'])
                )->avg(fn (Booking $booking) => $booking->created_at?->diffInMinutes($booking->accepted_at) ?? 0) ?? 0), 1);
            })->all(),
        ];

        return view('dashboard', [
            'todas' => Toda::query()->orderBy('name')->get(),
            'barangays' => Barangay::query()->orderBy('name')->get(),
            'selectedRange' => $selectedRange,
            'selectedTodaId' => $selectedTodaId,
            'selectedBarangayId' => $selectedBarangayId,
            'avgWaitMinutes' => $avgWaitMinutes,
            'bookingToAcceptRate' => $bookingToAcceptRate,
            'completionRate' => $completionRate,
            'activeDrivers' => $activeDrivers,
            'tripsToday' => $completedBookings->filter(fn (Booking $booking) => $booking->completed_at?->isToday())->count(),
            'driverAvailabilityRate' => $driverAvailabilityRate,
            'bookingChart' => $bookingChart,
            'waitTimeChart' => $waitTimeChart,
            'pickupHeatmapPoints' => $pickupHeatmapPoints,
            'destinationHeatmapPoints' => $destinationHeatmapPoints,
            'latestBookings' => $latestBookings,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $selectedRange = $request->string('range')->toString() ?: '7d';
        $selectedRange = in_array($selectedRange, ['7d', '30d', 'month'], true) ? $selectedRange : '7d';
        if ($request->user()->isTodaAdmin()) {
            $selectedTodaId = $request->user()->toda_id;
        } else {
            $selectedTodaId = $request->filled('toda_id') ? (int) $request->input('toda_id') : null;
        }
        $selectedBarangayId = $request->filled('barangay_id') ? (int) $request->input('barangay_id') : null;

        $filteredQuery = $this->applyFilters(
            Booking::query()->with(['passenger', 'driver']),
            $selectedRange,
            $selectedTodaId,
            $selectedBarangayId
        );

        $rows = (clone $filteredQuery)->latest('created_at')->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['BookingReference', 'Status', 'RideType', 'Passenger', 'Driver', 'Fare', 'CreatedAt', 'CompletedAt']);
            foreach ($rows as $booking) {
                fputcsv($handle, [
                    $booking->booking_reference,
                    $booking->status,
                    $booking->ride_type,
                    $booking->passenger?->name,
                    $booking->driver?->full_name,
                    $booking->fare_amount,
                    $booking->created_at?->toDateTimeString(),
                    $booking->completed_at?->toDateTimeString(),
                ]);
            }
            fclose($handle);
        }, 'dashboard-kpi-export-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function applyFilters(Builder $query, string $range, ?int $todaId, ?int $barangayId): Builder
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

    private function resolveRangeStart(string $range): Carbon
    {
        return match ($range) {
            '30d' => now()->startOfDay()->subDays(29),
            'month' => now()->startOfMonth(),
            default => now()->startOfDay()->subDays(6),
        };
    }

    private function buildChartWindow(string $range): Collection
    {
        $dates = collect();
        $cursor = $this->resolveRangeStart($range)->copy();
        $end = now()->copy()->endOfDay();

        while ($cursor->lte($end)) {
            $dates->push($cursor->copy());
            $cursor->addDay();
        }

        return $dates->map(fn (Carbon $date) => [
            'date' => $date,
            'label' => $date->format('M d'),
        ]);
    }
}
