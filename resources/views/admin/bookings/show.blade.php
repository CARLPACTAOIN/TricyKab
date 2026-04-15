@extends('layouts.stitch')

@section('title', 'Booking Detail')

@php
    $statusBadge = fn (string $status) => match ($status) {
        'COMPLETED' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
        'TRIP_IN_PROGRESS' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300',
        'DRIVER_ASSIGNED', 'DRIVER_ON_THE_WAY', 'DRIVER_ARRIVED' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'SEARCHING_DRIVER', 'CREATED' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'CANCELLED_BY_PASSENGER' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
        'CANCELLED_BY_DRIVER' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
        'CANCELLED_NO_DRIVER' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
        'NO_SHOW_PASSENGER' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        'NO_SHOW_DRIVER' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };
    $rideBadge = $booking->ride_type === 'special'
        ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
        : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300';
    $initials = fn (?string $name) => collect(explode(' ', trim((string) $name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->join('');
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="{{ route('admin.bookings') }}" class="hover:text-primary transition-colors">Bookings & Trips</a>
                <span class="material-icons-outlined text-base">chevron_right</span>
                <span class="text-slate-900 dark:text-white font-medium">{{ $booking->booking_reference }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                Booking {{ $booking->booking_reference }}
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusBadge($booking->status) }}">{{ $booking->status }}</span>
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <button class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 hover:bg-slate-50 transition-colors cursor-not-allowed opacity-60" disabled>
                <span class="material-icons-outlined text-base">admin_panel_settings</span>
                Override Status
            </button>
            <button class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 hover:bg-slate-50 transition-colors cursor-not-allowed opacity-70" disabled>
                <span class="material-icons-outlined text-base">gavel</span>
                Open Dispute
            </button>
            <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm cursor-not-allowed opacity-80" disabled>
                <span class="material-icons-outlined text-base">receipt</span>
                View Receipt
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 dark:text-white mb-6">Booking Lifecycle</h3>
                <div class="relative overflow-x-auto">
                    <div class="min-w-[720px] flex items-center justify-between relative">
                        <div class="absolute top-4 left-0 right-0 h-0.5 bg-emerald-400"></div>
                        @foreach($timeline as $state)
                            <div class="relative flex flex-col items-center z-10">
                                <div class="w-8 h-8 rounded-full {{ $state['done'] ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700' }} flex items-center justify-center">
                                    @if($state['done'])
                                        <span class="material-icons-outlined text-white text-sm">check</span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    @endif
                                </div>
                                <span class="text-[9px] font-bold mt-2 text-slate-500 uppercase whitespace-nowrap">{{ $state['label'] }}</span>
                                <span class="text-[9px] text-slate-400">{{ $state['time'] ?? '—' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <h3 class="font-bold text-slate-800 dark:text-white">Route Summary</h3>
                    <span class="text-xs text-slate-400">
                        {{ $routeSummary['distance_meters'] ? number_format($routeSummary['distance_meters']) . ' m' : 'Distance unavailable' }}
                        · ~{{ $routeSummary['duration_minutes'] }} min
                    </span>
                </div>
                <div
                    class="relative h-64 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800"
                    data-map-context="booking-route"
                    data-map-payload='@json($routeMapPayload)'
                >
                    <div data-map-canvas class="absolute inset-0"></div>
                </div>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-800 p-3 rounded-lg">
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold">Pickup</p>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200 mt-1">{{ $booking->pickup_address }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ number_format((float) $booking->pickup_lat, 4) }}, {{ number_format((float) $booking->pickup_lng, 4) }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800 p-3 rounded-lg">
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold">Destination</p>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200 mt-1">{{ $booking->destination_address }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ number_format((float) $booking->destination_lat, 4) }}, {{ number_format((float) $booking->destination_lng, 4) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-bold text-slate-800 dark:text-white">Dispatch Attempts</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                                <th class="px-6 py-3">Attempt</th>
                                <th class="px-6 py-3">Radius</th>
                                <th class="px-6 py-3">Candidates</th>
                                <th class="px-6 py-3">Winner</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Duration</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($dispatchAttempts as $attempt)
                                <tr class="table-row-hover">
                                    <td class="px-6 py-3 text-sm font-medium">#{{ $attempt['attempt'] }}</td>
                                    <td class="px-6 py-3 text-sm">{{ $attempt['radius'] }}</td>
                                    <td class="px-6 py-3 text-sm">{{ $attempt['candidates'] }}</td>
                                    <td class="px-6 py-3 text-sm font-medium text-primary">{{ $attempt['winner'] }}</td>
                                    <td class="px-6 py-3">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusBadge($attempt['status']) }}">{{ $attempt['status'] }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-xs text-slate-500">{{ $attempt['duration'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-slate-800 dark:text-white">Booking Info</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 uppercase tracking-wider">Reference</span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-white">{{ $booking->booking_reference }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 uppercase tracking-wider">Ride Type</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $rideBadge }}">{{ $booking->ride_type }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 uppercase tracking-wider">Origin Barangay</span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $booking->originBarangay?->name ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 uppercase tracking-wider">Destination Barangay</span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $booking->destinationBarangay?->name ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 uppercase tracking-wider">Est. Distance</span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $routeSummary['distance_meters'] ? number_format($routeSummary['distance_meters']) . ' m' : '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 uppercase tracking-wider">Est. Duration</span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $routeSummary['duration_minutes'] }} min</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 uppercase tracking-wider">Created</span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $booking->created_at?->format('M d, Y h:i A') ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 uppercase tracking-wider">Completed</span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $booking->completed_at?->format('M d, Y h:i A') ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4">Passenger</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                        {{ $initials($booking->passenger?->name) ?: 'NA' }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $booking->passenger?->name ?? 'Unknown passenger' }}</p>
                        <p class="text-xs text-slate-500">{{ $booking->passenger?->phone ?? 'Phone not recorded' }}</p>
                    </div>
                </div>
                <div class="text-xs text-slate-500 space-y-1">
                    <div class="flex items-center gap-1"><span class="material-icons-outlined text-sm">person</span> Passenger account ID: {{ $booking->passenger?->id ?? '—' }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4">Driver</h3>
                @if($booking->driver)
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                            {{ $initials($booking->driver->full_name) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $booking->driver->full_name }}</p>
                            <p class="text-xs text-slate-500">{{ $booking->driver->contact_number ?? 'Phone not recorded' }}</p>
                        </div>
                    </div>
                    <div class="text-xs text-slate-500 space-y-1.5">
                        <div class="flex items-center gap-1"><span class="material-icons-outlined text-sm">groups</span> {{ $booking->driver->toda?->name ?? 'No TODA assigned' }}</div>
                        <div class="flex items-center gap-1"><span class="material-icons-outlined text-sm">two_wheeler</span> Plate: {{ $booking->tricycle?->plate_number ?? $booking->driver->tricycle?->plate_number ?? '—' }}</div>
                        <div class="flex items-center gap-1"><span class="material-icons-outlined text-sm">badge</span> License: {{ $booking->driver->license_number }}</div>
                        <div class="flex items-center gap-1"><span class="material-icons-outlined text-sm">star</span> Rating: {{ number_format((float) $booking->driver->rating, 1) }}/5</div>
                    </div>
                @else
                    <p class="text-sm text-slate-500">No driver is attached to this booking yet.</p>
                @endif
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4">Fare Breakdown</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Estimated Fare</span>
                        <span class="text-slate-700 dark:text-slate-300">₱{{ number_format((float) $booking->fare_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Ride Type</span>
                        <span class="text-slate-700 dark:text-slate-300 uppercase">{{ $booking->ride_type }}</span>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-3 flex justify-between text-sm font-semibold">
                        <span class="text-slate-800 dark:text-white">Final Fare</span>
                        <span class="text-primary text-lg">₱{{ number_format((float) $booking->fare_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4">Payment</h3>
                @if($booking->payment)
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Method</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-1"><span class="material-icons-outlined text-sm">payments</span> {{ strtoupper($booking->payment->method) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Amount</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-300">₱{{ number_format((float) $booking->payment->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Status</span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-600">{{ strtoupper($booking->payment->status) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Paid At</span>
                            <span class="text-slate-700 dark:text-slate-300">{{ $booking->completed_at?->format('M d, Y h:i A') ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Receipt</span>
                            <span class="text-primary font-medium">{{ $routeSummary['receipt_reference'] }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-500">No payment record is attached to this booking.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
