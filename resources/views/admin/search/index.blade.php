@extends('layouts.stitch')

@section('title', 'Search Results')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Search Results</h1>
    <p class="text-slate-500 mt-1">Results for "{{ $q }}"</p>
</div>

@if (strlen($q) < 2)
    <div class="bg-white dark:bg-slate-800 p-8 rounded-xl border border-slate-200 dark:border-slate-700 text-center">
        <span class="material-icons-outlined text-4xl text-slate-400 mb-4">search</span>
        <p class="text-slate-600 dark:text-slate-400">Enter at least 2 characters to search.</p>
    </div>
@else
    <div class="space-y-6">
        <!-- TODAs -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <h2 class="font-semibold text-slate-900 dark:text-white">TODAs</h2>
                @if ($todas->isNotEmpty())
                    <a href="{{ route('todas.index', ['search' => $q]) }}" class="text-primary text-sm font-medium hover:underline">View all</a>
                @endif
            </div>
            <div class="p-6">
                @forelse ($todas as $toda)
                    <a href="{{ route('todas.edit', $toda) }}" class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg px-2 -mx-2 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">{{ substr($toda->name, 0, 1) }}</div>
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $toda->name }}</p>
                            <p class="text-sm text-slate-500">{{ $toda->area_coverage ?? '—' }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-slate-500 text-sm">No TODAs found.</p>
                @endforelse
            </div>
        </div>

        <!-- Drivers -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <h2 class="font-semibold text-slate-900 dark:text-white">Drivers</h2>
                @if ($drivers->isNotEmpty())
                    <a href="{{ route('drivers.index', ['search' => $q]) }}" class="text-primary text-sm font-medium hover:underline">View all</a>
                @endif
            </div>
            <div class="p-6">
                @forelse ($drivers as $driver)
                    <a href="{{ route('drivers.edit', $driver) }}" class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg px-2 -mx-2 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">{{ substr($driver->first_name, 0, 1) }}{{ substr($driver->last_name, 0, 1) }}</div>
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $driver->first_name }} {{ $driver->last_name }}</p>
                            <p class="text-sm text-slate-500">License: {{ $driver->license_number }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-slate-500 text-sm">No drivers found.</p>
                @endforelse
            </div>
        </div>

        <!-- Tricycles -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <h2 class="font-semibold text-slate-900 dark:text-white">Tricycles</h2>
                @if ($tricycles->isNotEmpty())
                    <a href="{{ route('tricycles.index', ['search' => $q]) }}" class="text-primary text-sm font-medium hover:underline">View all</a>
                @endif
            </div>
            <div class="p-6">
                @forelse ($tricycles as $tricycle)
                    <a href="{{ route('tricycles.edit', $tricycle) }}" class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg px-2 -mx-2 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-icons-outlined">electric_rickshaw</span>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $tricycle->body_number }} · {{ $tricycle->plate_number }}</p>
                            <p class="text-sm text-slate-500">{{ $tricycle->toda?->name ?? '—' }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-slate-500 text-sm">No tricycles found.</p>
                @endforelse
            </div>
        </div>
    </div>
@endif
@endsection
