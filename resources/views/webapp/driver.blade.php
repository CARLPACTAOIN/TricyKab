@extends('layouts.webapp')

@section('head')
    <meta name="api-base" content="{{ url('/api/v1') }}">
@endsection

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
                <p class="text-xs text-slate-500">Optional: paste a driver Sanctum token from <code class="rounded bg-slate-100 px-1">POST /api/v1/auth/otp/verify</code> to load offers and run trip actions against the API.</p>
                <label class="mt-2 block">
                    <span class="mb-1 block text-xs font-semibold text-slate-500">API access token</span>
                    <input id="apiTokenInput" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-primary" type="password" autocomplete="off" placeholder="Bearer token">
                </label>
                <button id="simulateOfferBtn" class="mt-4 w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary/90">
                    Fetch / simulate offer
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
                    <p id="offerPickupText" class="text-sm font-semibold text-slate-700">Kabacan Public Market</p>
                </div>
                <div class="rounded-xl bg-slate-100 p-3">
                    <p class="text-xs uppercase text-slate-500">Destination</p>
                    <p id="offerDestText" class="text-sm font-semibold text-slate-700">USM Main Gate</p>
                </div>
            </div>
            <div class="mt-3 rounded-xl bg-primary/5 p-3">
                <p class="text-xs uppercase tracking-wide text-primary/80">Ride Type / Fare</p>
                <p id="offerFareText" class="text-sm font-bold text-primary">SHARED — PHP 35.00</p>
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
    const apiBase = document.querySelector('meta[name="api-base"]')?.getAttribute('content') || '';

    const state = {
        online: true,
        phase: 'WAITING_OFFERS',
        countdown: null,
        tripId: null,
        bookingId: null,
        pendingOffer: null,
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

    const offerPickupText = document.getElementById('offerPickupText');
    const offerDestText = document.getElementById('offerDestText');
    const offerFareText = document.getElementById('offerFareText');

    const getToken = () => {
        const v = document.getElementById('apiTokenInput')?.value?.trim();
        if (v) {
            try { localStorage.setItem('tricykab_driver_api_token', v); } catch (e) { /* ignore */ }
            return v;
        }
        try { return localStorage.getItem('tricykab_driver_api_token') || ''; } catch (e) { return ''; }
    };

    const authHeaders = (json = false) => {
        const h = { Accept: 'application/json', Authorization: `Bearer ${getToken()}` };
        if (json) h['Content-Type'] = 'application/json';
        return h;
    };

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

    const startCountdown = (seconds) => {
        let left = Math.max(1, seconds || 15);
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

    const openOfferDemo = () => {
        if (!state.online) return;
        offerPickupText.textContent = 'Kabacan Public Market';
        offerDestText.textContent = 'USM Main Gate';
        offerFareText.textContent = 'SHARED — PHP 35.00';
        offerSheet.classList.remove('hidden');
        startCountdown(15);
    };

    const mapOfferToUi = (raw) => {
        const b = raw.booking || {};
        const pu = b.pickup || {};
        const de = b.destination || {};
        offerPickupText.textContent = pu.address || 'Pickup';
        offerDestText.textContent = de.address || 'Destination';
        const fare = b.estimated_fare != null ? `PHP ${b.estimated_fare}` : '—';
        offerFareText.textContent = `${(b.ride_type || 'SHARED')} — ${fare}`;
        state.pendingOffer = {
            bookingId: b.id,
            candidateId: raw.candidate_id,
            dispatchAttemptId: raw.dispatch_attempt_id,
            pickupLat: pu.latitude ?? 7.1083,
            pickupLng: pu.longitude ?? 124.8295,
            destLat: de.latitude ?? 7.1117,
            destLng: de.longitude ?? 124.8419,
            fareAmount: b.estimated_fare != null ? String(b.estimated_fare) : '35.00',
        };
    };

    const fetchDispatchOffer = async () => {
        const r = await fetch(`${apiBase}/drivers/me/dispatch-offers`, { headers: authHeaders(false) });
        const body = await r.json().catch(() => ({}));
        if (!r.ok || !body.success) {
            alert(body?.error?.message || 'No offers or request failed.');
            return;
        }
        const offers = body.data?.offers || [];
        if (!offers.length) {
            alert('No pending dispatch offers.');
            return;
        }
        mapOfferToUi(offers[0]);
        offerSheet.classList.remove('hidden');
        startCountdown(offers[0].countdown_seconds ?? 60);
    };

    onlineToggle.addEventListener('click', async () => {
        state.online = !state.online;
        onlineToggle.textContent = state.online ? 'ONLINE' : 'OFFLINE';
        onlineToggle.className = `rounded-lg px-3 py-1.5 text-xs font-bold text-white ${state.online ? 'bg-success' : 'bg-slate-500'}`;
        const tok = getToken();
        if (tok && apiBase) {
            try {
                await fetch(`${apiBase}/drivers/me/availability`, {
                    method: 'POST',
                    headers: authHeaders(true),
                    body: JSON.stringify({
                        driver_status: state.online ? 'ONLINE' : 'OFFLINE',
                        latitude: state.online ? 7.114 : null,
                        longitude: state.online ? 124.836 : null,
                    }),
                });
            } catch (e) { /* ignore */ }
        }
        if (!state.online) {
            closeOffer();
            setPhase('WAITING_OFFERS');
        }
    });

    simulateOfferBtn.addEventListener('click', () => {
        if (!state.online) return;
        if (getToken() && apiBase) {
            fetchDispatchOffer();
            return;
        }
        openOfferDemo();
    });

    declineOfferBtn.addEventListener('click', async () => {
        const tok = getToken();
        const p = state.pendingOffer;
        if (tok && apiBase && p?.bookingId) {
            try {
                await fetch(`${apiBase}/drivers/bookings/${p.bookingId}/decline`, {
                    method: 'POST',
                    headers: authHeaders(true),
                    body: JSON.stringify({
                        dispatch_attempt_id: p.dispatchAttemptId,
                        candidate_id: p.candidateId,
                        reason_code: 'TOO_FAR',
                    }),
                });
            } catch (e) { /* ignore */ }
        }
        state.pendingOffer = null;
        closeOffer();
        setPhase('WAITING_OFFERS');
    });

    acceptOfferBtn.addEventListener('click', async () => {
        const tok = getToken();
        const p = state.pendingOffer;
        if (tok && apiBase && p?.bookingId) {
            try {
                const r = await fetch(`${apiBase}/drivers/bookings/${p.bookingId}/accept`, {
                    method: 'POST',
                    headers: authHeaders(true),
                    body: JSON.stringify({
                        dispatch_attempt_id: p.dispatchAttemptId,
                        candidate_id: p.candidateId,
                    }),
                });
                const body = await r.json().catch(() => ({}));
                if (!r.ok || !body.success) {
                    alert(body?.error?.message || 'Accept failed');
                    closeOffer();
                    return;
                }
                state.tripId = body.data?.trip_id ?? null;
                state.bookingId = p.bookingId;
            } catch (e) {
                alert('Network error');
                closeOffer();
                return;
            }
        } else {
            state.tripId = null;
            state.bookingId = null;
        }
        closeOffer();
        setPhase('ASSIGNED_PICKUP');
    });

    const postTripGeo = async (pathSuffix, extra = {}) => {
        const p = state.pendingOffer;
        const lat = p?.pickupLat ?? 7.1083;
        const lng = p?.pickupLng ?? 124.8295;
        const r = await fetch(`${apiBase}/drivers/trips/${state.tripId}${pathSuffix}`, {
            method: 'POST',
            headers: authHeaders(true),
            body: JSON.stringify({ latitude: lat, longitude: lng, accuracy_meters: 6, ...extra }),
        });
        const body = await r.json().catch(() => ({}));
        if (!r.ok || !body.success) throw new Error(body?.error?.message || pathSuffix);
    };

    arrivedBtn.addEventListener('click', async () => {
        const tok = getToken();
        if (tok && apiBase && state.tripId) {
            try {
                await postTripGeo('/arrive');
            } catch (e) {
                alert(e.message || 'Arrive failed');
                return;
            }
        }
        setPhase('ARRIVED');
    });

    startTripBtn.addEventListener('click', async () => {
        const tok = getToken();
        if (tok && apiBase && state.tripId) {
            try {
                await postTripGeo('/start');
            } catch (e) {
                alert(e.message || 'Start failed');
                return;
            }
        }
        setPhase('TRIP_IN_PROGRESS');
    });

    addPassengerBtn.addEventListener('click', async () => {
        const tok = getToken();
        if (tok && apiBase && state.tripId) {
            try {
                const r = await fetch(`${apiBase}/drivers/trips/${state.tripId}/add-passengers`, {
                    method: 'POST',
                    headers: authHeaders(true),
                    body: JSON.stringify({ quantity: 1, notes: 'Webapp walk-in' }),
                });
                const body = await r.json().catch(() => ({}));
                if (!r.ok || !body.success) throw new Error(body?.error?.message || 'add-passengers');
                alert('Passenger count updated on server.');
            } catch (e) {
                alert(e.message || 'Failed');
            }
            return;
        }
        alert('Shared passenger added (demo).');
    });

    endTripBtn.addEventListener('click', async () => {
        const tok = getToken();
        const p = state.pendingOffer;
        if (tok && apiBase && state.tripId && p) {
            try {
                const r = await fetch(`${apiBase}/drivers/trips/${state.tripId}/end`, {
                    method: 'POST',
                    headers: authHeaders(true),
                    body: JSON.stringify({
                        latitude: p.destLat,
                        longitude: p.destLng,
                        accuracy_meters: 8,
                    }),
                });
                const endBody = await r.json().catch(() => ({}));
                if (!r.ok || !endBody.success) throw new Error(endBody?.error?.message || 'end');
                if (state.bookingId) {
                    const pr = await fetch(`${apiBase}/payments/${state.bookingId}/record`, {
                        method: 'POST',
                        headers: authHeaders(true),
                        body: JSON.stringify({
                            amount: p.fareAmount,
                            method: 'CASH',
                            recorded_by_role: 'DRIVER',
                            notes: 'Recorded from driver webapp',
                        }),
                    });
                    const payBody = await pr.json().catch(() => ({}));
                    if (pr.ok && payBody.success) {
                        const rc = payBody.data?.receipt?.receipt_number || '—';
                        alert(`Trip completed. Receipt: ${rc}`);
                    }
                }
            } catch (e) {
                alert(e.message || 'End trip failed');
                return;
            }
        }
        state.pendingOffer = null;
        state.tripId = null;
        state.bookingId = null;
        setPhase('TRIP_COMPLETED');
    });

    (function hydrateToken() {
        try {
            const tok = localStorage.getItem('tricykab_driver_api_token');
            const inp = document.getElementById('apiTokenInput');
            if (tok && inp) inp.value = tok;
        } catch (e) { /* ignore */ }
    })();

    setPhase('WAITING_OFFERS');
})();
</script>
@endsection

