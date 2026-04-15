@extends('layouts.stitch')

@section('title', 'Standby Points')

@php
    $statusBadge = fn (string $status) => $status === 'ACTIVE'
        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Standby Points</h1>
            <p class="text-slate-500 mt-1">LGU/TODA-approved waiting areas and geofence management.</p>
        </div>
        <button type="button" class="bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition-all shadow-md cursor-not-allowed opacity-80" disabled>
            <span class="material-icons-outlined">add</span>
            Add Standby Point
        </button>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800 dark:text-white">Kabacan Standby Point Map</h3>
            <span class="text-xs text-slate-400">{{ $activePointCount }} active points</span>
        </div>
        <div
            class="relative h-72 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800"
            data-map-context="standby-points"
            data-map-payload='@json($mapPayload)'
        >
            <div data-map-canvas class="absolute inset-0"></div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.standby-points') }}" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap items-center gap-3 shadow-sm">
        <div class="relative">
            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search standby points..."
                class="pl-10 pr-4 py-2 w-64 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary"
            >
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
            <select name="status" class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none min-w-[140px]">
                <option value="">All Status</option>
                <option value="ACTIVE" @selected($selectedStatus === 'ACTIVE')>Active</option>
                <option value="INACTIVE" @selected($selectedStatus === 'INACTIVE')>Inactive</option>
            </select>
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
        </div>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Apply Filters</button>
        <a href="{{ route('admin.standby-points') }}" class="text-sm text-slate-500 hover:text-primary transition-colors">Reset</a>
    </form>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">TODA</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Barangay</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Coordinates</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Radius</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($standbyPoints as $point)
                        <tr class="table-row-hover transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons-outlined text-primary text-lg">place</span>
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $point->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $point->toda?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $point->barangay?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-500 font-mono">{{ number_format((float) $point->latitude, 4) }}, {{ number_format((float) $point->longitude, 4) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ number_format($point->radius_meters) }}m</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">{{ number_format((float) $point->priority_weight, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusBadge($point->status) }}">{{ ucfirst(strtolower($point->status)) }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-amber-600 transition-colors cursor-not-allowed" disabled><span class="material-icons-outlined text-xl">edit</span></button>
                                    <button type="button" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-red-600 transition-colors cursor-not-allowed" disabled><span class="material-icons-outlined text-xl">delete_outline</span></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-slate-500">No standby points matched the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
