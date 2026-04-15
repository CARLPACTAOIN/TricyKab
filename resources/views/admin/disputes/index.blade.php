@extends('layouts.stitch')

@section('title', 'Disputes')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Disputes</h1>
            <p class="text-slate-500 mt-1">Fare disputes, trip incidents, and resolution workspace.</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Open</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">3</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Under Review</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">2</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Resolved</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">18</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Rejected</p>
            <p class="text-2xl font-bold text-slate-500 mt-1">4</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        {{-- Filter Tabs --}}
        <div class="px-6 pt-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-1 overflow-x-auto pb-0 custom-scrollbar">
                <button class="px-4 py-2.5 text-sm font-semibold text-primary border-b-2 border-primary whitespace-nowrap">All</button>
                <button class="px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-primary border-b-2 border-transparent whitespace-nowrap">Open</button>
                <button class="px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-primary border-b-2 border-transparent whitespace-nowrap">Under Review</button>
                <button class="px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-primary border-b-2 border-transparent whitespace-nowrap">Resolved</button>
                <button class="px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-primary border-b-2 border-transparent whitespace-nowrap">Rejected</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Booking Ref</th>
                        <th class="px-6 py-4">Reported By</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-slate-500">#1</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">BK-2026-0015</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-700">PASSENGER</span>
                                Maria Clara
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">FARE</span></td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 max-w-[200px] truncate">Driver charged ₱60 instead of the ₱45 shown in the app</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-600">OPEN</span></td>
                        <td class="px-6 py-4 text-xs text-slate-500">Today 08:15</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-sm text-primary font-medium hover:underline">Review</button>
                        </td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-slate-500">#2</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">BK-2026-0012</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-700">DRIVER</span>
                                Pedro Alvarez
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">NO_SHOW</span></td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 max-w-[200px] truncate">Passenger was not at pickup location after 5 minutes waiting</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-600">UNDER_REVIEW</span></td>
                        <td class="px-6 py-4 text-xs text-slate-500">Yesterday 16:30</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-sm text-primary font-medium hover:underline">Review</button>
                        </td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-slate-500">#3</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">BK-2026-0009</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-700">PASSENGER</span>
                                Juan Dela Cruz
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">GPS</span></td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 max-w-[200px] truncate">Driver took a longer route than necessary from Osias to USM</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-600">OPEN</span></td>
                        <td class="px-6 py-4 text-xs text-slate-500">Yesterday 14:10</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-sm text-primary font-medium hover:underline">Review</button>
                        </td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-slate-500">#4</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">BK-2026-0006</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-700">PASSENGER</span>
                                Rosa Santos
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700">CONDUCT</span></td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 max-w-[200px] truncate">Driver was rude and refused to follow the destination</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-600">RESOLVED</span></td>
                        <td class="px-6 py-4 text-xs text-slate-500">Apr 12, 10:20</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-sm text-slate-400 font-medium hover:underline">View</button>
                        </td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-slate-500">#5</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">BK-2026-0003</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-700">DRIVER</span>
                                Carlos Miguel
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">FARE</span></td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 max-w-[200px] truncate">Passenger only paid ₱30 for an ₱80 special ride</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600">REJECTED</span></td>
                        <td class="px-6 py-4 text-xs text-slate-500">Apr 10, 09:45</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-sm text-slate-400 font-medium hover:underline">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <p class="text-sm text-slate-500">Showing 1 to 5 of 27 disputes</p>
            <div class="flex items-center gap-1">
                <button class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 dark:border-slate-700 text-slate-500">Previous</button>
                <button class="px-3 py-1.5 rounded-lg text-sm bg-primary text-white">1</button>
                <button class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 dark:border-slate-700 text-slate-500">2</button>
                <button class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 dark:border-slate-700 text-slate-500">Next</button>
            </div>
        </div>
    </div>
</div>
@endsection
