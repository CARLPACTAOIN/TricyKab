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
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                    <span class="material-icons-outlined text-amber-500">emoji_events</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white">Top Drivers</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Ranked by completed trips & earnings · Top 10</p>
                </div>
            </div>
            <span class="text-xs text-slate-400 font-medium">{{ $topDrivers->count() }} driver{{ $topDrivers->count() !== 1 ? 's' : '' }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-4 py-4 text-center w-12">#</th>
                        <th class="px-4 py-4">Driver</th>
                        <th class="px-4 py-4">TODA</th>
                        <th class="px-4 py-4">Tricycle</th>
                        <th class="px-4 py-4 text-center">Rating</th>
                        <th class="px-4 py-4 text-center">Accepted</th>
                        <th class="px-4 py-4 text-center">Completed</th>
                        <th class="px-4 py-4 text-center">Cancelled</th>
                        <th class="px-4 py-4 text-center">Completion</th>
                        <th class="px-4 py-4 text-center">Avg Wait</th>
                        <th class="px-4 py-4 text-right">Earnings</th>
                        <th class="px-4 py-4 text-right w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($topDrivers as $index => $entry)
                    @php
                        $rank = $index + 1;
                        $driver = $entry['driver'];
                        $rating = (float) $driver->rating;
                        $completionPct = $entry['completion_rate'];
                        $completionColor = $completionPct >= 80 ? 'bg-emerald-500' : ($completionPct >= 50 ? 'bg-amber-400' : 'bg-rose-400');
                    @endphp
                    <tr class="table-row-hover transition-colors group">
                        {{-- Rank --}}
                        <td class="px-4 py-3.5 text-center">
                            @if($rank === 1)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 text-xs font-extrabold" title="1st Place">
                                    <span class="material-icons-outlined text-base">emoji_events</span>
                                </span>
                            @elseif($rank === 2)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-extrabold" title="2nd Place">2</span>
                            @elseif($rank === 3)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 text-xs font-extrabold" title="3rd Place">3</span>
                            @else
                                <span class="text-sm font-semibold text-slate-400">{{ $rank }}</span>
                            @endif
                        </td>

                        {{-- Driver Name + Avatar --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="relative flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-[10px]">
                                        {{ substr($driver->first_name, 0, 1) }}{{ substr($driver->last_name, 0, 1) }}
                                    </div>
                                    @if($driver->status === 'active')
                                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-slate-900"></span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('drivers.show', $driver) }}" class="text-sm font-semibold text-slate-800 dark:text-white hover:text-primary transition-colors truncate block">{{ $driver->full_name }}</a>
                                    <p class="text-[10px] text-slate-400 font-mono">{{ $driver->license_number }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- TODA --}}
                        <td class="px-4 py-3.5 text-sm text-slate-600 dark:text-slate-400">{{ $driver->toda?->name ?? '—' }}</td>

                        {{-- Tricycle Plate --}}
                        <td class="px-4 py-3.5">
                            @if($driver->tricycle)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    <span class="material-icons-outlined text-[10px]">local_taxi</span>
                                    {{ $driver->tricycle->plate_number }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400 italic">—</span>
                            @endif
                        </td>

                        {{-- Rating Stars --}}
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-0.5">
                                <span class="material-icons-outlined text-amber-400 text-sm">star</span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ number_format($rating, 1) }}</span>
                            </div>
                        </td>

                        {{-- Accepted --}}
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $entry['accepted'] }}</span>
                        </td>

                        {{-- Completed --}}
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $entry['completed'] }}</span>
                        </td>

                        {{-- Cancelled --}}
                        <td class="px-4 py-3.5 text-center">
                            @if($entry['cancelled'] > 0)
                                <span class="text-sm font-semibold text-rose-500">{{ $entry['cancelled'] }}</span>
                            @else
                                <span class="text-sm text-slate-400">0</span>
                            @endif
                        </td>

                        {{-- Completion Rate Bar --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2 justify-center">
                                <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="{{ $completionColor }} h-full rounded-full transition-all" style="width: {{ min($completionPct, 100) }}%"></div>
                                </div>
                                <span class="text-xs font-bold {{ $completionPct >= 80 ? 'text-emerald-600 dark:text-emerald-400' : ($completionPct >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-500') }}">{{ $completionPct }}%</span>
                            </div>
                        </td>

                        {{-- Avg Wait --}}
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm text-slate-600 dark:text-slate-400">{{ $entry['avg_wait'] }}<span class="text-[10px] text-slate-400 ml-0.5">min</span></span>
                        </td>

                        {{-- Earnings --}}
                        <td class="px-4 py-3.5 text-right">
                            <span class="text-sm font-bold text-primary">&#8369;{{ number_format($entry['earnings'], 2) }}</span>
                        </td>

                        {{-- Action --}}
                        <td class="px-4 py-3.5 text-right">
                            <a href="{{ route('drivers.show', $driver) }}" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-primary transition-colors opacity-0 group-hover:opacity-100 inline-flex" title="View Profile">
                                <span class="material-icons-outlined text-lg">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-icons-outlined text-4xl text-slate-300 dark:text-slate-600 mb-2">leaderboard</span>
                                <p class="text-sm text-slate-500">No driver performance data for the selected period.</p>
                            </div>
                        </td>
                    </tr>
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
