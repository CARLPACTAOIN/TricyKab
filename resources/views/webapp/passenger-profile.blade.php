@extends('layouts.webapp')

@section('title', 'Profile - TricyKab Passenger')

@section('content')
<div class="min-h-screen bg-background-light pb-24">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('passenger.app') }}" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50" aria-label="Back to book">
                    <span class="material-icons-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight text-slate-900">Profile</h1>
                    <p class="text-xs text-slate-500">Saved on this device only</p>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-lg px-4 py-6 sm:px-6">
        <div id="profileToast" class="mb-4 hidden rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"></div>

        <form id="passengerProfileForm" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Display name</label>
                <input name="display_name" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" placeholder="e.g. Jane Doe" autocomplete="name">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Mobile (OTP)</label>
                <input name="phone" type="tel" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" placeholder="+63 9XX XXX XXXX" autocomplete="tel">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Emergency contact</label>
                <input name="emergency_name" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" placeholder="Name">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Emergency phone</label>
                <input name="emergency_phone" type="tel" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" placeholder="+63…">
            </div>
            <button type="submit" class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-md shadow-primary/25 hover:bg-primary/90">
                Save profile
            </button>
        </form>
    </main>

    @include('webapp.partials.passenger-nav', ['activeNav' => $activeNav ?? 'profile'])
</div>
@endsection

@section('scripts')
<script>
(function () {
    const PREFIX = 'tricykab_passenger_';
    const form = document.getElementById('passengerProfileForm');
    const toast = document.getElementById('profileToast');

    function load() {
        ['display_name', 'phone', 'emergency_name', 'emergency_phone'].forEach(function (key) {
            const el = form.elements.namedItem(key);
            if (el) el.value = localStorage.getItem(PREFIX + key) || '';
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        ['display_name', 'phone', 'emergency_name', 'emergency_phone'].forEach(function (key) {
            const el = form.elements.namedItem(key);
            if (el && el.value) localStorage.setItem(PREFIX + key, el.value.trim());
            else localStorage.removeItem(PREFIX + key);
        });
        const name = form.elements.namedItem('display_name')?.value?.trim();
        if (name) localStorage.setItem(PREFIX + 'display_name', name);
        toast.textContent = 'Saved locally. This will sync when accounts are connected to the server.';
        toast.classList.remove('hidden');
        setTimeout(function () { toast.classList.add('hidden'); }, 4000);
    });

    load();
})();
</script>
@endsection
