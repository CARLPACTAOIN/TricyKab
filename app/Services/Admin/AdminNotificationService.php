<?php

namespace App\Services\Admin;

use App\Models\Booking;
use App\Models\Dispute;
use App\Models\SosAlert;
use App\Models\User;
use Illuminate\Support\Carbon;

class AdminNotificationService
{
    private const DISMISS_SESSION_KEY = 'dismissed_admin_notifications';

    /**
     * @return array{items: list<array<string, mixed>>, unread_count: int}
     */
    public function forUser(User $user): array
    {
        $dismissed = collect(session(self::DISMISS_SESSION_KEY, []))->flip();
        $items = $user->isLguAdmin()
            ? $this->lguItems()
            : $this->todaItems($user);

        $items = collect($items)
            ->reject(fn (array $item) => $dismissed->has($item['key']))
            ->sortByDesc('created_at')
            ->values()
            ->take(20)
            ->map(function (array $item) {
                $item['created_at_human'] = Carbon::parse($item['created_at'])->diffForHumans();

                return $item;
            })
            ->all();

        return [
            'items' => $items,
            'unread_count' => count($items),
        ];
    }

    public function dismiss(User $user, string $key): void
    {
        $keys = session(self::DISMISS_SESSION_KEY, []);
        if (! in_array($key, $keys, true)) {
            $keys[] = $key;
            session([self::DISMISS_SESSION_KEY => $keys]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function lguItems(): array
    {
        $items = [];

        SosAlert::query()
            ->with('booking')
            ->where('status', 'OPEN')
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->each(function (SosAlert $alert) use (&$items) {
                $ref = $alert->booking?->booking_reference;
                $items[] = [
                    'key' => 'sos:'.$alert->id,
                    'type' => 'sos',
                    'severity' => 'critical',
                    'icon' => 'sos',
                    'title' => 'SOS alert',
                    'body' => trim(($alert->passenger_name ?: 'Passenger').($ref ? " · {$ref}" : '')),
                    'url' => route('admin.sos'),
                    'created_at' => $alert->created_at?->toIso8601String(),
                ];
            });

        Dispute::query()
            ->with('booking')
            ->whereIn('status', ['OPEN', 'UNDER_REVIEW'])
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->each(function (Dispute $dispute) use (&$items) {
                $ref = $dispute->booking?->booking_reference;
                $items[] = [
                    'key' => 'dispute:'.$dispute->id,
                    'type' => 'dispute',
                    'severity' => $dispute->status === 'OPEN' ? 'warning' : 'info',
                    'icon' => 'gavel',
                    'title' => 'Dispute · '.$dispute->status,
                    'body' => trim(($dispute->dispute_type ?: 'Report').($ref ? " · {$ref}" : '')),
                    'url' => route('admin.disputes'),
                    'created_at' => $dispute->created_at?->toIso8601String(),
                ];
            });

        return $items;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function todaItems(User $user): array
    {
        $items = [];
        $todaId = $user->toda_id;

        Booking::query()
            ->with(['passenger', 'driver'])
            ->active()
            ->whereHas('driver', fn ($q) => $q->where('toda_id', $todaId))
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->each(function (Booking $booking) use (&$items) {
                $items[] = [
                    'key' => 'booking:'.$booking->id,
                    'type' => 'booking',
                    'severity' => $booking->status === Booking::STATUS_SEARCHING_DRIVER ? 'warning' : 'info',
                    'icon' => 'receipt_long',
                    'title' => 'Active booking',
                    'body' => trim($booking->booking_reference.' · '.str_replace('_', ' ', $booking->status)),
                    'url' => route('admin.bookings.show', $booking->booking_reference),
                    'created_at' => $booking->created_at?->toIso8601String(),
                ];
            });

        return $items;
    }
}
