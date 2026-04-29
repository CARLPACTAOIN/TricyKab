@extends('layouts.stitch')

@section('title', 'Disputes')

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

    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Disputes</h1>
        <p class="text-slate-500 mt-1">Fare disputes, trip incidents, and resolution workspace.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach(['OPEN', 'UNDER_REVIEW', 'RESOLVED', 'REJECTED'] as $kpiStatus)
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">{{ str_replace('_', ' ', $kpiStatus) }}</p>
                <p class="text-2xl font-bold mt-1">{{ $summary[$kpiStatus] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    <form method="GET" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search disputes..." class="px-3 py-2 border rounded-lg text-sm w-72">
        <select name="status" class="px-3 py-2 border rounded-lg text-sm">
            <option value="">All Status</option>
            @foreach(['OPEN', 'UNDER_REVIEW', 'RESOLVED', 'REJECTED'] as $statusOption)
                <option value="{{ $statusOption }}" {{ $status === $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-primary text-white rounded-lg text-sm">Filter</button>
        <a href="{{ route('admin.disputes.export', request()->query()) }}" class="px-4 py-2 border rounded-lg text-sm">Export CSV</a>
    </form>

    <form id="bulkDisputeForm" method="POST" action="{{ route('admin.disputes.bulk-update') }}" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap items-center gap-3">
        @csrf
        @method('PATCH')
        <span class="text-sm text-slate-500">Bulk action for selected rows:</span>
        <select name="status" class="px-3 py-2 border rounded-lg text-sm">
            @foreach(['OPEN', 'UNDER_REVIEW', 'RESOLVED', 'REJECTED'] as $statusOption)
                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
            @endforeach
        </select>
        <input type="text" name="resolution_notes" placeholder="Optional note" class="px-3 py-2 border rounded-lg text-sm w-64">
        <button class="px-4 py-2 bg-primary text-white rounded-lg text-sm">Apply to Selected</button>
    </form>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-4 py-3">
                            <input id="toggleAllDisputes" type="checkbox" class="rounded border-slate-300">
                        </th>
                        <th class="px-4 py-3">Booking</th>
                        <th class="px-4 py-3">Driver</th>
                        <th class="px-4 py-3">Reported By</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($disputes as $dispute)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <input type="checkbox" name="dispute_ids[]" value="{{ $dispute->id }}" form="bulkDisputeForm" class="dispute-row rounded border-slate-300">
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $dispute->booking?->booking_reference ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $dispute->driver?->full_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $dispute->reported_by_name ?? $dispute->reported_by_role }}</td>
                            <td class="px-4 py-3 text-sm">{{ $dispute->dispute_type }}</td>
                            <td class="px-4 py-3 text-sm max-w-[300px]">{{ \Illuminate\Support\Str::limit($dispute->description, 80) }}</td>
                            <td class="px-4 py-3 text-sm font-semibold">{{ $dispute->status }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.disputes.update', $dispute) }}" class="flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="px-2 py-1 border rounded text-xs">
                                        @foreach(['OPEN', 'UNDER_REVIEW', 'RESOLVED', 'REJECTED'] as $statusOption)
                                            <option value="{{ $statusOption }}" {{ $dispute->status === $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="resolution_notes" placeholder="Notes" class="px-2 py-1 border rounded text-xs">
                                    <button class="px-2 py-1 bg-primary text-white rounded text-xs">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-4 text-center text-slate-500">No disputes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $disputes->links() }}</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('toggleAllDisputes')?.addEventListener('change', function (event) {
    document.querySelectorAll('.dispute-row').forEach((el) => {
        el.checked = event.target.checked;
    });
});
</script>
@endsection
