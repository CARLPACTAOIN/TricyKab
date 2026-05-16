<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Dispute;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * PRD §7.19 — Dispute filing for passengers and drivers.
 *
 * POST /api/v1/bookings/{booking}/dispute
 *
 * Both passengers (on their bookings) and drivers (on their assigned bookings)
 * may file a dispute. Only one OPEN dispute per booking per reporter is allowed.
 */
class DisputeController extends Controller
{
    private const ALLOWED_TYPES = [
        'FARE',
        'NO_SHOW',
        'GPS',
        'CONDUCT',
        'SAFETY',
        'OTHER',
    ];

    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    public function store(Request $request, Booking $booking): JsonResponse
    {
        $request->validate([
            'dispute_type' => ['required', 'string', Rule::in(self::ALLOWED_TYPES)],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Determine reporter role and validate booking ownership
        [$reporterRole, $reporterIdentityId] = $this->resolveReporter($user, $booking);

        if ($reporterRole === null) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'You are not a participant in this booking.',
                ],
            ], 403);
        }

        // Booking must be in a state where disputes make sense (not just PENDING/SEARCHING)
        $nonDisputeableStatuses = [
            Booking::STATUS_PENDING,
            Booking::STATUS_SEARCHING_DRIVER,
        ];
        if (in_array($booking->status, $nonDisputeableStatuses, true)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'BOOKING_NOT_DISPUTABLE',
                    'message' => 'Disputes cannot be filed for bookings that have not yet been actioned.',
                ],
            ], 422);
        }

        // Prevent duplicate OPEN disputes from the same reporter on the same booking
        $existing = Dispute::query()
            ->where('booking_id', $booking->id)
            ->where('reported_by_role', $reporterRole)
            ->where('status', 'OPEN')
            ->first();

        if ($existing !== null) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DISPUTE_ALREADY_OPEN',
                    'message' => 'An open dispute already exists for this booking.',
                    'dispute_id' => $existing->id,
                ],
            ], 409);
        }

        $dispute = Dispute::query()->create([
            'booking_id' => $booking->id,
            'driver_id' => $booking->driver_id,
            'reported_by_role' => $reporterRole,
            'reported_by_name' => $user->name ?? $reporterRole,
            'dispute_type' => $request->input('dispute_type'),
            'description' => $request->input('description'),
            'status' => 'OPEN',
        ]);

        $this->audit->log(
            actor: $user,
            objectType: 'DISPUTE',
            objectId: $dispute->id,
            action: 'DISPUTE_FILED',
            previous: null,
            next: [
                'booking_id' => $booking->id,
                'dispute_type' => $dispute->dispute_type,
                'reported_by_role' => $reporterRole,
            ],
            reason: $request->input('dispute_type'),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'success' => true,
            'data' => [
                'dispute_id' => $dispute->id,
                'booking_reference' => $booking->reference,
                'dispute_type' => $dispute->dispute_type,
                'status' => $dispute->status,
                'reported_by_role' => $reporterRole,
                'filed_at' => $dispute->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Identifies the reporter's role from their user model and booking context.
     *
     * @return array{string|null, int|null} [reporterRole, identityId]
     */
    private function resolveReporter(\App\Models\User $user, Booking $booking): array
    {
        // Check if passenger
        if ($user->role === 'passenger' && (int) $booking->passenger_id === (int) $user->id) {
            return ['PASSENGER', $user->id];
        }

        // Check if driver (via user → driver relationship)
        if ($user->role === 'driver') {
            $driver = \App\Models\Driver::query()->where('user_id', $user->id)->first();
            if ($driver !== null && (int) $booking->driver_id === (int) $driver->id) {
                return ['DRIVER', $driver->id];
            }
        }

        return [null, null];
    }
}
