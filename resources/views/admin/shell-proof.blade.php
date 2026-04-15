@extends('layouts.stitch')

@section('title', 'Admin Shell Proof')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Admin Shell Proof</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-500">
                Internal proof screen for <span class="font-semibold text-slate-700 dark:text-slate-300">TK-MOCK-002</span>.
                This page verifies the reusable admin shell: spacing, card styling, filter bar, table shell, status badges, and desktop grid behavior.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                <span class="material-icons-outlined mr-2 text-base">visibility</span>
                Review Shell
            </button>
            <button class="inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary/90">
                <span class="material-icons-outlined mr-2 text-base">photo_camera</span>
                Export Proof
            </button>
        </div>
    </div>

    @include('layouts.components.filter-bar', [
        'dateFilter' => true,
        'todaFilter' => true,
        'statusFilter' => true,
        'typeFilter' => true,
    ])

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @include('layouts.components.kpi-card', [
            'title' => 'Average Passenger Wait Time',
            'value' => '6.8 min',
            'subtitle' => 'Driver assigned at - booking created at',
            'icon' => '<span class="material-icons-outlined text-lg">schedule</span>',
        ])
        @include('layouts.components.kpi-card', [
            'title' => 'Booking-to-Accept Rate',
            'value' => '78%',
            'subtitle' => 'Accepted bookings / searchable bookings',
            'icon' => '<span class="material-icons-outlined text-lg">check_circle</span>',
            'trend' => '4.2%',
            'trendDirection' => 'up',
        ])
        @include('layouts.components.kpi-card', [
            'title' => 'Active Drivers',
            'value' => '142',
            'subtitle' => 'Online and eligible',
            'icon' => '<span class="material-icons-outlined text-lg">hail</span>',
        ])
        @include('layouts.components.kpi-card', [
            'title' => 'Driver Availability Rate',
            'value' => '64%',
            'subtitle' => 'Online eligible time / total service time',
            'icon' => '<span class="material-icons-outlined text-lg">insights</span>',
            'trend' => '1.1%',
            'trendDirection' => 'up',
        ])
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2">
            @component('layouts.components.table-shell')
                @slot('head')
                    <tr>
                        <th class="px-6 py-4">Reference</th>
                        <th class="px-6 py-4">Ride Type</th>
                        <th class="px-6 py-4">Passenger</th>
                        <th class="px-6 py-4">Driver</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Fare</th>
                        <th class="px-6 py-4">Created</th>
                    </tr>
                @endslot
                @slot('body')
                    <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-200">BK-2026-0018</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">SHARED</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">Maria Clara</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">Mariano Ramos</td>
                        <td class="px-6 py-4">@include('layouts.components.status-badge', ['status' => 'COMPLETED'])</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">PHP 45.00</td>
                        <td class="px-6 py-4 text-xs text-slate-500">Today 09:12</td>
                    </tr>
                    <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-200">BK-2026-0019</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">SPECIAL</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">Juan Dela Cruz</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">Carlos Miguel</td>
                        <td class="px-6 py-4">@include('layouts.components.status-badge', ['status' => 'DRIVER_ASSIGNED'])</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">PHP 80.00</td>
                        <td class="px-6 py-4 text-xs text-slate-500">Today 08:51</td>
                    </tr>
                    <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-200">BK-2026-0020</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">SHARED</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">Amy Lee</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">Roberto Cruz</td>
                        <td class="px-6 py-4">@include('layouts.components.status-badge', ['status' => 'SEARCHING_DRIVER'])</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">PHP 35.00</td>
                        <td class="px-6 py-4 text-xs text-slate-500">Today 08:45</td>
                    </tr>
                    <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-200">BK-2026-0021</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">SPECIAL</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">Liza Mae Torres</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">Rogelio Santos</td>
                        <td class="px-6 py-4">@include('layouts.components.status-badge', ['status' => 'DRIVER_ON_THE_WAY'])</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">PHP 95.00</td>
                        <td class="px-6 py-4 text-xs text-slate-500">Today 08:37</td>
                    </tr>
                @endslot
            @endcomponent
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-800">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Detail Shell</p>
                    <h2 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">BK-2026-0019</h2>
                    <p class="mt-1 text-sm text-slate-500">Special ride detail workspace</p>
                </div>
                @include('layouts.components.status-badge', ['status' => 'DRIVER_ASSIGNED'])
            </div>

            <div class="mt-6 space-y-5 text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Passenger</p>
                    <p class="mt-2 font-medium text-slate-800 dark:text-slate-200">Juan Dela Cruz</p>
                    <p class="text-slate-500">Pickup: Poblacion Public Market</p>
                    <p class="text-slate-500">Destination: USM Main Gate</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Driver</p>
                    <p class="mt-2 font-medium text-slate-800 dark:text-slate-200">Carlos Miguel</p>
                    <p class="text-slate-500">Poblacion TODA</p>
                    <p class="text-slate-500">Plate No. KBC-214</p>
                </div>
                <div class="grid grid-cols-2 gap-4 rounded-lg bg-slate-50 p-4 dark:bg-slate-900/50">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Fare</p>
                        <p class="mt-1 font-semibold text-slate-800 dark:text-slate-200">PHP 80.00</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">ETA</p>
                        <p class="mt-1 font-semibold text-slate-800 dark:text-slate-200">4 min</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Created</p>
                        <p class="mt-1 text-slate-600 dark:text-slate-300">08:51 AM</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Payment</p>
                        <p class="mt-1 text-slate-600 dark:text-slate-300">Cash</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 pt-2">
                    <button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary/90">View Full Timeline</button>
                    <button class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">Edit Booking</button>
                    <button class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 transition-colors hover:bg-amber-100">Flag for Review</button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Admin Login Framing</p>
            <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">Auth Surface Preview</h3>
            <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-900/40">
                <div class="mb-4">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Admin access only</p>
                    <p class="mt-1 text-sm text-slate-500">Passenger and driver sign-in uses OTP in the mobile apps.</p>
                </div>
                <div class="space-y-3">
                    <div class="rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-400 dark:border-slate-700 dark:bg-slate-800">Email</div>
                    <div class="rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-400 dark:border-slate-700 dark:bg-slate-800">Password</div>
                    <button class="w-full rounded-lg bg-primary px-4 py-3 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary/90">Sign In</button>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Badge Patterns</p>
            <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">Lifecycle Status Preview</h3>
            <div class="mt-5 flex flex-wrap gap-3">
                @include('layouts.components.status-badge', ['status' => 'CREATED'])
                @include('layouts.components.status-badge', ['status' => 'SEARCHING_DRIVER'])
                @include('layouts.components.status-badge', ['status' => 'DRIVER_ASSIGNED'])
                @include('layouts.components.status-badge', ['status' => 'DRIVER_ON_THE_WAY'])
                @include('layouts.components.status-badge', ['status' => 'DRIVER_ARRIVED'])
                @include('layouts.components.status-badge', ['status' => 'TRIP_IN_PROGRESS'])
                @include('layouts.components.status-badge', ['status' => 'COMPLETED'])
                @include('layouts.components.status-badge', ['status' => 'CANCELLED_BY_PASSENGER'])
                @include('layouts.components.status-badge', ['status' => 'CANCELLED_BY_DRIVER'])
                @include('layouts.components.status-badge', ['status' => 'NO_SHOW_PASSENGER'])
                @include('layouts.components.status-badge', ['status' => 'NO_SHOW_DRIVER'])
                @include('layouts.components.status-badge', ['status' => 'CANCELLED_NO_DRIVER'])
            </div>
        </div>
    </div>
</div>
@endsection
