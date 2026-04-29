@extends('layouts.stitch')

@section('title', 'Analytics')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Analytics</h1>
            <p class="text-slate-500 mt-1">KPI dashboards, breakdowns, and export tools aligned with PRD reporting.</p>
        </div>
        <form method="GET" class="flex items-center gap-3">
            <select name="range" class="px-3 py-2 border rounded-lg text-sm">
                @foreach(['7d' => 'Last 7 days', '30d' => 'Last 30 days', 'month' => 'This month'] as $rangeKey => $rangeLabel)
                    <option value="{{ $rangeKey }}" {{ $selectedRange === $rangeKey ? 'selected' : '' }}>{{ $rangeLabel }}</option>
                @endforeach
            </select>
            <select name="toda_id" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">All TODAs</option>
                @foreach($todas as $toda)
                    <option value="{{ $toda->id }}" {{ (string)$selectedTodaId === (string)$toda->id ? 'selected' : '' }}>{{ $toda->name }}</option>
                @endforeach
            </select>
            <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Apply</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Avg Wait Time</span><span class="material-icons-outlined text-blue-500">schedule</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ number_format($kpis['avg_wait_time'], 1) }} min</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Booking-to-Accept</span><span class="material-icons-outlined text-emerald-500">check_circle</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ $kpis['booking_to_accept_rate'] }}%</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Completion Rate</span><span class="material-icons-outlined text-indigo-500">task_alt</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ $kpis['completion_rate'] }}%</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Active Drivers</span><span class="material-icons-outlined text-orange-500">hail</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ $kpis['active_drivers'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Total Trips (Period)</span><span class="material-icons-outlined text-purple-500">route</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">{{ $kpis['total_trips'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Total Earnings</span><span class="material-icons-outlined text-rose-500">insights</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">PHP {{ number_format($kpis['total_earnings'], 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 dark:text-white">Trips by Ride Type</h3>
                <span class="text-xs text-slate-400">Current filter range</span>
            </div>
            <div id="rideTypeChart" class="w-full h-80"></div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 dark:text-white">Trips per TODA</h3>
                <span class="text-xs text-slate-400">Current filter range</span>
            </div>
            <div id="tripsPerTodaChart" class="w-full h-80"></div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 dark:text-white">Top Drivers</h3>
            <span class="text-xs text-slate-400">Ranked by completed trips and earnings</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-3">Driver</th>
                        <th class="px-6 py-3">TODA</th>
                        <th class="px-6 py-3 text-center">Accepted</th>
                        <th class="px-6 py-3 text-center">Completed</th>
                        <th class="px-6 py-3 text-center">Earnings</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($topDrivers as $entry)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-[10px]">{{ substr($entry['driver']->first_name,0,1) }}</div>
                                <a href="{{ route('drivers.show', $entry['driver']) }}" class="text-sm font-medium text-slate-800 dark:text-white hover:text-primary">{{ $entry['driver']->full_name }}</a>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $entry['driver']->toda?->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm font-semibold text-center">{{ $entry['accepted'] }}</td>
                        <td class="px-6 py-3 text-sm font-semibold text-center">{{ $entry['completed'] }}</td>
                        <td class="px-6 py-3 text-sm font-semibold text-center text-primary">PHP {{ number_format($entry['earnings'], 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-4 text-center text-slate-500">No driver performance data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-slate-800 dark:text-white">Flagged Drivers (Many Complaints)</h3>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($flaggedDrivers as $entry)
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('drivers.show', $entry['driver']) }}" class="font-semibold hover:text-primary">{{ $entry['driver']->full_name }}</a>
                        <span class="text-sm font-semibold text-rose-600">{{ $entry['complaints'] }} active complaints</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $entry['driver']->toda?->name ?? 'No TODA' }}</p>
                </div>
            @empty
                <p class="p-4 text-sm text-slate-500">No flagged drivers in selected period.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const todaSeries = @json(collect($tripsPerToda)->pluck('count')->values());
    const todaCategories = @json(collect($tripsPerToda)->pluck('name')->values());
    const rideTypeSeries = @json([(int) $tripsByRideType['shared'], (int) $tripsByRideType['special']]);

    const tripsPerTodaEl = document.querySelector('#tripsPerTodaChart');
    if (tripsPerTodaEl) {
        new ApexCharts(tripsPerTodaEl, {
            series: [{ name: 'Trips', data: todaSeries }],
            chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: todaCategories,
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: { labels: { formatter: (value) => Math.round(value) } },
            colors: ['#6258ca'],
            grid: { strokeDashArray: 4, borderColor: '#e2e8f0' }
        }).render();
    }

    const rideTypeEl = document.querySelector('#rideTypeChart');
    if (rideTypeEl) {
        const totalTrips = rideTypeSeries.reduce((acc, value) => acc + value, 0);
        new ApexCharts(rideTypeEl, {
            series: rideTypeSeries,
            chart: { type: 'donut', height: 320, fontFamily: 'Inter, sans-serif' },
            labels: ['SHARED', 'SPECIAL'],
            colors: ['#6258ca', '#23b7e5'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: { show: true, label: 'Total Trips', formatter: () => String(totalTrips) }
                        }
                    }
                }
            },
            legend: { position: 'bottom', fontSize: '13px' },
            dataLabels: { enabled: false }
        }).render();
    }
});
</script>
@endsection
