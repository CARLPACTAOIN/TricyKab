@php
    $active = $activeNav ?? 'dashboard';
    $item = function (string $key) use ($active): string {
        return $active === $key
            ? 'flex flex-1 min-w-0 items-center justify-center gap-1.5 rounded-lg bg-primary/10 px-2 py-2 text-sm font-bold text-primary sm:gap-2 sm:px-3'
            : 'flex flex-1 min-w-0 items-center justify-center gap-1.5 rounded-lg px-2 py-2 text-sm font-semibold text-slate-500 hover:bg-slate-50 sm:gap-2 sm:px-3';
    };
@endphp
<nav class="fixed bottom-0 left-0 right-0 z-30 border-t border-slate-200 bg-white" aria-label="Driver primary">
    <div class="mx-auto flex h-16 w-full max-w-6xl items-stretch justify-around px-2 sm:px-4">
        <a class="{{ $item('dashboard') }}" href="{{ route('driver.app') }}">
            <span class="material-icons-outlined text-base">map</span>
            <span>Dashboard</span>
        </a>
        <a class="{{ $item('earnings') }}" href="{{ route('driver.earnings') }}">
            <span class="material-icons-outlined text-base">receipt_long</span>
            <span>Earnings</span>
        </a>
        <a class="{{ $item('account') }}" href="{{ route('driver.account') }}">
            <span class="material-icons-outlined text-base">person</span>
            <span>Account</span>
        </a>
    </div>
</nav>
