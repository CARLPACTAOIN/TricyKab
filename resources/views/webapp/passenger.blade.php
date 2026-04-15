@extends('layouts.webapp')

@section('title', 'Passenger App - TricyKab')

@section('content')
<div class="min-h-screen bg-background-light">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white">
                    <span class="material-icons-outlined">electric_rickshaw</span>
                </div>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight text-slate-900">TricyKab Passenger</h1>
                    <p class="text-xs text-slate-500">Kabacan Smart Dispatch</p>
                </div>
            </div>
            <a href="{{ route('passenger.profile') }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50" id="passengerHeaderName">
                Profile
            </a>
        </div>
    </header>

    <main class="mx-auto grid w-full max-w-6xl grid-cols-1 gap-5 px-4 py-5 pb-24 lg:grid-cols-3 lg:px-6">
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-900">Live Map</h2>
                <span id="bookingStatusBadge" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">IDLE</span>
            </div>
            <div class="relative h-[420px] overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-secondary/10 to-slate-100"></div>
                <div class="absolute left-[18%] top-[34%] z-10">
                    <span class="material-icons-outlined text-3xl text-success">my_location</span>
                </div>
                <div class="absolute right-[22%] top-[58%] z-10">
                    <span class="material-icons-outlined text-3xl text-red-500">place</span>
                </div>
                <div id="driverMarker" class="absolute left-[40%] top-[45%] z-10 hidden">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-primary/30">
                        <span class="material-icons-outlined text-lg">electric_rickshaw</span>
                    </div>
                </div>
                <div class="absolute bottom-4 left-4 rounded-xl bg-white/95 px-4 py-3 shadow-sm">
                    <p class="text-xs uppercase tracking-wider text-slate-400">Route Preview</p>
                    <p class="text-sm font-semibold text-slate-700">Kabacan Public Market -> USM Main Gate</p>
                </div>

                <div id="searchOverlay" class="absolute inset-0 hidden items-center justify-center bg-white/70 backdrop-blur-sm">
                    <div class="rounded-2xl border border-slate-200 bg-white p-7 text-center shadow-xl">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-primary/10">
                            <div class="flex h-11 w-11 animate-pulse items-center justify-center rounded-full bg-primary text-white">
                                <span class="material-icons-outlined">search</span>
                            </div>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Searching for Driver...</h3>
                        <p class="mt-1 text-sm text-slate-500">Matching you to the best nearby driver.</p>
                    </div>
                </div>
            </div>
        </section>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 font-bold text-slate-900">Book a Ride</h3>
                <div class="space-y-3">
                    <label class="block">
                        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pickup</span>
                        <input id="pickupInput" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" value="Kabacan Public Market">
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Destination</span>
                        <input id="destinationInput" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" value="USM Main Gate">
                    </label>
                </div>

                <div class="mt-4 rounded-xl bg-slate-100 p-1">
                    <button data-ride-type="SHARED" class="ride-type-btn w-1/2 rounded-lg bg-white px-3 py-2 text-sm font-bold text-primary shadow-sm">SHARED</button>
                    <button data-ride-type="SPECIAL" class="ride-type-btn w-1/2 rounded-lg px-3 py-2 text-sm font-bold text-slate-500">SPECIAL</button>
                </div>

                <div id="specialFareWrap" class="mt-3 hidden">
                    <label class="block">
                        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Proposed Special Fare (PHP)</span>
                        <input id="specialFareInput" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-primary" value="95.00">
                    </label>
                </div>

                <div class="mt-4 rounded-xl border border-primary/20 bg-primary/5 p-4">
                    <p class="text-xs uppercase tracking-wide text-primary/80">Estimated Fare</p>
                    <p id="farePreview" class="text-2xl font-extrabold text-primary">PHP 35.00</p>
                    <p id="etaPreview" class="text-xs text-slate-500">ETA: ~13 minutes (estimate)</p>
                </div>

                <button id="bookBtn" class="mt-4 w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-md shadow-primary/25 hover:bg-primary/90">
                    Book Ride
                </button>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 font-bold text-slate-900">Trip Lifecycle</h3>
                <ol class="space-y-2 text-sm">
                    <li><span id="step-searching" class="font-semibold text-slate-500">1. Searching Driver</span></li>
                    <li><span id="step-assigned" class="font-semibold text-slate-500">2. Driver Assigned</span></li>
                    <li><span id="step-progress" class="font-semibold text-slate-500">3. Trip in Progress</span></li>
                    <li><span id="step-complete" class="font-semibold text-slate-500">4. Completed</span></li>
                </ol>
                <div class="mt-4 flex gap-2">
                    <button id="startTripBtn" class="hidden flex-1 rounded-lg bg-secondary px-3 py-2 text-xs font-bold text-white hover:bg-secondary/90">Start Trip</button>
                    <button id="completeTripBtn" class="hidden flex-1 rounded-lg bg-success px-3 py-2 text-xs font-bold text-white hover:bg-success/90">Complete</button>
                </div>
            </div>
        </aside>
    </main>

    @include('webapp.partials.passenger-nav', ['activeNav' => $activeNav ?? 'book'])
</div>
@endsection

