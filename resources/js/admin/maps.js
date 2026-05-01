import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

/* ── Fix 1: Leaflet default icon paths broken by Vite/bundlers ───────── */
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

/* ── Fix 2: Tailwind Preflight vs Leaflet tile rendering ─────────────
 * Tailwind's Preflight resets `img { max-width: 100% }` which collapses
 * OSM tiles to 0 × 0.  Injecting overrides here guarantees they load
 * alongside the Leaflet CSS regardless of build-tool CSS ordering.
 */
const styleId = '__leaflet-tw-fix';
if (!document.getElementById(styleId)) {
    const style = document.createElement('style');
    style.id = styleId;
    style.textContent = `
        .leaflet-container { font-family: inherit; z-index: 0; }
        .leaflet-container img,
        .leaflet-container svg { max-width: none !important; max-height: none !important; }
        .leaflet-container .leaflet-control-container { z-index: 1000; }
        [data-map-root] { min-height: 16rem; }
        [data-map-canvas].leaflet-container { height: 100%; min-height: 16rem; width: 100%; }
        .leaflet-tile-pane img { max-width: none !important; max-height: none !important; }
    `;
    document.head.appendChild(style);
}

const DEFAULT_CENTER = [7.114, 124.836];

/** leaflet.heat expects global `L` (UMD); ensure it exists before loading the plugin. */
let heatPluginLoaded = false;

async function ensureHeatPlugin() {
    if (typeof L.heatLayer === 'function') {
        return;
    }
    if (typeof window !== 'undefined') {
        window.L = L;
    }
    if (!heatPluginLoaded) {
        try {
            await import('leaflet.heat');
            heatPluginLoaded = true;
        } catch (e) {
            console.warn('[admin maps] leaflet.heat chunk failed to load; using circle fallback.', e);
        }
    }
}

function parsePayload(root) {
    try {
        return JSON.parse(root.dataset.mapPayload ?? '{}');
    } catch {
        return {};
    }
}

function setMapMessage(root, message, tone = 'neutral') {
    let messageEl = root.querySelector('[data-map-message]');

    if (!messageEl) {
        messageEl = document.createElement('div');
        messageEl.dataset.mapMessage = 'true';
        messageEl.className =
            'absolute inset-0 z-[500] flex items-center justify-center rounded-lg bg-slate-950/60 px-6 text-center text-sm font-medium backdrop-blur-[1px]';
        root.appendChild(messageEl);
    }

    const toneClasses = {
        neutral: 'text-slate-100',
        warning: 'text-amber-100',
        error: 'text-rose-100',
    };

    messageEl.className = `absolute inset-0 z-[500] flex items-center justify-center rounded-lg bg-slate-950/60 px-6 text-center text-sm font-medium backdrop-blur-[1px] ${toneClasses[tone] ?? toneClasses.neutral}`;
    messageEl.textContent = message;
}

function clearMapMessage(root) {
    root.querySelector('[data-map-message]')?.remove();
}

function mapCanvas(root) {
    return root.querySelector('[data-map-canvas]') ?? root;
}

/** Leaflet often measures 0×0 on first paint with flex/grid parents; re-measure after layout. */
function scheduleMapResize(map) {
    const run = () => {
        try {
            map.invalidateSize();
        } catch {
            /* ignore */
        }
    };
    requestAnimationFrame(() => {
        run();
        requestAnimationFrame(run);
    });
    setTimeout(run, 50);
    setTimeout(run, 250);
    if (typeof window !== 'undefined') {
        window.addEventListener('load', run, { once: true });
    }
}

function hasLatLng(p) {
    if (p == null || typeof p !== 'object') {
        return false;
    }
    const lat = Number(p.lat);
    const lng = Number(p.lng);
    return Number.isFinite(lat) && Number.isFinite(lng);
}

/** @param {L.Map} map */
function fitBoundsFromLatLngs(map, points, padding = 48) {
    if (!Array.isArray(points) || points.length === 0) {
        return;
    }

    const latlngs = points.map((p) => L.latLng(p.lat, p.lng));
    if (latlngs.length === 1) {
        map.setView(latlngs[0], 15);
        return;
    }

    const bounds = L.latLngBounds(latlngs);
    map.fitBounds(bounds, { padding: [padding, padding] });
}

function baseTileLayer() {
    return L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    });
}

function createLeafletMap(root, payload) {
    clearMapMessage(root);
    const canvas = mapCanvas(root);
    const center = payload.center
        ? [payload.center.lat, payload.center.lng]
        : DEFAULT_CENTER;
    const zoom = payload.zoom ?? 13;

    const map = L.map(canvas, {
        zoomControl: true,
        attributionControl: true,
    }).setView(center, zoom);

    baseTileLayer().addTo(map);

    map.whenReady(() => {
        scheduleMapResize(map);
    });

    return map;
}

