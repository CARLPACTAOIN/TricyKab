@extends('layouts.stitch')

@section('title', 'SOS Alerts')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">SOS Alerts</h1>
            <p class="text-slate-500 mt-1">Active safety alerts, acknowledgement, and escalation history.</p>
        </div>
    </div>

    {{-- Active Alert Banner --}}
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-5 flex items-start gap-4">
        <div class="p-2 bg-red-100 dark:bg-red-900/40 rounded-lg">
            <span class="material-icons-outlined text-red-600 text-2xl animate-pulse">sos</span>
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-red-700 dark:text-red-400">1 Active SOS Alert</h3>
            <p class="text-sm text-red-600/80 dark:text-red-400/80 mt-1">Passenger Sofia Aguilar triggered SOS near Osias Market. Immediate response required.</p>
            <div class="flex items-center gap-3 mt-3">
                <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 transition-colors shadow-sm">
                    <span class="material-icons-outlined text-base">check_circle</span>
                    Acknowledge
                </button>
                <button class="bg-white dark:bg-slate-800 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 hover:bg-red-50 transition-colors">
                    <span class="material-icons-outlined text-base">phone</span>
                    Call Passenger
                </button>
            </div>
        </div>
        <span class="text-xs text-red-500 whitespace-nowrap">2 min ago</span>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-icons-outlined text-red-500">warning</span>
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Open</p>
            </div>
            <p class="text-2xl font-bold text-red-600">1</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-icons-outlined text-amber-500">visibility</span>
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Acknowledged</p>
            </div>
            <p class="text-2xl font-bold text-amber-600">2</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-icons-outlined text-emerald-500">check_circle</span>
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Closed (7 days)</p>
            </div>
            <p class="text-2xl font-bold text-emerald-600">5</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Passenger</th>
                        <th class="px-6 py-4">Booking</th>
                        <th class="px-6 py-4">Location</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr class="bg-red-50/50 dark:bg-red-900/10 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-slate-500">#8</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold text-xs">SA</div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white">Sofia Aguilar</p>
                                    <p class="text-xs text-slate-500">+63 917 555 1234</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-700 dark:text-slate-300">BK-2026-0028</td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            <div class="flex items-center gap-1"><span class="material-icons-outlined text-sm text-red-500">place</span>Near Osias Market</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">7.1180, 124.8420</div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-600 animate-pulse">OPEN</span></td>
                        <td class="px-6 py-4 text-xs text-slate-500">Today 09:58</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-700">Acknowledge</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-slate-500">#7</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-bold text-xs">ET</div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white">Elena Torres</p>
                                    <p class="text-xs text-slate-500">+63 918 444 5678</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-700 dark:text-slate-300">BK-2026-0022</td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            <div class="flex items-center gap-1"><span class="material-icons-outlined text-sm text-amber-500">place</span>Nongnongan Road</div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-600">ACKNOWLEDGED</span></td>
                        <td class="px-6 py-4 text-xs text-slate-500">Yesterday 18:12</td>
                        <td class="px-6 py-4 text-right">
                            <button class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-emerald-700">Close</button>
                        </td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-slate-500">#6</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-bold text-xs">AR</div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white">Amy Reyes</p>
                                    <p class="text-xs text-slate-500">+63 919 333 4567</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-700 dark:text-slate-300">BK-2026-0020</td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            <div class="flex items-center gap-1"><span class="material-icons-outlined text-sm text-amber-500">place</span>Osias Barangay Hall</div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-600">ACKNOWLEDGED</span></td>
                        <td class="px-6 py-4 text-xs text-slate-500">Apr 13, 14:55</td>
                        <td class="px-6 py-4 text-right">
                            <button class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-emerald-700">Close</button>
                        </td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-slate-500">#5</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">MC</div>
                                <p class="text-sm font-semibold text-slate-800 dark:text-white">Maria Clara</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-700 dark:text-slate-300">BK-2026-0011</td>
                        <td class="px-6 py-4 text-xs text-slate-500">Kabacan National HS</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600">CLOSED</span></td>
                        <td class="px-6 py-4 text-xs text-slate-500">Apr 12, 09:30</td>
                        <td class="px-6 py-4 text-right"><button class="text-sm text-slate-400 hover:underline">View</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
