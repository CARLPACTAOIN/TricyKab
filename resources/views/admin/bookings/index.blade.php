@extends('layouts.stitch')

@section('title', 'Bookings & Trips')

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
    $rideBadge = fn (string $rideType) => $rideType === 'special'
        ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
        : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Bookings & Trips</h1>
            <p class="text-slate-500 mt-1">Operational booking visibility, lifecycle tracking, and manual overrides.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 cursor-not-allowed opacity-70" disabled>
                <span class="material-icons-outlined text-base">download</span>
                Export CSV
            </button>
            <button type="button" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm cursor-not-allowed opacity-80" disabled>
                <span class="material-icons-outlined text-base">picture_as_pdf</span>
                Export PDF
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Bookings</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $summary['total'] }}</p>
            <p class="text-xs text-slate-500 mt-1">After current search and top-level filters</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Active Now</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $summary['active'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Created, searching, assigned, or in progress</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Completed Today</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $summary['completed_today'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Completed bookings with today’s timestamp</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Cancelled / No-Show</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ $summary['cancelled'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Cancellation and no-show outcomes combined</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 pt-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-1 overflow-x-auto pb-0 custom-scrollbar">
                @foreach($statusTabs as $tabKey => $tab)
                    <a
                        href="{{ route('admin.bookings', array_merge(request()->query(), ['tab' => $tabKey, 'page' => null])) }}"
                        class="px-4 py-2.5 text-sm whitespace-nowrap border-b-2 transition-colors {{ $selectedTab === $tabKey ? 'font-semibold text-primary border-primary' : 'font-medium text-slate-500 hover:text-primary border-transparent' }}"
                    >
                        {{ $tab['label'] }} <span class="text-slate-400">({{ $tab['count'] }})</span>
                    </a>
                @endforeach
            </div>
        </div>

        <form method="GET" action="{{ route('admin.bookings') }}" class="px-6 py-4 flex flex-wrap items-center gap-3 border-b border-slate-100 dark:border-slate-800">
            <input type="hidden" name="tab" value="{{ $selectedTab }}">
            <div class="relative">
                <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search reference, passenger, driver..."
                    class="pl-10 pr-4 py-2 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary"
                >
            </div>
            <div class="relative">
                <select name="ride_type" class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none min-w-[140px]">
                    <option value="">All Ride Types</option>
                    <option value="shared" @selected($selectedRideType === 'shared')>SHARED</option>
                    <option value="special" @selected($selectedRideType === 'special')>SPECIAL</option>
                </select>
                <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>
            <div class="relative">
                <select name="toda_id" class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none min-w-[180px]">
                    <option value="">All TODAs</option>
                    @foreach($todas as $toda)
                        <option value="{{ $toda->id }}" @selected($selectedTodaId === $toda->id)>{{ $toda->name }}</option>
                    @endforeach
                </select>
                <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>
            <div class="relative">
                <select name="date_scope" class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none min-w-[140px]">
                    <option value="today" @selected($selectedDateScope === 'today')>Today</option>
                    <option value="7d" @selected($selectedDateScope === '7d')>Last 7 days</option>
                    <option value="all" @selected($selectedDateScope === 'all')>All time</option>
                </select>
                <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Apply Filters</button>
            <a href="{{ route('admin.bookings') }}" class="text-sm text-slate-500 hover:text-primary transition-colors">Reset</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4">Reference</th>
                        <th class="px-6 py-4">Ride</th>
                        <th class="px-6 py-4">Passenger</th>
                        <th class="px-6 py-4">Driver</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Pickup</th>
                        <th class="px-6 py-4">Destination</th>
                        <th class="px-6 py-4">Fare</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($bookings as $booking)
                        <tr class="table-row-hover transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $booking->booking_reference }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $rideBadge($booking->ride_type) }}">{{ $booking->ride_type }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $booking->passenger?->name ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $booking->driver?->full_name ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusBadge($booking->status) }}">{{ $booking->status }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">{{ $booking->pickup_address }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">{{ $booking->destination_address }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $booking->isCancelled() ? 'text-slate-400' : 'text-slate-700 dark:text-slate-200' }}">₱{{ number_format((float) $booking->fare_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">{{ $booking->created_at?->format('M d, h:i A') ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.bookings.show', $booking->booking_reference) }}" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-primary transition-colors inline-block" title="View Detail">
                                    <span class="material-icons-outlined text-xl">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-sm text-slate-500">No bookings matched the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <p class="text-sm text-slate-500">
                Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} results
            </p>
            <div>
                {{ $bookings->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