@section('scripts')
<script>
(() => {
    const state = {
        rideType: 'SHARED',
        bookingState: 'IDLE',
    };

    const rideBtns = [...document.querySelectorAll('.ride-type-btn')];
    const specialWrap = document.getElementById('specialFareWrap');
    const farePreview = document.getElementById('farePreview');
    const etaPreview = document.getElementById('etaPreview');
    const statusBadge = document.getElementById('bookingStatusBadge');
    const searchOverlay = document.getElementById('searchOverlay');
    const driverMarker = document.getElementById('driverMarker');
    const bookBtn = document.getElementById('bookBtn');
    const startTripBtn = document.getElementById('startTripBtn');
    const completeTripBtn = document.getElementById('completeTripBtn');

    const stepSearching = document.getElementById('step-searching');
    const stepAssigned = document.getElementById('step-assigned');
    const stepProgress = document.getElementById('step-progress');
    const stepComplete = document.getElementById('step-complete');

    const setActiveStep = (el) => {
        [stepSearching, stepAssigned, stepProgress, stepComplete].forEach((s) => {
            s.className = 'font-semibold text-slate-500';
        });
        if (el) el.className = 'font-bold text-primary';
    };

    const renderRideType = () => {
        rideBtns.forEach((btn) => {
            const active = btn.dataset.rideType === state.rideType;
            btn.classList.toggle('bg-white', active);
            btn.classList.toggle('text-primary', active);
            btn.classList.toggle('shadow-sm', active);
            btn.classList.toggle('text-slate-500', !active);
        });
        if (state.rideType === 'SPECIAL') {
            specialWrap.classList.remove('hidden');
            farePreview.textContent = 'PHP 95.00';
            etaPreview.textContent = 'ETA: ~13 minutes (exclusive ride)';
        } else {
            specialWrap.classList.add('hidden');
            farePreview.textContent = 'PHP 35.00';
            etaPreview.textContent = 'ETA: ~13 minutes (estimate)';
        }
    };

    const setBookingState = (next) => {
        state.bookingState = next;
        statusBadge.textContent = next.replaceAll('_', ' ');
        statusBadge.className = 'rounded-full px-3 py-1 text-xs font-bold';
        searchOverlay.classList.add('hidden');
        searchOverlay.classList.remove('flex');
        startTripBtn.classList.add('hidden');
        completeTripBtn.classList.add('hidden');

        switch (next) {
            case 'SEARCHING_DRIVER':
                statusBadge.classList.add('bg-amber-100', 'text-amber-700');
                searchOverlay.classList.remove('hidden');
                searchOverlay.classList.add('flex');
                driverMarker.classList.add('hidden');
                setActiveStep(stepSearching);
                break;
            case 'DRIVER_ASSIGNED':
                statusBadge.classList.add('bg-blue-100', 'text-blue-700');
                driverMarker.classList.remove('hidden');
                startTripBtn.classList.remove('hidden');
                setActiveStep(stepAssigned);
                break;
            case 'TRIP_IN_PROGRESS':
                statusBadge.classList.add('bg-sky-100', 'text-sky-700');
                driverMarker.classList.remove('hidden');
                completeTripBtn.classList.remove('hidden');
                setActiveStep(stepProgress);
                break;
            case 'COMPLETED':
                statusBadge.classList.add('bg-emerald-100', 'text-emerald-700');
                driverMarker.classList.add('hidden');
                setActiveStep(stepComplete);
                break;
            default:
                statusBadge.classList.add('bg-slate-100', 'text-slate-600');
                driverMarker.classList.add('hidden');
                setActiveStep(null);
        }
    };

    rideBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            state.rideType = btn.dataset.rideType;
            renderRideType();
        });
    });

    bookBtn.addEventListener('click', () => {
        setBookingState('SEARCHING_DRIVER');
        setTimeout(() => {
            if (state.bookingState === 'SEARCHING_DRIVER') {
                setBookingState('DRIVER_ASSIGNED');
            }
        }, 1800);
    });

    startTripBtn.addEventListener('click', () => setBookingState('TRIP_IN_PROGRESS'));
    completeTripBtn.addEventListener('click', () => {
        setBookingState('COMPLETED');
        try {
            const pickup = document.getElementById('pickupInput')?.value || 'Pickup';
            const dest = document.getElementById('destinationInput')?.value || 'Destination';
            const fare = farePreview?.textContent?.replace(/^PHP\s*/i, '').trim() || '0';
            const list = JSON.parse(localStorage.getItem('tricykab_passenger_trips') || '[]');
            list.unshift({
                ref: 'BKG-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 900000) + 100000),
                status: 'COMPLETED',
                pickup,
                destination: dest,
                fare,
                rideType: state.rideType,
                at: new Date().toISOString(),
            });
            localStorage.setItem('tricykab_passenger_trips', JSON.stringify(list.slice(0, 50)));
        } catch (e) { /* ignore */ }
    });

    (function syncHeaderName() {
        try {
            const n = localStorage.getItem('tricykab_passenger_display_name');
            if (n && document.getElementById('passengerHeaderName')) {
                document.getElementById('passengerHeaderName').textContent = n;
            }
        } catch (e) { /* ignore */ }
    })();

    renderRideType();
    setBookingState('IDLE');
})();
</script>
@endsection

