@extends('layouts.stitch')

@section('title', 'Analytics')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Analytics</h1>
            <p class="text-slate-500 mt-1">KPI dashboards, breakdowns, and export tools aligned with PRD reporting.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm">
                <span class="material-icons-outlined text-slate-400 text-lg">calendar_today</span>
                <select class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200 text-sm">
                    <option>Last 7 days</option><option>Last 30 days</option><option>This month</option><option>Custom</option>
                </select>
            </div>
            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm">
                <span class="material-icons-outlined text-slate-400 text-lg">groups</span>
                <select class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200 text-sm">
                    <option>All TODAs</option><option>Poblacion TODA</option><option>Osias TODA</option><option>Nongnongan TODA</option>
                </select>
            </div>
            <button class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                <span class="material-icons-outlined text-base">download</span>CSV
            </button>
            <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm">
                <span class="material-icons-outlined text-base">picture_as_pdf</span>PDF
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Avg Wait Time</span><span class="material-icons-outlined text-blue-500">schedule</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">5.2 min</p>
            <p class="text-xs text-emerald-500 mt-2 flex items-center gap-1"><span class="material-icons-outlined text-sm">trending_down</span> -1.6 min vs last week</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Booking-to-Accept</span><span class="material-icons-outlined text-emerald-500">check_circle</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">82%</p>
            <p class="text-xs text-emerald-500 mt-2 flex items-center gap-1"><span class="material-icons-outlined text-sm">trending_up</span> +4% vs last week</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Completion Rate</span><span class="material-icons-outlined text-indigo-500">task_alt</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">93%</p>
            <p class="text-xs text-emerald-500 mt-2 flex items-center gap-1"><span class="material-icons-outlined text-sm">trending_up</span> +2% vs last week</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Active Drivers</span><span class="material-icons-outlined text-orange-500">hail</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">156</p>
            <p class="text-xs text-slate-500 mt-2">Online and eligible now</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Total Trips (Period)</span><span class="material-icons-outlined text-purple-500">route</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">2,847</p>
            <p class="text-xs text-emerald-500 mt-2 flex items-center gap-1"><span class="material-icons-outlined text-sm">trending_up</span> +18% vs last week</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between"><span class="text-sm uppercase tracking-wider text-slate-500">Driver Availability Rate</span><span class="material-icons-outlined text-rose-500">insights</span></div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">68%</p>
            <p class="text-xs text-amber-500 mt-2 flex items-center gap-1"><span class="material-icons-outlined text-sm">trending_flat</span> Same as last week</p>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 dark:text-white">Trips per Barangay</h3>
                <span class="text-xs text-slate-400">Last 7 days</span>
            </div>
            <div id="tripsPerBarangayChart" class="w-full h-80"></div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 dark:text-white">Trips by Ride Type</h3>
                <span class="text-xs text-slate-400">Last 7 days</span>
            </div>
            <div id="rideTypeChart" class="w-full h-80"></div>
        </div>
    </div>

    {{-- Driver Utilization Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 dark:text-white">Driver Utilization</h3>
            <span class="text-xs text-slate-400">Top 10 drivers by trip count</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-3">Driver</th>
                        <th class="px-6 py-3">TODA</th>
                        <th class="px-6 py-3 text-center">Trips Today</th>
                        <th class="px-6 py-3 text-center">Accept Rate</th>
                        <th class="px-6 py-3 text-center">Online Hrs</th>
                        <th class="px-6 py-3 text-center">Idle %</th>
                        <th class="px-6 py-3 text-center">Earnings</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @php
                    $drivers = [
                        ['name'=>'Mariano Ramos','toda'=>'Poblacion','trips'=>14,'accept'=>'95%','hours'=>'8.2','idle'=>'18%','earn'=>'₱1,260'],
                        ['name'=>'Pedro Alvarez','toda'=>'Poblacion','trips'=>12,'accept'=>'88%','hours'=>'7.5','idle'=>'22%','earn'=>'₱1,080'],
                        ['name'=>'Roberto Cruz','toda'=>'Osias','trips'=>11,'accept'=>'92%','hours'=>'8.0','idle'=>'20%','earn'=>'₱1,155'],
                        ['name'=>'Carlos Miguel','toda'=>'Poblacion','trips'=>10,'accept'=>'85%','hours'=>'7.0','idle'=>'25%','earn'=>'₱950'],
                        ['name'=>'Antonio Garcia','toda'=>'Nongnongan','trips'=>9,'accept'=>'90%','hours'=>'6.5','idle'=>'30%','earn'=>'₱810'],
                        ['name'=>'Jose Mendoza','toda'=>'Osias','trips'=>8,'accept'=>'78%','hours'=>'6.0','idle'=>'35%','earn'=>'₱720'],
                        ['name'=>'Manuel Santos','toda'=>'Nongnongan','trips'=>7,'accept'=>'82%','hours'=>'5.5','idle'=>'38%','earn'=>'₱630'],
                        ['name'=>'Ricardo Flores','toda'=>'Poblacion','trips'=>7,'accept'=>'91%','hours'=>'5.0','idle'=>'28%','earn'=>'₱665'],
                    ];
                    @endphp
                    @foreach($drivers as $d)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-[10px]">{{ substr($d['name'],0,1) }}</div>
                                <span class="text-sm font-medium text-slate-800 dark:text-white">{{ $d['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $d['toda'] }} TODA</td>
                        <td class="px-6 py-3 text-sm font-semibold text-center">{{ $d['trips'] }}</td>
                        <td class="px-6 py-3 text-sm text-center"><span class="font-semibold {{ intval($d['accept']) >= 90 ? 'text-emerald-600' : (intval($d['accept']) >= 80 ? 'text-amber-600' : 'text-rose-600') }}">{{ $d['accept'] }}</span></td>
                        <td class="px-6 py-3 text-sm text-center text-slate-600 dark:text-slate-400">{{ $d['hours'] }}</td>
                        <td class="px-6 py-3 text-sm text-center text-slate-600 dark:text-slate-400">{{ $d['idle'] }}</td>
                        <td class="px-6 py-3 text-sm font-semibold text-center text-primary">{{ $d['earn'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Trips per Barangay
    new ApexCharts(document.querySelector('#tripsPerBarangayChart'), {
        series: [{ name: 'Trips', data: [520, 380, 290, 185, 145, 120, 95, 72] }],
        chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
        plotOptions: { bar: { borderRadius: 6, columnWidth: '55%', distributed: true } },
        dataLabels: { enabled: false },
        xaxis: {
            categories: ['Poblacion', 'Osias', 'Nongnongan', 'Katidtuan', 'Pedtad', 'Kayaga', 'Ginotan', 'Dagupan'],
            axisBorder: { show: false }, axisTicks: { show: false },
            labels: { style: { colors: '#64748b', fontSize: '11px' } }
        },
        yaxis: { labels: { style: { colors: '#64748b' } } },
        colors: ['#6258ca','#7c6fe0','#9589e8','#aea3f0','#c7bef5','#dbd6fa','#eeeafc','#f5f3fe'],
        grid: { strokeDashArray: 4, borderColor: '#e2e8f0' }
    }).render();

    // Ride Type donut
    new ApexCharts(document.querySelector('#rideTypeChart'), {
        series: [72, 28],
        chart: { type: 'donut', height: 320, fontFamily: 'Inter, sans-serif' },
        labels: ['SHARED', 'SPECIAL'],
        colors: ['#6258ca', '#23b7e5'],
        plotOptions: {
            pie: { donut: { size: '65%', labels: { show: true, name: { show: true }, value: { show: true, formatter: v => v + '%' }, total: { show: true, label: 'Total Trips', formatter: () => '2,847' } } } }
        },
        legend: { position: 'bottom', fontSize: '13px' },
        dataLabels: { enabled: false }
    }).render();
});
</script>
@endsection
