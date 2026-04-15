@extends('layouts.webapp')

@section('title', 'Driver App - TricyKab')

@section('content')
<div class="min-h-screen bg-background-light">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white">
                    <span class="material-icons-outlined">drive_eta</span>
                </div>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight text-slate-900">TricyKab Driver</h1>
                    <p class="text-xs text-slate-500">Route + Dispatch Console</p>
                </div>
            </div>
            <button id="onlineToggle" class="rounded-lg bg-success px-3 py-1.5 text-xs font-bold text-white">ONLINE</button>
        </div>
    </header>

    <main class="mx-auto grid w-full max-w-6xl grid-cols-1 gap-5 px-4 py-5 pb-24 lg:grid-cols-3 lg:px-6">
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-900">Active Area Map</h2>
                <span id="driverStateBadge" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">WAITING_OFFERS</span>
            </div>
            <div class="relative h-[420px] overflow-hidden bg-gradient-to-br from-primary/10 via-secondary/10 to-slate-100">
                <div class="absolute left-[40%] top-[45%] z-10">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-primary/30">
                        <span class="material-icons-outlined text-lg">electric_rickshaw</span>
                    </div>
                </div>
                <div id="pickupPin" class="absolute left-[25%] top-[30%] z-10 hidden">
                    <span class="material-icons-outlined text-3xl text-red-500">place</span>
                </div>
                <div class="absolute bottom-4 left-4 rounded-xl bg-white/95 px-4 py-3 shadow-sm">
                    <p class="text-xs uppercase tracking-wider text-slate-400">Current Zone</p>
                    <p class="text-sm font-semibold text-slate-700">Poblacion, Kabacan</p>
                </div>
            </div>
        </section>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 font-bold text-slate-900">Live Shift Summary</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-primary/5 p-3">
                        <p class="text-xs uppercase text-primary/80">Trips Today</p>
                        <p class="text-xl font-extrabold text-primary">14</p>
                    </div>
                    <div class="rounded-xl bg-secondary/10 p-3">
                        <p class="text-xs uppercase text-secondary">Earnings</p>
                        <p class="text-xl font-extrabold text-secondary">PHP 1,520</p>
                    </div>
                </div>
                <button id="simulateOfferBtn" class="mt-4 w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary/90">
                    Simulate Incoming Offer
                </button>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 font-bold text-slate-900">Assignment Controls</h3>
                <div class="space-y-2">
                    <button id="arrivedBtn" class="hidden w-full rounded-lg bg-secondary px-3 py-2 text-xs font-bold text-white hover:bg-secondary/90">Mark Arrived</button>
                    <button id="startTripBtn" class="hidden w-full rounded-lg bg-primary px-3 py-2 text-xs font-bold text-white hover:bg-primary/90">Start Trip</button>
                    <button id="addPassengerBtn" class="hidden w-full rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-white hover:bg-amber-600">Add Shared Passenger</button>
                    <button id="endTripBtn" class="hidden w-full rounded-lg bg-success px-3 py-2 text-xs font-bold text-white hover:bg-success/90">End Trip</button>
                </div>
            </div>
        </aside>
    </main>

    @include('webapp.partials.driver-nav', ['activeNav' => $activeNav ?? 'dashboard'])

    <div id="offerSheet" class="fixed inset-0 z-50 hidden bg-slate-900/40">
        <div class="absolute bottom-0 left-0 right-0 mx-auto w-full max-w-3xl rounded-t-3xl border border-slate-200 bg-white p-5 shadow-2xl">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-lg font-extrabold text-slate-900">Incoming Offer</h3>
                <span id="offerCountdown" class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">15s</span>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-100 p-3">
                    <p class="text-xs uppercase text-slate-500">Pickup</p>
                    <p class="text-sm font-semibold text-slate-700">Kabacan Public Market</p>
                </div>
                <div class="rounded-xl bg-slate-100 p-3">
                    <p class="text-xs uppercase text-slate-500">Destination</p>
                    <p class="text-sm font-semibold text-slate-700">USM Main Gate</p>
                </div>
            </div>
            <div class="mt-3 rounded-xl bg-primary/5 p-3">
                <p class="text-xs uppercase tracking-wide text-primary/80">Ride Type / Fare</p>
                <p class="text-sm font-bold text-primary">SHARED - PHP 35.00</p>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2">
                <button id="declineOfferBtn" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-100">Decline</button>
                <button id="acceptOfferBtn" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary/90">Accept</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(() => {
    const state = {
        online: true,
        phase: 'WAITING_OFFERS',
        countdown: null,
    };

    const onlineToggle = document.getElementById('onlineToggle');
    const stateBadge = document.getElementById('driverStateBadge');
    const offerSheet = document.getElementById('offerSheet');
    const offerCountdown = document.getElementById('offerCountdown');
    const pickupPin = document.getElementById('pickupPin');

    const simulateOfferBtn = document.getElementById('simulateOfferBtn');
    const acceptOfferBtn = document.getElementById('acceptOfferBtn');
    const declineOfferBtn = document.getElementById('declineOfferBtn');

    const arrivedBtn = document.getElementById('arrivedBtn');
    const startTripBtn = document.getElementById('startTripBtn');
    const addPassengerBtn = document.getElementById('addPassengerBtn');
    const endTripBtn = document.getElementById('endTripBtn');

    const setPhase = (phase) => {
        state.phase = phase;
        stateBadge.textContent = phase;
        stateBadge.className = 'rounded-full px-3 py-1 text-xs font-bold';
        [arrivedBtn, startTripBtn, addPassengerBtn, endTripBtn].forEach((btn) => btn.classList.add('hidden'));

        if (phase === 'WAITING_OFFERS') {
            stateBadge.classList.add('bg-slate-100', 'text-slate-700');
            pickupPin.classList.add('hidden');
        } else if (phase === 'ASSIGNED_PICKUP') {
            stateBadge.classList.add('bg-blue-100', 'text-blue-700');
            pickupPin.classList.remove('hidden');
            arrivedBtn.classList.remove('hidden');
        } else if (phase === 'ARRIVED') {
            stateBadge.classList.add('bg-amber-100', 'text-amber-700');
            pickupPin.classList.remove('hidden');
            startTripBtn.classList.remove('hidden');
        } else if (phase === 'TRIP_IN_PROGRESS') {
            stateBadge.classList.add('bg-primary/10', 'text-primary');
            pickupPin.classList.add('hidden');
            addPassengerBtn.classList.remove('hidden');
            endTripBtn.classList.remove('hidden');
        } else if (phase === 'TRIP_COMPLETED') {
            stateBadge.classList.add('bg-emerald-100', 'text-emerald-700');
            pickupPin.classList.add('hidden');
        }
    };

    const closeOffer = () => {
        offerSheet.classList.add('hidden');
        if (state.countdown) {
            clearInterval(state.countdown);
            state.countdown = null;
        }
    };

    const openOffer = () => {
        if (!state.online) return;
        offerSheet.classList.remove('hidden');
        let left = 15;
        offerCountdown.textContent = `${left}s`;
        if (state.countdown) clearInterval(state.countdown);
        state.countdown = setInterval(() => {
            left -= 1;
            offerCountdown.textContent = `${left}s`;
            if (left <= 0) {
                closeOffer();
                setPhase('WAITING_OFFERS');
            }
        }, 1000);
    };

    onlineToggle.addEventListener('click', () => {
        state.online = !state.online;
        onlineToggle.textContent = state.online ? 'ONLINE' : 'OFFLINE';
        onlineToggle.className = `rounded-lg px-3 py-1.5 text-xs font-bold text-white ${state.online ? 'bg-success' : 'bg-slate-500'}`;
        if (!state.online) {
            closeOffer();
            setPhase('WAITING_OFFERS');
        }
    });

    simulateOfferBtn.addEventListener('click', openOffer);
    declineOfferBtn.addEventListener('click', () => {
        closeOffer();
        setPhase('WAITING_OFFERS');
    });
    acceptOfferBtn.addEventListener('click', () => {
        closeOffer();
        setPhase('ASSIGNED_PICKUP');
    });

    arrivedBtn.addEventListener('click', () => setPhase('ARRIVED'));
    startTripBtn.addEventListener('click', () => setPhase('TRIP_IN_PROGRESS'));
    addPassengerBtn.addEventListener('click', () => alert('Shared passenger added. Capacity: 2/4'));
    endTripBtn.addEventListener('click', () => setPhase('TRIP_COMPLETED'));

    setPhase('WAITING_OFFERS');
})();
</script>
@endsection

