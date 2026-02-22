@extends('layouts.stitch')

@section('title', 'TricyKab Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Overview</h1>
        <p class="text-slate-500 mt-1">Welcome back, monitoring system health.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Dispatch -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <span class="material-icons-outlined text-blue-600">navigation</span>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600">+12%</span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium uppercase tracking-wider">Total Dispatch</h3>
            <p class="text-2xl font-bold mt-1 text-slate-900 dark:text-white">1,284</p>
            <div class="mt-4 flex items-end gap-1 h-8">
                <div class="w-1 bg-blue-200 h-2 rounded-full"></div>
                <div class="w-1 bg-blue-200 h-4 rounded-full"></div>
                <div class="w-1 bg-blue-200 h-6 rounded-full"></div>
                <div class="w-1 bg-blue-600 h-8 rounded-full"></div>
                <div class="w-1 bg-blue-400 h-5 rounded-full"></div>
                <div class="w-1 bg-blue-600 h-7 rounded-full"></div>
            </div>
        </div>

        <!-- Card 2: Active Drivers -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                    <span class="material-icons-outlined text-success">hail</span>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-success">+5%</span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium uppercase tracking-wider">Active Drivers</h3>
            <p class="text-2xl font-bold mt-1 text-slate-900 dark:text-white">156</p>
            <div class="mt-4 flex items-end gap-1 h-8">
                <div class="w-1 bg-emerald-200 h-4 rounded-full"></div>
                <div class="w-1 bg-emerald-200 h-3 rounded-full"></div>
                <div class="w-1 bg-emerald-600 h-8 rounded-full"></div>
                <div class="w-1 bg-emerald-400 h-6 rounded-full"></div>
                <div class="w-1 bg-emerald-200 h-4 rounded-full"></div>
                <div class="w-1 bg-emerald-600 h-7 rounded-full"></div>
            </div>
        </div>

        <!-- Card 3: Registered Tricycles -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-orange-50 dark:bg-orange-900/30 rounded-lg">
                    <span class="material-icons-outlined text-warning">minor_crash</span>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-orange-50 text-warning">+2%</span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium uppercase tracking-wider">Registered</h3>
            <p class="text-2xl font-bold mt-1 text-slate-900 dark:text-white">432</p>
            <div class="mt-4 flex items-end gap-1 h-8">
                <div class="w-1 bg-orange-200 h-2 rounded-full"></div>
                <div class="w-1 bg-orange-200 h-5 rounded-full"></div>
                <div class="w-1 bg-orange-400 h-4 rounded-full"></div>
                <div class="w-1 bg-orange-200 h-6 rounded-full"></div>
                <div class="w-1 bg-orange-600 h-8 rounded-full"></div>
                <div class="w-1 bg-orange-400 h-7 rounded-full"></div>
            </div>
        </div>

        <!-- Card 4: Total Income -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-primary/10 rounded-lg">
                    <span class="material-icons-outlined text-primary">account_balance_wallet</span>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-primary/10 text-primary">+18%</span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium uppercase tracking-wider">Total Income</h3>
            <p class="text-2xl font-bold mt-1 text-slate-900 dark:text-white">₱45,200</p>
            <div class="mt-4 flex items-end gap-1 h-8">
                <div class="w-1 bg-primary/20 h-3 rounded-full"></div>
                <div class="w-1 bg-primary/20 h-4 rounded-full"></div>
                <div class="w-1 bg-primary/50 h-6 rounded-full"></div>
                <div class="w-1 bg-primary/50 h-5 rounded-full"></div>
                <div class="w-1 bg-primary h-8 rounded-full"></div>
                <div class="w-1 bg-primary h-7 rounded-full"></div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Weekly Dispatch Bar Chart -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 dark:text-white">Weekly Dispatch Overview</h3>
                <span class="material-icons-outlined text-slate-400 text-lg cursor-pointer">more_horiz</span>
            </div>
            <div id="dispatchChart" class="w-full h-80"></div>
        </div>

        <!-- Revenue Analytics Line Chart -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-slate-800 dark:text-white">Revenue Analytics</h3>
                <div class="flex gap-2">
                    <span class="w-3 h-3 rounded-full bg-primary"></span>
                    <span class="text-[10px] font-medium text-slate-500">Current Month</span>
                </div>
            </div>
            <div id="revenueChart" class="w-full h-80"></div>
        </div>
    </div>

    <!-- Recent Dispatch Activity -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 flex items-center justify-between border-b border-slate-50 dark:border-slate-800">
            <h3 class="font-bold text-slate-800 dark:text-white">Recent Dispatch Activity</h3>
            <button class="text-primary text-xs font-semibold hover:underline">View All</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4">Transaction ID</th>
                        <th class="px-6 py-4">Passenger</th>
                        <th class="px-6 py-4">Driver</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Fare</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <!-- Row 1 -->
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-600 dark:text-slate-300">#TK-9821</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-slate-100 rounded-full flex items-center justify-center text-[10px] font-bold text-slate-600">JD</div>
                                <span class="text-sm font-medium">Jane Doe</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Mariano Ramos</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30">COMPLETED</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">₱45.00</td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-600 dark:text-slate-300">#TK-9822</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-slate-100 rounded-full flex items-center justify-center text-[10px] font-bold text-slate-600">BS</div>
                                <span class="text-sm font-medium">Bryan Smith</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Carlos Miguel</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-100 text-orange-600 dark:bg-orange-900/30">PENDING</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">₱60.00</td>
                    </tr>
                    <!-- Row 3 -->
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-600 dark:text-slate-300">#TK-9823</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-slate-100 rounded-full flex items-center justify-center text-[10px] font-bold text-slate-600">AL</div>
                                <span class="text-sm font-medium">Amy Lee</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Roberto Cruz</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30">COMPLETED</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">₱35.00</td>
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
        // Dispatch Chart
        var dispatchOptions = {
            series: [{
                name: 'Dispatch Count',
                data: [30, 40, 35, 50, 49, 60, 70, 91, 125, 45, 60, 40]
            }],
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '40%',
                }
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#64748b' } }
            },
            yaxis: {
                labels: { style: { colors: '#64748b' } }
            },
            fill: {
                opacity: 1,
                colors: ['#6258ca']
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " trips"
                    }
                }
            },
            grid: {
                strokeDashArray: 4,
                borderColor: '#e2e8f0',
                padding: { top: 0, right: 0, bottom: 0, left: 10 }
            }
        };

        var dispatchChart = new ApexCharts(document.querySelector("#dispatchChart"), dispatchOptions);
        dispatchChart.render();

        // Revenue Chart
        var revenueOptions = {
            series: [{
                name: 'Revenue',
                data: [1500, 2300, 3200, 4500, 3800, 5200, 6100]
            }],
            chart: {
                height: 320,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: ['#6258ca']
            },
            xaxis: {
                categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#64748b' } }
            },
            yaxis: {
                labels: { style: { colors: '#64748b' } }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100],
                    colorStops: [
                        { offset: 0, color: '#6258ca', opacity: 0.4 },
                        { offset: 100, color: '#6258ca', opacity: 0.0 }
                    ]
                }
            },
            grid: {
                strokeDashArray: 4,
                borderColor: '#e2e8f0',
                padding: { top: 0, right: 0, bottom: 0, left: 10 }
            },
             tooltip: {
                y: {
                    formatter: function (val) {
                        return "₱ " + val
                    }
                }
            },
            markers: {
                size: 5,
                colors: ['#6258ca'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 7 }
            }
        };

        var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
        revenueChart.render();
    });
</script>
@endsection
