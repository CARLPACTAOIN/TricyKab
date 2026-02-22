<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-7">
        <h6 class="font-medium text-defaulttextcolor mb-3">{{ $label }} Fare Settings</h6>
        <form action="{{ route('fares.store') }}" method="POST">
            @csrf
            <input type="hidden" name="ride_type" value="{{ $rideType }}">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="base_fare_{{ $rideType }}" class="ti-form-label">Base Fare (₱)</label>
                        <input type="number" step="0.25" class="ti-form-input" id="base_fare_{{ $rideType }}" name="base_fare" value="{{ old('base_fare', $matrix->base_fare ?? 10) }}">
                    </div>
                    <div>
                        <label for="minimum_distance_{{ $rideType }}" class="ti-form-label">Base Distance (km)</label>
                        <input type="number" step="0.1" class="ti-form-input" id="minimum_distance_{{ $rideType }}" name="minimum_distance" value="{{ old('minimum_distance', $matrix->minimum_distance ?? 2) }}">
                    </div>
                </div>

                <div>
                    <label for="per_km_rate_{{ $rideType }}" class="ti-form-label">Rate per Succeeding KM (₱)</label>
                    <input type="number" step="0.25" class="ti-form-input" id="per_km_rate_{{ $rideType }}" name="per_km_rate" value="{{ old('per_km_rate', $matrix->per_km_rate ?? 2) }}">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="discount_percentage_{{ $rideType }}" class="ti-form-label">Discount (%)</label>
                        <div class="relative">
                            <input type="number" step="1" class="ti-form-input ltr:pr-8 rtl:pl-8" id="discount_percentage_{{ $rideType }}" name="discount_percentage" value="{{ old('discount_percentage', $matrix->discount_percentage ?? 20) }}">
                            <div class="absolute inset-y-0 ltr:right-0 rtl:left-0 flex items-center px-3 pointer-events-none text-gray-500">%</div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Applied for Students, PWDs, Senior Citizens</p>
                    </div>
                    <div>
                        <label for="per_passenger_addon_{{ $rideType }}" class="ti-form-label">Per-Passenger Add-on (₱)</label>
                        <input type="number" step="0.25" class="ti-form-input" id="per_passenger_addon_{{ $rideType }}" name="per_passenger_addon" value="{{ old('per_passenger_addon', $matrix->per_passenger_addon ?? 0) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="effective_date_{{ $rideType }}" class="ti-form-label">Effective Date</label>
                        <input type="date" class="ti-form-input" id="effective_date_{{ $rideType }}" name="effective_date" value="{{ old('effective_date', $matrix->effective_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-200 my-4"></div>
                <h6 class="font-medium text-gray-700 mb-2">Advanced Surcharges</h6>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="rush_hour_surcharge_{{ $rideType }}" class="ti-form-label">Rush Hour Surcharge (₱)</label>
                        <input type="number" step="1" class="ti-form-input" id="rush_hour_surcharge_{{ $rideType }}" name="rush_hour_surcharge" value="{{ old('rush_hour_surcharge', $matrix->rush_hour_surcharge ?? 0) }}">
                    </div>
                    <div>
                        <label for="night_diff_percentage_{{ $rideType }}" class="ti-form-label">Night Differential (%)</label>
                        <input type="number" step="1" class="ti-form-input" id="night_diff_percentage_{{ $rideType }}" name="night_diff_percentage" value="{{ old('night_diff_percentage', $matrix->night_diff_percentage ?? 0) }}">
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="ti-btn ti-btn-primary-full">Save {{ $label }} Fare</button>
            </div>
        </form>
    </div>

    <!-- Preview Panel -->
    <div class="col-span-12 xl:col-span-5">
        <h6 class="font-medium text-defaulttextcolor mb-3">Fare Calculation Preview</h6>
        <div class="alert alert-info">
            <div class="flex items-center">
                <i class="ri-information-line text-lg me-2"></i>
                <span>Based on current {{ $label }} settings:</span>
            </div>
            <ul class="list-disc list-inside mt-2 space-y-1">
                <li>First <strong>{{ $matrix->minimum_distance ?? 2 }} km</strong> costs <strong>₱{{ number_format($matrix->base_fare ?? 10, 2) }}</strong></li>
                <li>Succeeding km costs <strong>₱{{ number_format($matrix->per_km_rate ?? 2, 2) }}/km</strong></li>
                <li>Per-passenger add-on: <strong>₱{{ number_format($matrix->per_passenger_addon ?? 0, 2) }}</strong></li>
                <li>Discount (PWD/Senior/Student): <strong>{{ $matrix->discount_percentage ?? 20 }}%</strong></li>
            </ul>
        </div>
        @if($matrix && $matrix->effective_date)
            <p class="text-xs text-textmuted mt-2">
                <i class="ri-calendar-line me-1"></i> Effective since {{ $matrix->effective_date->format('M d, Y') }}
            </p>
        @endif
    </div>
</div>
