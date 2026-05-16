@extends('layouts.stitch')

@section('title', 'Recent OTPs (Dev)')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Recent OTPs</h1>
        <p class="text-slate-500 mt-1">
            Pilot tooling for testers — each OTP request caches its own code for {{ (int) ($cache_ttl_seconds / 60) }} minutes (per challenge ID, not per phone).
            Visible only when <code>APP_DEBUG=true</code>.
        </p>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">OTP</th>
                        <th class="px-4 py-3">Attempts</th>
                        <th class="px-4 py-3">Expires</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Issued</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($rows as $row)
                        <tr class="text-sm">
                            <td class="px-4 py-3 text-slate-500">{{ $row['id'] }}</td>
                            <td class="px-4 py-3 font-mono">{{ $row['phone_number'] }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-xs">{{ $row['role_hint'] }}</span></td>
                            <td class="px-4 py-3 font-mono">
                                @if(!empty($row['plaintext']))
                                    <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200 tracking-widest">{{ $row['plaintext'] }}</span>
                                @else
                                    <span class="text-slate-400">expired / hashed-only</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $row['verify_attempts'] }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ optional($row['expires_at'])->diffForHumans() }}</td>
                            <td class="px-4 py-3">
                                @if($row['consumed_at'])
                                    <span class="text-slate-500">CONSUMED</span>
                                @elseif($row['locked_at'])
                                    <span class="text-rose-500">LOCKED</span>
                                @elseif(optional($row['expires_at'])?->isPast())
                                    <span class="text-amber-500">EXPIRED</span>
                                @else
                                    <span class="text-emerald-500">ACTIVE</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ optional($row['created_at'])->toDateTimeString() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-6 text-center text-slate-400">No OTP challenges issued yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
