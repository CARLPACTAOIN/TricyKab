@extends('layouts.webapp')

@section('title', 'Account - TricyKab Driver')

@section('content')
<div class="min-h-screen bg-background-light pb-24">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('driver.app') }}" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50" aria-label="Back to dashboard">
                    <span class="material-icons-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight text-slate-900">Account</h1>
                    <p class="text-xs text-slate-500">Vehicle & compliance (local demo)</p>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-lg px-4 py-6 sm:px-6">
        <div id="accountToast" class="mb-4 hidden rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"></div>

        <form id="driverAccountForm" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Driver name</label>
                <input name="driver_name" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" placeholder="Juan Dela Cruz">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">License No.</label>
                <input name="license" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" placeholder="N01-12-345678">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Plate / Body No.</label>
                <input name="plate" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" placeholder="ABC-1234">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">TODA</label>
                <input name="toda" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" placeholder="Kabacan TODA">
            </div>
            <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Sound alerts for offers</p>
                    <p class="text-xs text-slate-500">UI only — no audio in browser demo</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input name="sound_alerts" type="checkbox" class="peer sr-only" checked>
                    <div class="h-6 w-11 rounded-full bg-slate-200 peer-checked:bg-primary after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></div>
                </label>
            </div>
            <button type="submit" class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-md shadow-primary/25 hover:bg-primary/90">
                Save
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-slate-500">
            <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Admin login</a>
            <span class="mx-2 text-slate-300">·</span>
            <a href="{{ url('/') }}" class="font-semibold text-slate-600 hover:underline">Marketing site</a>
        </p>
    </main>

    @include('webapp.partials.driver-nav', ['activeNav' => $activeNav ?? 'account'])
</div>
@endsection

@section('scripts')
<script>
(function () {
    const PREFIX = 'tricykab_driver_';
    const form = document.getElementById('driverAccountForm');
    const toast = document.getElementById('accountToast');

    function load() {
        ['driver_name', 'license', 'plate', 'toda'].forEach(function (key) {
            const el = form.elements.namedItem(key);
            if (el) el.value = localStorage.getItem(PREFIX + key) || '';
        });
        const sound = form.elements.namedItem('sound_alerts');
        if (sound) sound.checked = localStorage.getItem(PREFIX + 'sound_alerts') !== '0';
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        ['driver_name', 'license', 'plate', 'toda'].forEach(function (key) {
            const el = form.elements.namedItem(key);
            if (el && el.value) localStorage.setItem(PREFIX + key, el.value.trim());
        });
        const sound = form.elements.namedItem('sound_alerts');
        if (sound) localStorage.setItem(PREFIX + 'sound_alerts', sound.checked ? '1' : '0');
        toast.textContent = 'Preferences saved in this browser.';
        toast.classList.remove('hidden');
        setTimeout(function () { toast.classList.add('hidden'); }, 3500);
    });

    load();
})();
</script>
@endsection