async function initDashboardHeatmap(root) {
    await ensureHeatPlugin();

    const payload = parsePayload(root);
    const points = Array.isArray(payload.points) ? payload.points : [];
    const map = createLeafletMap(root, payload);

    if (!points.length) {
        setMapMessage(root, 'No booking coordinates in the current filter range.');
        return;
    }

    const heatPoints = points.map((p) => [p.lat, p.lng, p.weight ?? 0.5]);
    const gradient =
        payload.kind === 'destination'
            ? {
                  0.0: '#0ea5e9',
                  0.35: '#3b82f6',
                  0.55: '#6366f1',
                  0.75: '#a855f7',
                  1.0: '#f43f5e',
              }
            : {
                  0.0: '#22c55e',
                  0.35: '#10b981',
                  0.55: '#0ea5e9',
                  0.75: '#6366f1',
                  1.0: '#8b5cf6',
              };

    if (typeof L.heatLayer === 'function') {
        L.heatLayer(heatPoints, {
            radius: payload.radiusPixels ?? 45,
            blur: 22,
            maxZoom: 17,
            max: 1.0,
            gradient,
        }).addTo(map);
    } else {
        const fill = payload.kind === 'destination' ? '#3b82f6' : '#22c55e';
        points.forEach((p) => {
            L.circleMarker([p.lat, p.lng], {
                radius: 10,
                stroke: false,
                fillColor: fill,
                fillOpacity: 0.45,
            }).addTo(map);
        });
    }

    fitBoundsFromLatLngs(map, points);
    scheduleMapResize(map);
}

async function fetchOsrmRoute(coords) {
    const url = `https://router.project-osrm.org/route/v1/driving/${coords.lng1},${coords.lat1};${coords.lng2},${coords.lat2}?overview=full&geometries=geojson`;
    const res = await fetch(url);
    if (!res.ok) {
        throw new Error('OSRM request failed');
    }
    const data = await res.json();
    const route = data.routes?.[0];
    if (!route?.geometry?.coordinates?.length) {
        throw new Error('No route geometry');
    }
    return route.geometry;
}

async function initBookingRouteMap(root) {
    const payload = parsePayload(root);
    const pickup = payload.pickup;
    const destination = payload.destination;

    if (!hasLatLng(pickup) || !hasLatLng(destination)) {
        setMapMessage(root, 'This booking does not have enough coordinates to render a route.', 'warning');
        return;
    }

    const midpoint = {
        lat: (pickup.lat + destination.lat) / 2,
        lng: (pickup.lng + destination.lng) / 2,
    };

    const map = createLeafletMap(root, { center: midpoint, zoom: payload.zoom ?? 13 });

    L.circleMarker([pickup.lat, pickup.lng], {
        radius: 10,
        color: '#ffffff',
        weight: 2,
        fillColor: '#22c55e',
        fillOpacity: 1,
    })
        .addTo(map)
        .bindTooltip('Pickup', { permanent: false });

    L.circleMarker([destination.lat, destination.lng], {
        radius: 10,
        color: '#ffffff',
        weight: 2,
        fillColor: '#f43f5e',
        fillOpacity: 1,
    })
        .addTo(map)
        .bindTooltip('Destination', { permanent: false });

    const boundsPoints = [pickup, destination];

    try {
        const geometry = await fetchOsrmRoute({
            lat1: pickup.lat,
            lng1: pickup.lng,
            lat2: destination.lat,
            lng2: destination.lng,
        });

        const routeLayer = L.geoJSON(
            {
                type: 'Feature',
                geometry,
            },
            {
                style: {
                    color: '#0f766e',
                    weight: 5,
                    opacity: 0.95,
                },
            },
        ).addTo(map);

        map.fitBounds(routeLayer.getBounds(), { padding: [48, 48] });
    } catch {
        L.polyline(
            [
                [pickup.lat, pickup.lng],
                [destination.lat, destination.lng],
            ],
            {
                color: '#0f766e',
                weight: 5,
                opacity: 0.95,
            },
        ).addTo(map);

        fitBoundsFromLatLngs(map, boundsPoints);
        setMapMessage(
            root,
            'Showing straight-line fallback (routing service unavailable or returned no path).',
            'warning',
        );
    }

    scheduleMapResize(map);
}

function initStandbyPointMap(root) {
    const payload = parsePayload(root);
    const points = Array.isArray(payload.points) ? payload.points : [];
    const map = createLeafletMap(root, payload);

    if (!points.length) {
        setMapMessage(root, 'No standby points match the current filters.');
        return;
    }

    points.forEach((point) => {
        const isActive = point.status === 'ACTIVE';
        const color = isActive ? '#2563eb' : '#f59e0b';

        L.circleMarker([point.lat, point.lng], {
            radius: 8,
            color: '#ffffff',
            weight: 2,
            fillColor: color,
            fillOpacity: 1,
        })
            .addTo(map)
            .bindTooltip(point.name ?? 'Standby point', { permanent: false });

        L.circle([point.lat, point.lng], {
            radius: point.radiusMeters ?? 50,
            color,
            weight: 2,
            opacity: 0.9,
            fillColor: color,
            fillOpacity: isActive ? 0.14 : 0.08,
        }).addTo(map);
    });

    fitBoundsFromLatLngs(map, points);
    scheduleMapResize(map);
}

async function initMapRoot(root) {
    const context = root.dataset.mapContext;

    if (context === 'dashboard-heatmap') {
        await initDashboardHeatmap(root);
        return;
    }

    if (context === 'booking-route') {
        await initBookingRouteMap(root);
        return;
    }

    if (context === 'standby-points') {
        initStandbyPointMap(root);
    }
}

async function initAdminMaps() {
    const roots = [...document.querySelectorAll('[data-map-context]')];

    if (!roots.length) {
        return;
    }

    for (const root of roots) {
        try {
            await initMapRoot(root);
        } catch {
            setMapMessage(root, 'The map could not be loaded for this panel.', 'error');
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminMaps);
} else {
    initAdminMaps();
}
