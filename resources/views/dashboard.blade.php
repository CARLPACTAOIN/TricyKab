@extends('layouts.stitch')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Page Header + Filters -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Overview</h1>
            <p class="text-slate-500 mt-1">Operational snapshot aligned with PRD KPIs and filters.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm">
                <span class="material-icons-outlined text-slate-400 text-lg">calendar_today</span>
                <select class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>This month</option>
                    <option>Custom range</option>
                </select>
            </div>
            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm">
                <span class="material-icons-outlined text-slate-400 text-lg">groups</span>
                <select class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200">
                    <option>All TODAs</option>
                    <option>Poblacion TODA</option>
                    <option>Osias TODA</option>
                    <option>Nongnongan TODA</option>
                </select>
            </div>
            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm">
                <span class="material-icons-outlined text-slate-400 text-lg">location_on</span>
                <select class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200">
                    <option>All Barangays</option>
                    <option>Poblacion</option>
                    <option>Osias</option>
                    <option>Nongnongan</option>
                </select>
            </div>
            <button class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                <span class="material-icons-outlined text-base">download</span>
                Export CSV
            </button>
            <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm">
                <span class="material-icons-outlined text-base">picture_as_pdf</span>
                Export PDF
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Avg Wait Time</span>
                <span class="material-icons-outlined text-blue-500">schedule</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">6.8 min</p>
            <p class="text-xs text-slate-500 mt-2">Driver assigned at - booking created at</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Booking-to-Accept</span>
                <span class="material-icons-outlined text-emerald-500">check_circle</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">78%</p>
            <p class="text-xs text-slate-500 mt-2">Accepted bookings / searchable bookings</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Completion Rate</span>
                <span class="material-icons-outlined text-indigo-500">task_alt</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">91%</p>
            <p class="text-xs text-slate-500 mt-2">Completed bookings / assigned bookings</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Active Drivers</span>
                <span class="material-icons-outlined text-orange-500">hail</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">142</p>
            <p class="text-xs text-slate-500 mt-2">Online and eligible</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Trips Today</span>
                <span class="material-icons-outlined text-purple-500">route</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">326</p>
            <p class="text-xs text-slate-500 mt-2">Completed trips (today)</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm uppercase tracking-wider text-slate-500">Driver Availability</span>
                <span class="material-icons-outlined text-rose-500">insights</span>
            </div>
            <p class="text-3xl font-bold mt-3 text-slate-900 dark:text-white">64%</p>
            <p class="text-xs text-slate-500 mt-2">Online eligible time / total service time</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 dark:text-white">Bookings vs Completed</h3>
                <span class="text-xs text-slate-400">Last 7 days</span>
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

    <!-- Heatmaps -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white">Pickup Heatmap</h3>
                <span class="text-xs text-slate-400">Barangay density</span>
            </div>
            <div class="h-64 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-400 text-sm">
                Map placeholder for pickup heatmap
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white">Destination Heatmap</h3>
                <span class="text-xs text-slate-400">Barangay density</span>
            </div>
            <div class="h-64 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-400 text-sm">
                Map placeholder for destination heatmap
            </div>
        </div>
    </div>

    <!-- Bookings & Trips Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 flex items-center justify-between border-b border-slate-50 dark:border-slate-800">
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
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-600 dark:text-slate-300">BK-2026-0018</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">SHARED</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Maria Clara</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Mariano Ramos</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30">COMPLETED</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">₱45.00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">Today 09:12</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-600 dark:text-slate-300">BK-2026-0019</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">SPECIAL</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Juan D.</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Carlos Miguel</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-600 dark:bg-blue-900/30">DRIVER_ASSIGNED</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">₱80.00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">Today 08:51</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-600 dark:text-slate-300">BK-2026-0020</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">SHARED</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Amy Lee</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Roberto Cruz</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-600 dark:bg-amber-900/30">SEARCHING_DRIVER</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">₱35.00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">Today 08:45</td>
                    </tr>
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
                { name: 'Bookings', data: [42, 56, 51, 63, 58, 72, 69] },
                { name: 'Completed', data: [38, 49, 46, 60, 54, 66, 64] }
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
                categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#64748b' } }
            },
            yaxis: { labels: { style: { colors: '#64748b' } } },
            colors: ['#6258ca', '#16a34a'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] }
            },
            grid: { strokeDashArray: 4, borderColor: '#e2e8f0' },
            tooltip: { shared: true }
        };

        const waitTimeOptions = {
            series: [{ name: 'Avg Wait (min)', data: [7.2, 6.8, 6.4, 7.0, 6.1, 5.8, 6.0] }],
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
                categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
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

        const bookingChart = new ApexCharts(document.querySelector('#bookingVolumeChart'), bookingOptions);
        bookingChart.render();

        const waitTimeChart = new ApexCharts(document.querySelector('#waitTimeChart'), waitTimeOptions);
        waitTimeChart.render();
    });
</script>
@endsection
