@extends('layouts.webapp')

@section('title', 'Trip history - TricyKab')

@section('content')
<div class="min-h-screen bg-background-light pb-24">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('passenger.app') }}" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50" aria-label="Back to book">
                    <span class="material-icons-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight text-slate-900">Trip history</h1>
                    <p class="text-xs text-slate-500">Recent rides on this device</p>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm text-slate-600">Demo trips below; rides you complete on the Book screen are saved locally and appear at the top.</p>
            <button type="button" id="clearLocalTrips" class="text-xs font-semibold text-rose-600 hover:underline">Clear locally saved trips</button>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <ul id="tripList" class="divide-y divide-slate-100" role="list">
                @foreach($demoTrips as $trip)
                    <li class="px-4 py-4 sm:px-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="font-mono text-sm font-bold text-slate-900">{{ $trip['ref'] }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $trip['pickup'] }} → {{ $trip['destination'] }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $trip['at'] }} · {{ $trip['rideType'] }}</p>
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700">{{ $trip['status'] }}</span>
                                <p class="mt-2 text-sm font-semibold text-primary">@if($trip['fare'] !== '—') ₱{{ $trip['fare'] }} @else — @endif</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <p id="tripListEmpty" class="hidden px-6 py-10 text-center text-sm text-slate-500">No trips yet. Book a ride from the Book tab.</p>
        </div>
    </main>

    @include('webapp.partials.passenger-nav', ['activeNav' => $activeNav ?? 'trips'])
</div>
@endsection

@section('scripts')
<script>
(function () {
    const STORAGE_KEY = 'tricykab_passenger_trips';
    const listEl = document.getElementById('tripList');
    const emptyEl = document.getElementById('tripListEmpty');
    const demoNodes = [...listEl.querySelectorAll('li')];

    function formatWhen(iso) {
        try {
            const d = new Date(iso);
            return d.toLocaleString();
        } catch (e) {
            return iso;
        }
    }

    function prependStored() {
        let rows = [];
        try {
            rows = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        } catch (e) {
            rows = [];
        }
        rows.forEach(function (t) {
            const li = document.createElement('li');
            li.className = 'px-4 py-4 sm:px-6 bg-primary/5';
            li.innerHTML = '<div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">' +
                '<div><p class="font-mono text-sm font-bold text-slate-900">' + (t.ref || '—') + '</p>' +
                '<p class="mt-1 text-sm text-slate-600">' + (t.pickup || '') + ' → ' + (t.destination || '') + '</p>' +
                '<p class="mt-1 text-xs text-slate-400">' + formatWhen(t.at) + ' · ' + (t.rideType || '') + '</p></div>' +
                '<div class="text-left sm:text-right">' +
                '<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">' + (t.status || 'COMPLETED') + '</span>' +
                '<p class="mt-2 text-sm font-semibold text-primary">₱' + (t.fare || '0') + '</p></div></div>';
            listEl.insertBefore(li, listEl.firstChild);
        });
    }

    document.getElementById('clearLocalTrips').addEventListener('click', function () {
        if (!confirm('Remove locally saved trips from this browser?')) return;
        localStorage.removeItem(STORAGE_KEY);
        demoNodes.forEach(function (n) { n.style.display = ''; });
        [...listEl.querySelectorAll('li')].forEach(function (li) {
            if (!demoNodes.includes(li)) li.remove();
        });
        emptyEl.classList.toggle('hidden', demoNodes.length > 0 || listEl.querySelectorAll('li').length > 0);
    });

    prependStored();
    const total = listEl.querySelectorAll('li').length;
    emptyEl.classList.toggle('hidden', total > 0);
    demoNodes.forEach(function (n) { if (total > demoNodes.length) n.classList.remove('bg-primary/5'); });
})();
</script>
@endsection
