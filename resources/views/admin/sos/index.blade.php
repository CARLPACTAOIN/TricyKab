@extends('layouts.stitch')

@section('title', 'SOS Alerts')

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
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">SOS Alerts</h1>
            <p class="text-slate-500 mt-1">Active safety alerts, acknowledgement, and escalation history.</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-icons-outlined text-red-500">warning</span>
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Open</p>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ $summary['OPEN'] ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-icons-outlined text-amber-500">visibility</span>
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Acknowledged</p>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $summary['ACKNOWLEDGED'] ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-icons-outlined text-emerald-500">check_circle</span>
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Closed</p>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ $summary['CLOSED'] ?? 0 }}</p>
        </div>
    </div>

    <form method="GET" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search passenger/location..." class="px-3 py-2 border rounded-lg text-sm w-72">
        <select name="status" class="px-3 py-2 border rounded-lg text-sm">
            <option value="">All Status</option>
            @foreach(['OPEN', 'ACKNOWLEDGED', 'CLOSED'] as $statusOption)
                <option value="{{ $statusOption }}" {{ $status === $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-primary text-white rounded-lg text-sm">Filter</button>
        <a href="{{ route('admin.sos.export', request()->query()) }}" class="px-4 py-2 border rounded-lg text-sm">Export CSV</a>
    </form>

    <form id="bulkSosForm" method="POST" action="{{ route('admin.sos.bulk-update-status') }}" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap items-center gap-3">
        @csrf
        @method('PATCH')
        <span class="text-sm text-slate-500">Bulk action for selected rows:</span>
        <select name="status" class="px-3 py-2 border rounded-lg text-sm">
            <option value="ACKNOWLEDGED">ACKNOWLEDGED</option>
            <option value="CLOSED">CLOSED</option>
        </select>
        <button class="px-4 py-2 bg-primary text-white rounded-lg text-sm">Apply to Selected</button>
    </form>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4"><input id="toggleAllSos" type="checkbox" class="rounded border-slate-300"></th>
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
                    @forelse($alerts as $alert)
                        <tr>
                            <td class="px-6 py-4"><input type="checkbox" name="alert_ids[]" value="{{ $alert->id }}" form="bulkSosForm" class="sos-row rounded border-slate-300"></td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-500">#{{ $alert->id }}</td>
                            <td class="px-6 py-4 text-sm">{{ $alert->passenger_name ?? $alert->passenger?->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 text-sm">{{ $alert->booking?->booking_reference ?? '—' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $alert->location_note ?? '—' }}
                                @if($alert->latitude && $alert->longitude)
                                    <div class="font-mono">{{ $alert->latitude }}, {{ $alert->longitude }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">{{ $alert->status }}</span></td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $alert->created_at?->format('M d, Y h:i A') }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($alert->status !== 'CLOSED')
                                    <form method="POST" action="{{ route('admin.sos.update-status', $alert) }}" class="inline-flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $alert->status === 'OPEN' ? 'ACKNOWLEDGED' : 'CLOSED' }}">
                                        <button class="bg-primary text-white px-3 py-1.5 rounded-lg text-xs font-semibold">
                                            {{ $alert->status === 'OPEN' ? 'Acknowledge' : 'Close' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Closed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-4 text-center text-slate-500">No SOS alerts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $alerts->links() }}</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('toggleAllSos')?.addEventListener('change', function (event) {
    document.querySelectorAll('.sos-row').forEach((el) => {
        el.checked = event.target.checked;
    });
});
</script>
@endsection
