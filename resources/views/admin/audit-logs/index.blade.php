@extends('layouts.stitch')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Logs</h1>
            <p class="text-slate-500 mt-1">Immutable record of admin actions, overrides, and system events.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                <span class="material-icons-outlined text-base">download</span>Export CSV
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap items-center gap-3 shadow-sm">
        <div class="relative">
            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input type="text" placeholder="Search by actor, action, or object..." class="pl-10 pr-4 py-2 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary">
        </div>
        <div class="relative">
            <select class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none min-w-[150px]">
                <option>All Object Types</option>
                <option>BOOKING</option><option>TRIP</option><option>DRIVER</option><option>FARE_RULE</option><option>PAYMENT</option><option>STANDBY_POINT</option>
            </select>
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
        </div>
        <div class="relative">
            <select class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none min-w-[150px]">
                <option>All Actions</option>
                <option>OVERRIDE</option><option>CREATE</option><option>UPDATE</option><option>DELETE</option><option>SUSPEND</option><option>REACTIVATE</option>
            </select>
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
        </div>
        <div class="flex items-center gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm">
            <span class="material-icons-outlined text-slate-400 text-lg">calendar_today</span>
            <span class="text-slate-600 dark:text-slate-300">Last 7 days</span>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">Actor</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Object</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Reason</th>
                        <th class="px-6 py-4 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @php
                    $logs = [
                        ['ts'=>'Today 09:45:12','actor'=>'Admin Sarah','role'=>'LGU','obj'=>'BOOKING #5018','action'=>'BOOKING_OVERRIDE_STATUS','reason'=>'Manually completed trip after driver connectivity loss','highlight'=>true],
                        ['ts'=>'Today 09:30:05','actor'=>'System','role'=>'SYSTEM','obj'=>'DRIVER #2003','action'=>'COMPLIANCE_SUSPENSION','reason'=>'5 cancellations in calendar day','highlight'=>true],
                        ['ts'=>'Today 08:52:18','actor'=>'Admin Sarah','role'=>'LGU','obj'=>'FARE_RULE #12','action'=>'FARE_RULE_UPDATE','reason'=>'Adjusted special ride multiplier from 1.5 to 1.3 per LGU memo','highlight'=>false],
                        ['ts'=>'Today 08:15:44','actor'=>'Admin Ruel','role'=>'TMU','obj'=>'DRIVER #2008','action'=>'DRIVER_REACTIVATE','reason'=>'Suspension period completed, driver cleared by TMU','highlight'=>false],
                        ['ts'=>'Yesterday 17:20:33','actor'=>'System','role'=>'SYSTEM','obj'=>'BOOKING #5027','action'=>'BOOKING_CANCELLED_NO_DRIVER','reason'=>'Dispatch retries exhausted (4 attempts)','highlight'=>false],
                        ['ts'=>'Yesterday 16:45:10','actor'=>'Admin Sarah','role'=>'LGU','obj'=>'STANDBY_POINT #3','action'=>'STANDBY_POINT_CREATE','reason'=>'New waiting area at Nongnongan Church per TODA request','highlight'=>false],
                        ['ts'=>'Yesterday 14:12:55','actor'=>'System','role'=>'SYSTEM','obj'=>'DRIVER #2001','action'=>'COMPLIANCE_WARNING','reason'=>'3 cancellations in calendar day','highlight'=>false],
                        ['ts'=>'Apr 13, 10:20:00','actor'=>'Admin Ruel','role'=>'TMU','obj'=>'DISPUTE #4','action'=>'DISPUTE_RESOLVED','reason'=>'Fare adjustment: driver agreed to refund ₱15 overage','highlight'=>false],
                        ['ts'=>'Apr 13, 09:00:15','actor'=>'Admin Sarah','role'=>'LGU','obj'=>'DRIVER #2010','action'=>'DRIVER_SUSPEND','reason'=>'Repeated conduct complaints from passengers','highlight'=>true],
                        ['ts'=>'Apr 12, 15:30:22','actor'=>'System','role'=>'SYSTEM','obj'=>'PAYMENT #3042','action'=>'PAYMENT_VOIDED','reason'=>'Duplicate payment record detected and voided','highlight'=>false],
                    ];
                    @endphp
                    @foreach($logs as $log)
                    <tr class="table-row-hover transition-colors {{ $log['highlight'] ? 'bg-amber-50/50 dark:bg-amber-900/5' : '' }}">
                        <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap font-mono">{{ $log['ts'] }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-700 dark:text-slate-200 whitespace-nowrap">{{ $log['actor'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log['role'] === 'SYSTEM')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-600">SYSTEM</span>
                            @elseif($log['role'] === 'LGU')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">LGU</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">TMU</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $log['obj'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-xs font-mono font-semibold
                                {{ str_contains($log['action'], 'OVERRIDE') || str_contains($log['action'], 'SUSPEND') ? 'text-rose-600' : '' }}
                                {{ str_contains($log['action'], 'CREATE') || str_contains($log['action'], 'REACTIVATE') || str_contains($log['action'], 'RESOLVED') ? 'text-emerald-600' : '' }}
                                {{ str_contains($log['action'], 'UPDATE') || str_contains($log['action'], 'WARNING') ? 'text-amber-600' : '' }}
                                {{ str_contains($log['action'], 'CANCELLED') || str_contains($log['action'], 'VOIDED') ? 'text-slate-500' : '' }}
                            ">{{ $log['action'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500 max-w-[250px] truncate" title="{{ $log['reason'] }}">{{ $log['reason'] }}</td>
                        <td class="px-6 py-4 text-right">
                            <button class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md text-slate-400 hover:text-primary transition-colors" title="View state diff">
                                <span class="material-icons-outlined text-xl">unfold_more</span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <p class="text-sm text-slate-500">Showing 1 to 10 of 342 entries</p>
            <div class="flex items-center gap-1">
                <button class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 dark:border-slate-700 text-slate-500">Previous</button>
                <button class="px-3 py-1.5 rounded-lg text-sm bg-primary text-white">1</button>
                <button class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 dark:border-slate-700 text-slate-500">2</button>
                <button class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 dark:border-slate-700 text-slate-500">3</button>
                <span class="px-2 text-slate-400">...</span>
                <button class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 dark:border-slate-700 text-slate-500">35</button>
                <button class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 dark:border-slate-700 text-slate-500">Next</button>
            </div>
        </div>
    </div>
</div>
@endsection
