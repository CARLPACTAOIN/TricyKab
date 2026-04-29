@extends('layouts.stitch')

@section('title', 'Driver Profile — ' . $driver->full_name)

@php
    $statusBadge = fn (string $status) => match ($status) {
        'COMPLETED' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
        'TRIP_IN_PROGRESS' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300',
        'DRIVER_ASSIGNED', 'DRIVER_ON_THE_WAY', 'DRIVER_ARRIVED' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'SEARCHING_DRIVER', 'CREATED' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };
    $disputeStatusBadge = fn (string $status) => match (strtolower($status)) {
        'resolved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'escalated' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
    };
    $disputeTypeBadge = fn (string $type) => match (strtolower($type)) {
        'complaint' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
        'feedback' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
    };
    $ratingStars = (float) $metrics['accumulated_rating'];
@endphp

@section('content')
<div class="space-y-8">

    {{-- Breadcrumb + Actions --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="{{ route('drivers.index') }}" class="hover:text-primary transition-colors">Drivers</a>
                <span class="material-icons-outlined text-xs">chevron_right</span>
                <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $driver->full_name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Driver Profile</h1>
            <p class="text-slate-500 mt-1">Performance overview, transactions, and feedback history.</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm">
                <span class="material-icons-outlined text-slate-400 text-lg">calendar_today</span>
                <select name="range" onchange="this.form.submit()" class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200 text-sm font-medium cursor-pointer">
                    @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month'] as $key => $label)
                        <option value="{{ $key }}" {{ $selectedRange === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('drivers.index') }}" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <span class="material-icons-outlined text-base">arrow_back</span>
                Back to Drivers
            </a>
        </div>
    </div>

    {{-- Profile Hero Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        {{-- Gradient Header --}}
        <div class="h-28 bg-gradient-to-br from-primary/80 via-primary to-indigo-600 relative">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.05%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
        </div>

        {{-- Profile Info --}}
        <div class="px-6 pb-6 -mt-12 relative">
            <div class="flex flex-col md:flex-row md:items-end gap-5">
                {{-- Avatar --}}
                <div class="w-24 h-24 rounded-2xl bg-white dark:bg-slate-800 border-4 border-white dark:border-slate-900 shadow-lg flex items-center justify-center text-primary font-extrabold text-2xl ring-2 ring-primary/20">
                    {{ substr($driver->first_name, 0, 1) }}{{ substr($driver->last_name, 0, 1) }}
                </div>

                {{-- Name + Meta --}}
                <div class="flex-1 pt-2">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $driver->full_name }}</h2>
                        @if($driver->status === 'active')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Active
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-500">Inactive</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <span class="material-icons-outlined text-base text-primary">badge</span>
                            {{ $driver->license_number }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="material-icons-outlined text-base text-primary">groups</span>
                            {{ $driver->toda?->name ?? 'No TODA' }}
                        </span>
                        @if($driver->tricycle)
                            <span class="flex items-center gap-1.5">
                                <span class="material-icons-outlined text-base text-primary">local_taxi</span>
                                {{ $driver->tricycle->plate_number }}
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 text-slate-400 italic">
                                <span class="material-icons-outlined text-base">local_taxi</span>
                                Unassigned
                            </span>
                        @endif
                        @if($driver->contact_number)
                            <span class="flex items-center gap-1.5">
                                <span class="material-icons-outlined text-base text-primary">phone</span>
                                {{ $driver->contact_number }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Star Rating --}}
                <div class="flex flex-col items-center md:items-end gap-1 pt-2">
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($ratingStars))
                                <span class="material-icons-outlined text-lg text-amber-400">star</span>
                            @elseif($i - $ratingStars < 1 && $i - $ratingStars > 0)
                                <span class="material-icons-outlined text-lg text-amber-400">star_half</span>
                            @else
                                <span class="material-icons-outlined text-lg text-slate-300 dark:text-slate-600">star_outline</span>
                            @endif
                        @endfor
                    </div>
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ number_format($ratingStars, 2) }}</span>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Accumulated Rating</span>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Accepted Trips --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm group hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Accepted Trips</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                    <span class="material-icons-outlined text-blue-500 text-lg">check_circle</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $metrics['accepted_trips'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Trips accepted this {{ $selectedRange === 'today' ? 'day' : $selectedRange }}</p>
        </div>

        {{-- Completed Trips --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm group hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Completed</span>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                    <span class="material-icons-outlined text-emerald-500 text-lg">task_alt</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $metrics['completed_trips'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Successfully completed</p>
        </div>

        {{-- Earnings --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm group hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Earnings</span>
                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                    <span class="material-icons-outlined text-primary text-lg">payments</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-primary">&#8369;{{ number_format($metrics['earnings'], 2) }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $metrics['transactions'] }} transaction{{ $metrics['transactions'] !== 1 ? 's' : '' }}</p>
        </div>

        {{-- Avg Wait --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm group hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Avg Wait</span>
                <div class="w-9 h-9 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center">
                    <span class="material-icons-outlined text-orange-500 text-lg">schedule</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($metrics['avg_wait_minutes'], 1) }}<span class="text-sm font-semibold text-slate-400 ml-1">min</span></p>
            <p class="text-xs text-slate-400 mt-1">Average passenger wait</p>
        </div>
    </div>

    {{-- Secondary Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 px-5 py-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center flex-shrink-0">
                <span class="material-icons-outlined text-indigo-500">assignment</span>
            </div>
            <div>
                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $metrics['assigned_bookings'] }}</p>
                <p class="text-xs text-slate-500">Assigned Bookings</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 px-5 py-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center flex-shrink-0">
                <span class="material-icons-outlined text-rose-500">cancel</span>
            </div>
            <div>
                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $metrics['cancelled_or_noshow'] }}</p>
                <p class="text-xs text-slate-500">Cancelled / No-show</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 px-5 py-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center flex-shrink-0">
                <span class="material-icons-outlined text-amber-500">report_problem</span>
            </div>
            <div>
                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $metrics['complaint_count'] }}</p>
                <p class="text-xs text-slate-500">Complaints Filed</p>
            </div>
        </div>
    </div>

    {{-- Recent Transactions Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                    <span class="material-icons-outlined text-primary">receipt_long</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white">Recent Transactions & Bookings</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Showing up to 25 records for the selected period</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4">Booking Ref</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Ride Type</th>
                        <th class="px-6 py-4">Fare</th>
                        <th class="px-6 py-4">Payment</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $booking->booking_reference }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusBadge($booking->status) }}">{{ str_replace('_', ' ', $booking->status) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($booking->ride_type === 'special')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">Special</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">{{ $booking->ride_type }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-700 dark:text-slate-300">&#8369;{{ number_format((float)($booking->fare_amount ?? 0), 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                @if($booking->payment)
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">&#8369;{{ number_format((float)$booking->payment->amount, 2) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">{{ $booking->created_at?->format('M d, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-icons-outlined text-4xl text-slate-300 dark:text-slate-600 mb-2">receipt_long</span>
                                    <p class="text-sm text-slate-500">No records for the selected range.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Feedbacks / Complaints --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                    <span class="material-icons-outlined text-amber-500">feedback</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white">Feedbacks & Complaints</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Dispute records filed within the selected period</p>
                </div>
            </div>
            <span class="text-xs text-slate-400 font-medium">{{ $feedbacks->count() }} record{{ $feedbacks->count() !== 1 ? 's' : '' }}</span>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($feedbacks as $feedback)
                <div class="p-5 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                                <span class="material-icons-outlined text-slate-400 text-base">person</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $disputeTypeBadge($feedback['type']) }}">{{ $feedback['type'] }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $disputeStatusBadge($feedback['status']) }}">{{ ucfirst($feedback['status']) }}</span>
                                </div>
                                <p class="text-sm text-slate-700 dark:text-slate-300 mt-1.5 leading-relaxed">{{ $feedback['description'] }}</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 sm:ml-4">
                            <p class="text-xs text-slate-500">{{ $feedback['at']?->diffForHumans() }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">by {{ $feedback['reported_by'] }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <span class="material-icons-outlined text-4xl text-slate-300 dark:text-slate-600 mb-2">sentiment_satisfied</span>
                        <p class="text-sm text-slate-500">No feedback records for the selected range.</p>
                        <p class="text-xs text-slate-400 mt-1">Looking good!</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
