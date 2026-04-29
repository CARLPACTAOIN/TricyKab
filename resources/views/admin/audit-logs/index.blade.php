@extends('layouts.stitch')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Logs</h1>
            <p class="text-slate-500 mt-1">Immutable record of admin actions, overrides, and system events.</p>
        </div>
    </div>

    <form method="GET" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap items-center gap-3 shadow-sm">
        <div class="relative">
            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by actor, action, or object..." class="pl-10 pr-4 py-2 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary">
        </div>
        <div class="relative">
            <select name="object_type" class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none min-w-[150px]">
                <option value="">All Object Types</option>
                @foreach($objectTypes as $item)
                    <option value="{{ $item }}" {{ $objectType === $item ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </select>
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
        </div>
        <div class="relative">
            <select name="action" class="pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none min-w-[150px]">
                <option value="">All Actions</option>
                @foreach($actions as $item)
                    <option value="{{ $item }}" {{ $action === $item ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </select>
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
        </div>
        <select name="range" class="px-3 py-2 border rounded-lg text-sm">
            @foreach(['today' => 'Today', '7d' => 'Last 7 days', '30d' => 'Last 30 days', 'all' => 'All'] as $rangeKey => $rangeLabel)
                <option value="{{ $rangeKey }}" {{ $range === $rangeKey ? 'selected' : '' }}>{{ $rangeLabel }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-primary text-white rounded-lg text-sm">Apply</button>
        <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="px-4 py-2 border rounded-lg text-sm">Export CSV</a>
    </form>

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
                    @forelse($logs as $log)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap font-mono">{{ $log->created_at?->format('M d, Y H:i:s') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-700 dark:text-slate-200 whitespace-nowrap">{{ $log->actor_name ?? $log->actor?->name ?? 'System' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-600">{{ $log->actor_type }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $log->object_type }} #{{ $log->object_id ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap"><span class="text-xs font-mono font-semibold">{{ $log->action }}</span></td>
                        <td class="px-6 py-4 text-xs text-slate-500 max-w-[250px] truncate" title="{{ $log->reason }}">{{ $log->reason }}</td>
                        <td class="px-6 py-4 text-right text-xs text-slate-400">immutable</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-slate-500">No audit logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
