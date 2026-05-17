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

    <div class="grid grid-cols-3 gap-4" id="sos-stats-container">
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
        <input type="text" name="search" value="{{ $search }}" placeholder="Search reporter/location..." class="px-3 py-2 border rounded-lg text-sm w-72">
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

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        <div class="xl:col-span-7 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/50">
                <p class="text-xs text-slate-500">Click a row with coordinates to view the alert on the map.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                            <th class="px-4 py-4"><input id="toggleAllSos" type="checkbox" class="rounded border-slate-300"></th>
                            <th class="px-4 py-4">ID</th>
                            <th class="px-4 py-4">Reporter</th>
                            <th class="px-4 py-4">Booking</th>
                            <th class="px-4 py-4">Location</th>
                            <th class="px-4 py-4">Status</th>
                            <th class="px-4 py-4">Created</th>
                            <th class="px-4 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800" id="sos-table-body">
                        @forelse($alerts as $alert)
                            @php
                                $isDriver = ($alert->reporter_role ?? 'PASSENGER') === 'DRIVER';
                                $reporterName = $isDriver
                                    ? ($alert->driver_name ?? trim(($alert->driver?->first_name ?? '').' '.($alert->driver?->last_name ?? '')) ?: 'Unknown')
                                    : ($alert->passenger_name ?? $alert->passenger?->name ?? 'Unknown');
                                $hasCoords = $alert->latitude !== null && $alert->longitude !== null;
                            @endphp
                            <tr
                                class="sos-alert-row transition-colors {{ $hasCoords ? 'cursor-pointer hover:bg-red-50/60 dark:hover:bg-red-950/20' : '' }}"
                                data-sos-row="1"
                                data-alert-id="{{ $alert->id }}"
                                data-reporter-role="{{ $alert->reporter_role ?? 'PASSENGER' }}"
                                data-reporter-name="{{ $reporterName }}"
                                data-booking-ref="{{ $alert->booking?->booking_reference ?? '' }}"
                                data-status="{{ $alert->status }}"
                                data-location-note="{{ $alert->location_note ?? '' }}"
                                data-created-at="{{ $alert->created_at?->format('M d, Y h:i A') ?? '' }}"
                                data-lat="{{ $hasCoords ? $alert->latitude : '' }}"
                                data-lng="{{ $hasCoords ? $alert->longitude : '' }}"
                            >
                                <td class="px-4 py-4" onclick="event.stopPropagation()">
                                    <input type="checkbox" name="alert_ids[]" value="{{ $alert->id }}" form="bulkSosForm" class="sos-row rounded border-slate-300">
                                </td>
                                <td class="px-4 py-4 text-sm font-medium text-slate-500">#{{ $alert->id }}</td>
                                <td class="px-4 py-4 text-sm">
                                    @if($isDriver)
                                        <div class="text-[10px] font-bold uppercase text-amber-600">Driver</div>
                                        <div>{{ $reporterName }}</div>
                                    @else
                                        <div class="text-[10px] font-bold uppercase text-primary">Passenger</div>
                                        <div>{{ $reporterName }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm">{{ $alert->booking?->booking_reference ?? '—' }}</td>
                                <td class="px-4 py-4 text-xs text-slate-500">
                                    {{ $alert->location_note ?? '—' }}
                                    @if($hasCoords)
                                        <button type="button" class="sos-coord-link mt-1 block font-mono text-left text-primary hover:underline">
                                            {{ $alert->latitude }}, {{ $alert->longitude }}
                                        </button>
                                    @endif
                                </td>
                                <td class="px-4 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">{{ $alert->status }}</span></td>
                                <td class="px-4 py-4 text-xs text-slate-500">{{ $alert->created_at?->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-4 text-right" onclick="event.stopPropagation()">
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

        <div class="xl:col-span-5 xl:sticky xl:top-6" id="sos-detail-panel">
            <div id="sos-detail-empty" class="bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-8 text-center">
                <span class="material-icons-outlined text-4xl text-slate-300 mb-3">map</span>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Select an SOS alert</p>
                <p class="text-xs text-slate-500 mt-1">Click a table row or its coordinates to open the location map.</p>
            </div>

            <div id="sos-detail-content" class="hidden bg-white dark:bg-slate-900 rounded-xl border-2 border-red-200 dark:border-red-900/50 shadow-lg overflow-hidden">
                <div class="px-5 py-4 bg-red-50 dark:bg-red-950/30 border-b border-red-100 dark:border-red-900/40 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-red-600">SOS Alert</p>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white" id="sos-detail-title">#—</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1" id="sos-detail-reporter">—</p>
                    </div>
                    <span id="sos-detail-status" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">—</span>
                </div>

                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold">Booking</p>
                            <p class="font-medium text-slate-800 dark:text-slate-100" id="sos-detail-booking">—</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold">Reported</p>
                            <p class="font-medium text-slate-800 dark:text-slate-100" id="sos-detail-created">—</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold">Note</p>
                            <p class="text-slate-700 dark:text-slate-200" id="sos-detail-note">—</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold">Coordinates</p>
                            <p class="font-mono text-sm text-slate-800 dark:text-slate-100" id="sos-detail-coords">—</p>
                            <a id="sos-detail-osm-link" href="#" target="_blank" rel="noopener" class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-primary hover:underline">
                                <span class="material-icons-outlined text-sm">open_in_new</span>
                                Open in OpenStreetMap
                            </a>
                        </div>
                    </div>

                    <div
                        class="relative h-80 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800"
                        id="sos-map-root"
                        data-map-root
                        data-map-context="sos-alert"
                        data-map-payload="{}"
                    >
                        <div data-map-canvas class="h-full w-full min-h-[20rem]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>
<script>
document.getElementById('toggleAllSos')?.addEventListener('change', function (event) {
    document.querySelectorAll('.sos-row').forEach((el) => {
        el.checked = event.target.checked;
    });
});

const SosDashboard = (function() {
    let latestId = parseInt('{{ $alerts->max("id") ?? 0 }}', 10);
    let selectedAlertId = null;
    const pollUrl = '{{ route("admin.sos.poll", request()->query()) }}';

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        const isError = type === 'error';
        const isAlert = type === 'alert';

        let bgClass = 'bg-slate-800 text-white';
        if (isError) bgClass = 'bg-rose-600 text-white';
        if (isAlert) bgClass = 'bg-red-600 text-white border-2 border-red-400 shadow-[0_0_15px_rgba(220,38,38,0.5)] animate-pulse';

        toast.className = `px-4 py-3 rounded shadow-lg text-sm font-semibold flex items-center gap-2 transform transition-all duration-300 translate-y-full opacity-0 ${bgClass}`;

        const icon = isError ? 'error' : (isAlert ? 'campaign' : 'check_circle');
        toast.innerHTML = `<span class="material-icons-outlined text-lg">${icon}</span> ${message}`;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-full', 'opacity-0');
        });

        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    function openSosDetail(row) {
        const lat = parseFloat(row.dataset.lat);
        const lng = parseFloat(row.dataset.lng);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            showToast('This alert has no GPS coordinates.', 'error');
            return;
        }

        const alertId = row.dataset.alertId;
        selectedAlertId = alertId;

        document.querySelectorAll('.sos-alert-row').forEach((r) => {
            r.classList.remove('bg-red-100', 'dark:bg-red-950/40', 'ring-2', 'ring-red-300', 'dark:ring-red-800');
        });
        row.classList.add('bg-red-100', 'dark:bg-red-950/40', 'ring-2', 'ring-red-300', 'dark:ring-red-800');

        const role = row.dataset.reporterRole === 'DRIVER' ? 'Driver' : 'Passenger';
        const reporterName = row.dataset.reporterName || 'Unknown';

        document.getElementById('sos-detail-empty')?.classList.add('hidden');
        const content = document.getElementById('sos-detail-content');
        content?.classList.remove('hidden');

        document.getElementById('sos-detail-title').textContent = `#${alertId}`;
        document.getElementById('sos-detail-reporter').textContent = `${role}: ${reporterName}`;
        document.getElementById('sos-detail-status').textContent = row.dataset.status || '—';
        document.getElementById('sos-detail-booking').textContent = row.dataset.bookingRef || '—';
        document.getElementById('sos-detail-created').textContent = row.dataset.createdAt || '—';
        document.getElementById('sos-detail-note').textContent = row.dataset.locationNote || '—';
        document.getElementById('sos-detail-coords').textContent = `${lat}, ${lng}`;

        const osmLink = document.getElementById('sos-detail-osm-link');
        if (osmLink) {
            osmLink.href = `https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}#map=17/${lat}/${lng}`;
        }

        const mapRoot = document.getElementById('sos-map-root');
        if (mapRoot && window.TricyKabMaps?.initSosAlertDetail) {
            window.TricyKabMaps.initSosAlertDetail(mapRoot, {
                alertId,
                lat,
                lng,
                reporterRole: row.dataset.reporterRole,
                label: `${role} — ${reporterName}`,
                zoom: 16,
            });
        }

        content?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function attachSosRowHandlers() {
        document.querySelectorAll('.sos-alert-row[data-sos-row]').forEach((row) => {
            if (row.dataset.rowBound === '1') return;
            row.dataset.rowBound = '1';

            row.addEventListener('click', () => openSosDetail(row));
            row.querySelector('.sos-coord-link')?.addEventListener('click', (e) => {
                e.stopPropagation();
                openSosDetail(row);
            });
        });
    }

    function attachAjaxForms() {
        document.querySelectorAll('form[action*="/sos-alerts/"][method="POST"]:not(#bulkSosForm)').forEach(form => {
            if (form.dataset.ajaxAttached) return;
            form.dataset.ajaxAttached = "true";

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = form.querySelector('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '...';
                btn.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        pollData(true);
                    } else {
                        showToast(data.message || 'Error occurred', 'error');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    showToast('Network error', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            });
        });

        const bulkForm = document.getElementById('bulkSosForm');
        if (bulkForm && !bulkForm.dataset.ajaxAttached) {
            bulkForm.dataset.ajaxAttached = "true";
            bulkForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const selected = document.querySelectorAll('.sos-row:checked');
                if (selected.length === 0) {
                    showToast('No rows selected', 'error');
                    return;
                }

                const btn = bulkForm.querySelector('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '...';
                btn.disabled = true;

                fetch(bulkForm.action, {
                    method: 'POST',
                    body: new FormData(bulkForm),
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        document.querySelectorAll('.sos-row:checked').forEach(c => c.checked = false);
                        const toggleAll = document.getElementById('toggleAllSos');
                        if (toggleAll) toggleAll.checked = false;
                        pollData(true);
                    } else {
                        showToast(data.message || 'Error', 'error');
                    }
                })
                .catch(() => showToast('Network error', 'error'))
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            });
        }
    }

    function pollData(forceSilently = false) {
        fetch(pollUrl, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                if (!forceSilently && data.latest_id > latestId) {
                    latestId = data.latest_id;
                    showToast('NEW SOS ALERT RECEIVED!', 'alert');
                    const audio = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU');
                    audio.play().catch(() => {});
                } else if (forceSilently) {
                    latestId = data.latest_id;
                }

                const parser = new DOMParser();
                const doc = parser.parseFromString(data.html, 'text/html');

                const newStats = doc.getElementById('sos-stats-container');
                const newTable = doc.getElementById('sos-table-body');

                if (newStats) document.getElementById('sos-stats-container').innerHTML = newStats.innerHTML;
                if (newTable) {
                    document.getElementById('sos-table-body').innerHTML = newTable.innerHTML;
                    attachAjaxForms();
                    attachSosRowHandlers();

                    if (selectedAlertId) {
                        const row = document.querySelector(`.sos-alert-row[data-alert-id="${selectedAlertId}"]`);
                        if (row) openSosDetail(row);
                    }
                }
            })
            .catch(err => console.error('Polling failed', err));
    }

    attachAjaxForms();
    attachSosRowHandlers();
    setInterval(() => pollData(), 10000);

    return { pollData };
})();
</script>
@endsection
