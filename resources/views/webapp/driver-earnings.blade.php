@extends('layouts.webapp')

@section('title', 'Earnings - TricyKab Driver')

@section('content')
<div class="min-h-screen bg-background-light pb-24">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('driver.app') }}" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50" aria-label="Back to dashboard">
                    <span class="material-icons-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight text-slate-900">Earnings</h1>
                    <p class="text-xs text-slate-500">Shift summary (demo)</p>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Today</p>
                <p class="mt-2 text-2xl font-extrabold text-primary">₱ 1,520.00</p>
                <p class="mt-1 text-xs text-slate-500">14 trips</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">This week</p>
                <p class="mt-2 text-2xl font-extrabold text-secondary">₱ 8,940.00</p>
                <p class="mt-1 text-xs text-slate-500">82 trips</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cash recorded</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">100%</p>
                <p class="mt-1 text-xs text-slate-500">MVP: cash only</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="font-bold text-slate-900">Recent trips</h2>
                <p class="text-xs text-slate-500">Static sample — connect API for live data</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Ref</th>
                            <th class="px-6 py-3">Route</th>
                            <th class="px-6 py-3">Fare</th>
                            <th class="px-6 py-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-3 font-mono text-xs">BKG-2026-000044</td>
                            <td class="px-6 py-3">Market → USM</td>
                            <td class="px-6 py-3 font-semibold text-primary">₱35.00</td>
                            <td class="px-6 py-3 text-slate-500">Today 09:12</td>
                        </tr>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-3 font-mono text-xs">BKG-2026-000043</td>
                            <td class="px-6 py-3">Terminal → Hospital</td>
                            <td class="px-6 py-3 font-semibold text-primary">₱50.00</td>
                            <td class="px-6 py-3 text-slate-500">Today 08:40</td>
                        </tr>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-3 font-mono text-xs">BKG-2026-000041</td>
                            <td class="px-6 py-3">USM → Poblacion</td>
                            <td class="px-6 py-3 font-semibold text-primary">₱40.00</td>
                            <td class="px-6 py-3 text-slate-500">Yesterday</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    @include('webapp.partials.driver-nav', ['activeNav' => $activeNav ?? 'earnings'])
</div>
@endsection
