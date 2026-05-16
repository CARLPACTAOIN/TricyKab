<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Dispute;
use App\Models\Toda;
use App\Services\Admin\AdminExportService;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    private const ADMIN_OVERRIDEABLE_STATUSES = [
        Booking::STATUS_CANCELLED_BY_PASSENGER,
        Booking::STATUS_CANCELLED_BY_DRIVER,
        Booking::STATUS_CANCELLED_NO_DRIVER,
        Booking::STATUS_COMPLETED,
        Booking::STATUS_NO_SHOW_PASSENGER,
        Booking::STATUS_NO_SHOW_DRIVER,
    ];

    public function __construct(
        private readonly AuditLogger $audit,
        private readonly AdminExportService $exports,
    ) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $selectedRideType = $request->string('ride_type')->toString() ?: null;
        $selectedRideType = in_array($selectedRideType, ['shared', 'special'], true) ? $selectedRideType : null;
        if ($request->user()->isTodaAdmin()) {
            $selectedTodaId = $request->user()->toda_id;
        } else {
            $selectedTodaId = $request->filled('toda_id') ? (int) $request->input('toda_id') : null;
        }
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
                ? $booking->created_at?->diffInSeconds($booking->accepted_at).'s'
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

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filteredExportBookings($request);

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['BookingReference', 'Status', 'RideType', 'Passenger', 'Driver', 'TODA', 'Fare', 'CreatedAt', 'CompletedAt']);
            foreach ($rows as $booking) {
                fputcsv($handle, [
                    $booking->booking_reference,
                    $booking->status,
                    $booking->ride_type,
                    $booking->passenger?->name,
                    $booking->driver?->full_name,
                    $booking->driver?->toda?->name,
                    $booking->fare_amount,
                    $booking->created_at?->toDateTimeString(),
                    $booking->completed_at?->toDateTimeString(),
                ]);
            }
            fclose($handle);
        }, 'bookings-export-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportPdf(Request $request)
    {
        $rows = $this->filteredExportBookings($request);

        return $this->exports->downloadPdf('admin.exports.bookings-pdf', [
            'generatedAt' => now(),
            'adminRoleLabel' => $request->user()->adminRoleLabel(),
            'bookings' => $rows,
            'total' => $rows->count(),
        ], 'bookings-export-'.now()->format('Ymd-His').'.pdf');
    }

    /**
     * @return \Illuminate\Support\Collection<int, Booking>
     */
    private function filteredExportBookings(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $selectedRideType = $request->string('ride_type')->toString() ?: null;
        $selectedRideType = in_array($selectedRideType, ['shared', 'special'], true) ? $selectedRideType : null;
        if ($request->user()->isTodaAdmin()) {
            $selectedTodaId = $request->user()->toda_id;
        } else {
            $selectedTodaId = $request->filled('toda_id') ? (int) $request->input('toda_id') : null;
        }
        $selectedDateScope = $request->string('date_scope')->toString() ?: 'today';
        $selectedDateScope = in_array($selectedDateScope, ['today', '7d', 'all'], true) ? $selectedDateScope : 'today';
        $statusTabs = $this->statusTabs();
        $selectedTab = $request->string('tab')->toString() ?: 'all';
        $selectedTab = array_key_exists($selectedTab, $statusTabs) ? $selectedTab : 'all';

        $query = $this->applyFilters(
            Booking::query()->with(['passenger', 'driver.toda']),
            $search,
            $selectedRideType,
            $selectedTodaId,
            $selectedDateScope
        );

        if ($statusTabs[$selectedTab]['statuses']) {
            $query->whereIn('status', $statusTabs[$selectedTab]['statuses']);
        }

        return $query->latest('created_at')->get();
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
                    ->where('booking_reference', 'like', '%'.$search.'%')
                    ->orWhere('pickup_address', 'like', '%'.$search.'%')
                    ->orWhere('destination_address', 'like', '%'.$search.'%')
                    ->orWhereHas('passenger', function (Builder $passengerQuery) use ($search) {
                        $passengerQuery->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('driver', function (Builder $driverQuery) use ($search) {
                        $driverQuery
                            ->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%')
                            ->orWhere('license_number', 'like', '%'.$search.'%');
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

    /**
     * PRD §6.5 — LGU admin overrides booking status with mandatory reason.
     * Returns JSON; UI updates badge in-place without page reload.
     */
    public function override(Request $request, string $reference): JsonResponse
    {
        // Only LGU admins may perform status overrides
        if (! $request->user()->isLguAdmin()) {
            return response()->json([
                'success' => false,
                'error' => 'FORBIDDEN',
                'message' => 'Only LGU administrators can override booking status.',
            ], 403);
        }

        $request->validate([
            'new_status' => ['required', 'string', Rule::in(self::ADMIN_OVERRIDEABLE_STATUSES)],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $booking = Booking::query()->where('reference', $reference)->firstOrFail();

        return DB::transaction(function () use ($request, $booking): JsonResponse {
            $previous = $booking->toArray();
            $previousStatus = $booking->status;
            $newStatus = $request->input('new_status');

            $booking->status = $newStatus;
            if ($newStatus === Booking::STATUS_COMPLETED && $booking->completed_at === null) {
                $booking->completed_at = now();
            }
            $booking->save();

            $auditRow = $this->audit->logModelChange(
                model: $booking,
                action: 'ADMIN_BOOKING_STATUS_OVERRIDE',
                previousAttributes: $previous,
                newAttributes: $booking->fresh()->toArray(),
                actor: $request->user(),
                reason: $request->input('reason'),
                request: $request,
                targetFields: ['status'],
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'reference' => $booking->reference,
                    'previous_status' => $previousStatus,
                    'new_status' => $booking->status,
                    'audit_log_id' => $auditRow?->id,
                    'overridden_at' => now()->toIso8601String(),
                ],
            ]);
        });
    }

    /**
     * PRD §9.5 — Returns receipt payload JSON for in-page modal display.
     */
    public function receiptData(string $reference): JsonResponse
    {
        $booking = Booking::query()
            ->with('receipt')
            ->where('reference', $reference)
            ->firstOrFail();

        if ($booking->receipt === null) {
            return response()->json([
                'success' => false,
                'error' => 'RECEIPT_NOT_FOUND',
                'message' => 'No receipt has been generated for this booking yet.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'booking_reference' => $booking->reference,
                'receipt' => $booking->receipt->receipt_payload_json ?? $booking->receipt->toArray(),
            ],
        ]);
    }

    /**
     * PRD §7.19 — Admin can file a dispute on behalf of parties.
     */
    public function openDispute(Request $request, string $reference): JsonResponse
    {
        $request->validate([
            'dispute_type' => ['required', 'string', Rule::in(['FARE', 'NO_SHOW', 'GPS', 'CONDUCT', 'SAFETY', 'OTHER'])],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $booking = Booking::query()->where('reference', $reference)->firstOrFail();

        $existing = Dispute::query()
            ->where('booking_id', $booking->id)
            ->where('status', 'OPEN')
            ->first();

        if ($existing !== null) {
            return response()->json([
                'success' => false,
                'error' => 'DISPUTE_ALREADY_OPEN',
                'message' => 'An open dispute already exists for this booking.',
                'dispute_id' => $existing->id,
            ], 409);
        }

        $dispute = Dispute::query()->create([
            'booking_id' => $booking->id,
            'driver_id' => $booking->driver_id,
            'reported_by_role' => 'ADMIN',
            'reported_by_name' => $request->user()->name ?? 'Admin',
            'dispute_type' => $request->input('dispute_type'),
            'description' => $request->input('description'),
            'status' => 'OPEN',
        ]);

        $this->audit->log(
            actor: $request->user(),
            objectType: 'DISPUTE',
            objectId: $dispute->id,
            action: 'DISPUTE_FILED_BY_ADMIN',
            previous: null,
            next: ['booking_id' => $booking->id, 'type' => $dispute->dispute_type],
            reason: $request->input('dispute_type'),
            ipAddress: $request->ip(),
        );

        return response()->json([
            'success' => true,
            'data' => [
                'dispute_id' => $dispute->id,
                'booking_reference' => $booking->reference,
                'dispute_type' => $dispute->dispute_type,
                'status' => $dispute->status,
            ],
        ], 201);
    }
}
