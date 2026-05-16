<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Toda;
use App\Models\Tricycle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search across TODAs, Drivers, Tricycles, and Bookings.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $todas = collect();
        $drivers = collect();
        $tricycles = collect();
        $bookings = collect();

        if (strlen($q) >= 2) {
            $term = '%'.$q.'%';
            $user = $request->user();

            if ($user->isLguAdmin()) {
                $todas = Toda::where(function ($query) use ($term) {
                    $query->where('name', 'like', $term)->orWhere('area_coverage', 'like', $term);
                })->limit(5)->get();
            }

            $driversQuery = Driver::with(['toda', 'tricycle'])
                ->where(function ($query) use ($term) {
                    $query->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('license_number', 'like', $term);
                });
            if ($user->isTodaAdmin()) {
                $driversQuery->where('toda_id', $user->toda_id);
            }
            $drivers = $driversQuery->limit(5)->get();

            $tricyclesQuery = Tricycle::with(['toda', 'driver'])
                ->where(function ($query) use ($term) {
                    $query->where('body_number', 'like', $term)
                        ->orWhere('plate_number', 'like', $term)
                        ->orWhere('make_model', 'like', $term);
                });
            if ($user->isTodaAdmin()) {
                $tricyclesQuery->where('toda_id', $user->toda_id);
            }
            $tricycles = $tricyclesQuery->limit(5)->get();

            $bookings = $this->bookingSearchQuery($user, $term)->limit(5)->get();
        }

        return view('admin.search.index', compact('q', 'todas', 'drivers', 'tricycles', 'bookings'));
    }

    public function suggest(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['groups' => []]);
        }

        $term = '%'.$q.'%';
        $user = $request->user();
        $groups = [];

        if ($user->isLguAdmin()) {
            $todas = Toda::where(function ($query) use ($term) {
                $query->where('name', 'like', $term)->orWhere('area_coverage', 'like', $term);
            })->limit(4)->get();

            if ($todas->isNotEmpty()) {
                $groups[] = [
                    'label' => 'TODAs',
                    'items' => $todas->map(fn (Toda $toda) => [
                        'label' => $toda->name,
                        'meta' => $toda->area_coverage,
                        'url' => route('todas.edit', $toda),
                    ])->all(),
                ];
            }
        }

        $driversQuery = Driver::query()
            ->where(function ($query) use ($term) {
                $query->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('license_number', 'like', $term);
            });
        if ($user->isTodaAdmin()) {
            $driversQuery->where('toda_id', $user->toda_id);
        }
        $drivers = $driversQuery->limit(4)->get();
        if ($drivers->isNotEmpty()) {
            $groups[] = [
                'label' => 'Drivers',
                'items' => $drivers->map(fn (Driver $driver) => [
                    'label' => trim($driver->first_name.' '.$driver->last_name),
                    'meta' => $driver->license_number,
                    'url' => route('drivers.edit', $driver),
                ])->all(),
            ];
        }

        $tricyclesQuery = Tricycle::query()
            ->where(function ($query) use ($term) {
                $query->where('body_number', 'like', $term)
                    ->orWhere('plate_number', 'like', $term)
                    ->orWhere('make_model', 'like', $term);
            });
        if ($user->isTodaAdmin()) {
            $tricyclesQuery->where('toda_id', $user->toda_id);
        }
        $tricycles = $tricyclesQuery->limit(4)->get();
        if ($tricycles->isNotEmpty()) {
            $groups[] = [
                'label' => 'Tricycles',
                'items' => $tricycles->map(fn (Tricycle $tricycle) => [
                    'label' => $tricycle->body_number.' · '.$tricycle->plate_number,
                    'meta' => $tricycle->make_model,
                    'url' => route('tricycles.edit', $tricycle),
                ])->all(),
            ];
        }

        $bookings = $this->bookingSearchQuery($user, $term)->limit(4)->get();
        if ($bookings->isNotEmpty()) {
            $groups[] = [
                'label' => 'Bookings',
                'items' => $bookings->map(fn (Booking $booking) => [
                    'label' => $booking->booking_reference,
                    'meta' => str_replace('_', ' ', $booking->status),
                    'url' => route('admin.bookings.show', $booking->booking_reference),
                ])->all(),
            ];
        }

        return response()->json(['groups' => $groups]);
    }

    private function bookingSearchQuery($user, string $term): Builder
    {
        $query = Booking::query()
            ->with(['passenger', 'driver'])
            ->where(function (Builder $bookingQuery) use ($term) {
                $bookingQuery
                    ->where('booking_reference', 'like', $term)
                    ->orWhere('pickup_address', 'like', $term)
                    ->orWhere('destination_address', 'like', $term)
                    ->orWhereHas('passenger', fn (Builder $passengerQuery) => $passengerQuery->where('name', 'like', $term))
                    ->orWhereHas('driver', function (Builder $driverQuery) use ($term) {
                        $driverQuery
                            ->where('first_name', 'like', $term)
                            ->orWhere('last_name', 'like', $term)
                            ->orWhere('license_number', 'like', $term);
                    });
            });

        if ($user->isTodaAdmin()) {
            $query->whereHas('driver', fn (Builder $driverQuery) => $driverQuery->where('toda_id', $user->toda_id));
        }

        return $query->latest('created_at');
    }
}
