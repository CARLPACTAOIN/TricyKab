@extends('layouts.stitch')

@section('title', 'Dashboard')

@php
    $statusBadge = fn (string $status) => match ($status) {
        'COMPLETED' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
        'TRIP_IN_PROGRESS' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300',
        'DRIVER_ASSIGNED', 'DRIVER_ON_THE_WAY', 'DRIVER_ARRIVED' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'SEARCHING_DRIVER', 'CREATED' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };
    $rideBadge = fn (string $rideType) => $rideType === 'special'
        ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
        : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300';
    $pickupMapPayload = [
        'kind' => 'pickup',
        'center' => ['lat' => 7.114, 'lng' => 124.836],
        'zoom' => 13,
        'points' => $pickupHeatmapPoints,
    ];
    $destinationMapPayload = [
        'kind' => 'destination',
        'center' => ['lat' => 7.114, 'lng' => 124.836],
        'zoom' => 13,
        'points' => $destinationHeatmapPoints,
    ];
@endphp

@section('content')
<div class="space-y-8">
    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Overview</h1>
            <p class="text-slate-500 mt-1">Operational snapshot aligned with PRD KPIs and location-driven admin visibility.</p>
        </div>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm">
                <span class="material-icons-outlined text-slate-400 text-lg">calendar_today</span>
                <select name="range" onchange="this.form.submit()" class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200">
                    <option value="7d" @selected($selectedRange === '7d')>Last 7 days</option>
                    <option value="30d" @selected($selectedRange === '30d')>Last 30 days</option>
                    <option value="month" @selected($selectedRange === 'month')>This month</option>
                </select>
            </div>
            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm">
                <span class="material-icons-outlined text-slate-400 text-lg">groups</span>
                <select name="toda_id" onchange="this.form.submit()" class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200">
                    <option value="">All TODAs</option>
                    @foreach($todas as $toda)
                        <option value="{{ $toda->id }}" @selected($selectedTodaId === $toda->id)>{{ $toda->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm">
                <span class="material-icons-outlined text-slate-400 text-lg">location_on</span>
                <select name="barangay_id" onchange="this.form.submit()" class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay->id }}" @selected($selectedBarangayId === $barangay->id)>{{ $barangay->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 cursor-not-allowed opacity-70" disabled>
                <span class="material-icons-outlined text-base">download</span>
                Export CSV
            </button>
            <button type="button" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm cursor-not-allowed opacity-80" disabled>
                <span class="material-icons-outlined text-base">picture_as_pdf</span>
                Export PDF
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Avg Wait Time</span>
                <span class="material-icons-outlined text-blue-500">schedule</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ number_format($avgWaitMinutes, 1) }} min</p>
            <p class="text-xs text-slate-500 mt-2">Driver assigned at minus booking created at</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Booking-to-Accept</span>
                <span class="material-icons-outlined text-emerald-500">check_circle</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ $bookingToAcceptRate }}%</p>
            <p class="text-xs text-slate-500 mt-2">Accepted bookings / searchable bookings</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Completion Rate</span>
                <span class="material-icons-outlined text-indigo-500">task_alt</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ $completionRate }}%</p>
            <p class="text-xs text-slate-500 mt-2">Completed bookings / assigned bookings</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Active Drivers</span>
                <span class="material-icons-outlined text-orange-500">hail</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ $activeDrivers }}</p>
            <p class="text-xs text-slate-500 mt-2">Drivers marked active in the current fleet roster</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Trips Today</span>
                <span class="material-icons-outlined text-purple-500">route</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ $tripsToday }}</p>
            <p class="text-xs text-slate-500 mt-2">Completed trips within today’s service window</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Driver Availability</span>
                <span class="material-icons-outlined text-rose-500">insights</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ $driverAvailabilityRate }}%</p>
            <p class="text-xs text-slate-500 mt-2">Active drivers / total registered drivers</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 dark:text-white">Bookings vs Completed</h3>
                <span class="text-xs text-slate-400">Filtered window</span>
            </div>
            <div id="bookingVolumeChart" class="w-full h-80"></div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 dark:text-white">Average Wait Time Trend</h3>
                <span class="text-xs text-slate-400">Minutes</span>
            </div>
            <div id="waitTimeChart" class="w-full h-80"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white">Pickup Heatmap</h3>
                <span class="text-xs text-slate-400">Filtered booking origins</span>
            </div>
            <div
                class="relative h-64 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900"
                data-map-root
                data-map-context="dashboard-heatmap"
                data-map-payload='@json($pickupMapPayload)'
            >
                <div data-map-canvas class="h-full w-full min-h-[16rem]"></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white">Destination Heatmap</h3>
                <span class="text-xs text-slate-400">Filtered booking destinations</span>
            </div>
            <div
                class="relative h-64 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900"
                data-map-root
                data-map-context="dashboard-heatmap"
                data-map-payload='@json($destinationMapPayload)'
            >
                <div data-map-canvas class="h-full w-full min-h-[16rem]"></div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-slate-800 dark:text-white">Latest Bookings & Trips</h3>
            <a href="{{ route('admin.bookings') }}" class="text-primary text-xs font-semibold hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4">Reference</th>
                        <th class="px-6 py-4">Ride Type</th>
                        <th class="px-6 py-4">Passenger</th>
                        <th class="px-6 py-4">Driver</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Fare</th>
                        <th class="px-6 py-4">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($latestBookings as $booking)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-700 dark:text-slate-200">
                                <a href="{{ route('admin.bookings.show', $booking->booking_reference) }}" class="hover:text-primary transition-colors">
                                    {{ $booking->booking_reference }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $rideBadge($booking->ride_type) }}">
                                    {{ $booking->ride_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $booking->passenger?->name ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $booking->driver?->full_name ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusBadge($booking->status) }}">{{ $booking->status }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">₱{{ number_format((float) $booking->fare_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">{{ $booking->created_at?->diffForHumans() ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">No bookings found for the current dashboard filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="module">
    document.addEventListener('DOMContentLoaded', function () {
        const bookingOptions = {
            series: [
                { name: 'Bookings', data: @json($bookingChart['bookings']) },
                { name: 'Completed', data: @json($bookingChart['completed']) }
            ],
            chart: {
                type: 'area',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: @json($bookingChart['categories']),
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#64748b' } }
            },
            yaxis: { labels: { style: { colors: '#64748b' } } },
            colors: ['#2563eb', '#16a34a'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] }
            },
            grid: { strokeDashArray: 4, borderColor: '#e2e8f0' },
            tooltip: { shared: true }
        };

        const waitTimeOptions = {
            series: [{ name: 'Avg Wait (min)', data: @json($waitTimeChart['values']) }],
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            plotOptions: {
                bar: { borderRadius: 4, columnWidth: '50%' }
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: @json($waitTimeChart['categories']),
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#64748b' } }
            },
            yaxis: { labels: { style: { colors: '#64748b' } } },
            colors: ['#f97316'],
            grid: { strokeDashArray: 4, borderColor: '#e2e8f0' },
            tooltip: {
                y: { formatter: function (val) { return val + ' min'; } }
            }
        };

        new ApexCharts(document.querySelector('#bookingVolumeChart'), bookingOptions).render();
        new ApexCharts(document.querySelector('#waitTimeChart'), waitTimeOptions).render();
    });
</script>
@endsection
