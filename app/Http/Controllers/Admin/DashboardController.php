<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
use App\Models\Booking;
use App\Models\Toda;
use App\Services\Admin\AdminExportService;
use App\Services\Admin\DashboardMetricsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardMetricsService $metrics,
        private readonly AdminExportService $exports,
    ) {}

    public function index(Request $request)
    {
        $filters = $this->metrics->resolveFiltersFromRequest(
            $request->user(),
            $request->string('range')->toString() ?: '7d',
            $request->filled('toda_id') ? (int) $request->input('toda_id') : null,
            $request->filled('barangay_id') ? (int) $request->input('barangay_id') : null,
        );

        $metrics = $this->metrics->build(
            $request->user(),
            $filters['range'],
            $filters['toda_id'],
            $filters['barangay_id'],
        );

        return view('dashboard', array_merge($metrics, [
            'selectedRange' => $filters['range'],
            'selectedTodaId' => $filters['toda_id'],
            'selectedBarangayId' => $filters['barangay_id'],
            'todas' => Toda::query()->orderBy('name')->get(),
            'barangays' => Barangay::query()->orderBy('name')->get(),
            'pickupHeatmapPoints' => $metrics['pickup_heatmap_points'],
            'destinationHeatmapPoints' => $metrics['destination_heatmap_points'],
            'avgWaitMinutes' => $metrics['avg_wait_minutes'],
            'bookingToAcceptRate' => $metrics['booking_to_accept_rate'],
            'completionRate' => $metrics['completion_rate'],
            'activeDrivers' => $metrics['active_drivers'],
            'onlineDrivers' => $metrics['online_drivers'],
            'driversOnTrip' => $metrics['drivers_on_trip'],
            'driverAvailabilityRate' => $metrics['driver_availability_rate'],
            'tripsToday' => $metrics['trips_today'],
            'bookingChart' => $metrics['booking_chart'],
            'waitTimeChart' => $metrics['wait_time_chart'],
            'tripsPerBarangay' => $metrics['trips_per_barangay'],
            'latestBookings' => $metrics['latest_bookings'],
            'adminRoleLabel' => $metrics['admin_role_label'],
            'scopeNote' => $metrics['scope_note'],
        ]));
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->exportBookings($request);

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

    public function exportPdf(Request $request)
    {
        $filters = $this->metrics->resolveFiltersFromRequest(
            $request->user(),
            $request->string('range')->toString() ?: '7d',
            $request->filled('toda_id') ? (int) $request->input('toda_id') : null,
            $request->filled('barangay_id') ? (int) $request->input('barangay_id') : null,
        );

        $metrics = $this->metrics->build(
            $request->user(),
            $filters['range'],
            $filters['toda_id'],
            $filters['barangay_id'],
        );

        $todaName = $filters['toda_id']
            ? Toda::query()->find($filters['toda_id'])?->name
            : null;
        $barangayName = $filters['barangay_id']
            ? Barangay::query()->find($filters['barangay_id'])?->name
            : null;

        return $this->exports->downloadPdf('admin.exports.dashboard-pdf', [
            'generatedAt' => now(),
            'rangeLabel' => $this->metrics->rangeLabel($filters['range']),
            'todaName' => $todaName,
            'barangayName' => $barangayName,
            'metrics' => $metrics,
            'adminRoleLabel' => $metrics['admin_role_label'],
        ], 'dashboard-kpi-'.now()->format('Ymd-His').'.pdf');
    }

    /**
     * @return \Illuminate\Support\Collection<int, Booking>
     */
    private function exportBookings(Request $request)
    {
        $filters = $this->metrics->resolveFiltersFromRequest(
            $request->user(),
            $request->string('range')->toString() ?: '7d',
            $request->filled('toda_id') ? (int) $request->input('toda_id') : null,
            $request->filled('barangay_id') ? (int) $request->input('barangay_id') : null,
        );

        return $this->metrics
            ->filteredBookingsQuery($filters['range'], $filters['toda_id'], $filters['barangay_id'])
            ->latest('created_at')
            ->get();
    }
}
