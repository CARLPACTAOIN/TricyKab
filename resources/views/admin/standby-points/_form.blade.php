@php
    /** @var \App\Models\StandbyPoint|null $standbyPoint */
    $editing = isset($standbyPoint) && $standbyPoint?->exists;
    $initLat  = old('latitude',  $standbyPoint->latitude  ?? 7.114);
    $initLng  = old('longitude', $standbyPoint->longitude ?? 124.836);
    $initRadius = old('radius_meters', $standbyPoint->radius_meters ?? 50);
    
    $mapPayload = [
        'center' => ['lat' => (float)$initLat, 'lng' => (float)$initLng],
        'zoom' => 15,
        'lat' => (float)$initLat,
        'lng' => (float)$initLng,
        'radius' => (int)$initRadius
    ];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- ── Left column: fields ── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Name <span class="text-rose-600">*</span></label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $standbyPoint->name ?? '') }}"
                class="mt-1 w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary @error('name') border-rose-400 @enderror"
                placeholder="e.g., Kabacan Public Market"
                required
            >
            @error('name')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- TODA + Barangay --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="toda_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">TODA</label>
                <div class="relative mt-1">
                    <select
                        id="toda_id"
                        name="toda_id"
                        class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none @error('toda_id') border-rose-400 @enderror"
                    >
                        <option value="">Global (no TODA)</option>
                        @foreach($todas as $toda)
                            <option value="{{ $toda->id }}" @selected((string) old('toda_id', $standbyPoint->toda_id ?? '') === (string) $toda->id)>{{ $toda->name }}</option>
                        @endforeach
                    </select>
                    <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                </div>
                @error('toda_id')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="barangay_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Barangay</label>
                <div class="relative mt-1">
                    <select
                        id="barangay_id"
                        name="barangay_id"
                        class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none @error('barangay_id') border-rose-400 @enderror"
                    >
                        <option value="">—</option>
                        @foreach($barangays as $barangay)
                            <option value="{{ $barangay->id }}" @selected((string) old('barangay_id', $standbyPoint->barangay_id ?? '') === (string) $barangay->id)>{{ $barangay->name }}</option>
                        @endforeach
                    </select>
                    <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                </div>
                @error('barangay_id')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ── Map Picker ── --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                    Location <span class="text-rose-600">*</span>
                    <span class="ml-2 text-xs font-normal text-slate-400">Click the map or drag the marker to set coordinates</span>
                </label>
                <button
                    type="button"
                    id="btn-use-my-location"
                    class="flex items-center gap-1 text-xs text-primary hover:text-primary/80 font-medium transition-colors"
                    title="Use my current location"
                >
                    <span class="material-icons-outlined text-sm">my_location</span>
                    Use my location
                </button>
            </div>

            {{-- Search box --}}
            <div class="relative">
                <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
                <input
                    id="map-search-input"
                    type="text"
                    placeholder="Search for a place (e.g., Kabacan Public Market)…"
                    class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary"
                    autocomplete="off"
                >
                <div
                    id="map-search-results"
                    class="hidden absolute top-full left-0 right-0 z-[9999] mt-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl overflow-hidden"
                ></div>
            </div>

            {{-- Map canvas --}}
            <div
                id="standby-picker-root"
                class="relative h-80 rounded-xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 shadow-sm"
                data-map-context="standby-point-picker"
                data-map-payload='@json($mapPayload)'
                style="z-index:0"
            >
                <div data-map-canvas class="h-full w-full"></div>
                {{-- "Drop pin" hint overlay shown before first interaction --}}
                <div id="picker-hint" class="pointer-events-none absolute bottom-3 left-1/2 -translate-x-1/2 z-[500] px-3 py-1.5 bg-slate-900/70 text-white text-xs rounded-full backdrop-blur-sm whitespace-nowrap">
                    <span class="material-icons-outlined text-xs align-middle mr-1">touch_app</span>Click anywhere on the map to place the pin
                </div>
            </div>

            {{-- Coordinate read-only display + hidden inputs --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="latitude" class="block text-xs font-medium text-slate-500 mb-1">Latitude <span class="text-rose-500">*</span></label>
                    <input
                        id="latitude"
                        name="latitude"
                        type="number"
                        step="0.0000001"
                        value="{{ $initLat }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-mono text-slate-600 dark:text-slate-300 focus:ring-primary focus:border-primary @error('latitude') border-rose-400 @enderror"
                        placeholder="7.1234567"
                        required
                    >
                    @error('latitude')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="longitude" class="block text-xs font-medium text-slate-500 mb-1">Longitude <span class="text-rose-500">*</span></label>
                    <input
                        id="longitude"
                        name="longitude"
                        type="number"
                        step="0.0000001"
                        value="{{ $initLng }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-mono text-slate-600 dark:text-slate-300 focus:ring-primary focus:border-primary @error('longitude') border-rose-400 @enderror"
                        placeholder="124.1234567"
                        required
                    >
                    @error('longitude')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

    </div>

    {{-- ── Right column: geofence + actions ── --}}
    <div class="space-y-5">
        <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-white">Geofence</h3>
            <p class="mt-1 text-xs text-slate-500">Radius and prioritization affect standby scoring.</p>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="radius_meters" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Radius (meters) <span class="text-rose-600">*</span></label>
                    <input
                        id="radius_meters"
                        name="radius_meters"
                        type="number"
                        min="1"
                        max="2000"
                        value="{{ $initRadius }}"
                        class="mt-1 w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary @error('radius_meters') border-rose-400 @enderror"
                        required
                    >
                    @error('radius_meters')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority_weight" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Priority Weight <span class="text-rose-600">*</span></label>
                    <input
                        id="priority_weight"
                        name="priority_weight"
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        value="{{ old('priority_weight', $standbyPoint->priority_weight ?? 1) }}"
                        class="mt-1 w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary @error('priority_weight') border-rose-400 @enderror"
                        required
                    >
                    @error('priority_weight')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Status <span class="text-rose-600">*</span></label>
                    <div class="relative mt-1">
                        <select
                            id="status"
                            name="status"
                            class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm appearance-none @error('status') border-rose-400 @enderror"
                            required
                        >
                            @foreach(['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive'] as $value => $label)
                                <option value="{{ $value }}" @selected((string) old('status', $standbyPoint->status ?? 'ACTIVE') === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-medium shadow-md">
                {{ $editing ? 'Save Changes' : 'Create Standby Point' }}
            </button>
            <a href="{{ route('admin.standby-points') }}" class="px-5 py-2.5 rounded-lg font-medium border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>
