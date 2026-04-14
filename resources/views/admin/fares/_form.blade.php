<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-7">
        <h6 class="font-medium text-defaulttextcolor mb-3">{{ $label }} Fare Rules</h6>
        <form action="{{ route('fares.store') }}" method="POST">
            @csrf
            <input type="hidden" name="ride_type" value="{{ $rideType }}">
            <input type="hidden" name="effective_date" value="{{ now()->format('Y-m-d') }}">
            @if($rideType === 'shared')
                <input type="hidden" name="multiplier" value="1">
            @endif
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="base_fare_{{ $rideType }}" class="ti-form-label">Base Fare (₱)</label>
                        <input type="number" step="0.25" class="ti-form-input" id="base_fare_{{ $rideType }}" name="base_fare" value="{{ old('base_fare', $matrix->base_fare ?? 10) }}">
                    </div>
                    <div>
                        <label for="per_km_rate_{{ $rideType }}" class="ti-form-label">Per Km Rate (₱)</label>
                        <input type="number" step="0.25" class="ti-form-input" id="per_km_rate_{{ $rideType }}" name="per_km_rate" value="{{ old('per_km_rate', $matrix->per_km_rate ?? 2) }}">
                    </div>
                </div>

                @if($rideType === 'special')
                    <div>
                        <label for="multiplier_{{ $rideType }}" class="ti-form-label">Multiplier</label>
                        <input type="number" step="0.1" class="ti-form-input" id="multiplier_{{ $rideType }}" name="multiplier" value="{{ old('multiplier', $matrix->multiplier ?? 1.5) }}">
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="min_fare_{{ $rideType }}" class="ti-form-label">Min Fare (₱)</label>
                        <input type="number" step="0.25" class="ti-form-input" id="min_fare_{{ $rideType }}" name="min_fare" value="{{ old('min_fare', $matrix->min_fare ?? 0) }}">
                    </div>
                    <div>
                        <label for="max_fare_{{ $rideType }}" class="ti-form-label">Max Fare (₱)</label>
                        <input type="number" step="0.25" class="ti-form-input" id="max_fare_{{ $rideType }}" name="max_fare" value="{{ old('max_fare', $matrix->max_fare ?? 999) }}">
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="ti-btn ti-btn-primary-full">Save {{ $label }} Fare Rule</button>
            </div>
        </form>
    </div>

    <div class="col-span-12 xl:col-span-5">
        <h6 class="font-medium text-defaulttextcolor mb-3">Fare Calculation Preview</h6>
        <div class="alert alert-info">
            <div class="flex items-center">
                <i class="ri-information-line text-lg me-2"></i>
                <span>Based on current {{ $label }} settings:</span>
            </div>
            <ul class="list-disc list-inside mt-2 space-y-1">
                <li>Base fare: <strong>₱{{ number_format($matrix->base_fare ?? 10, 2) }}</strong></li>
                <li>Per km rate: <strong>₱{{ number_format($matrix->per_km_rate ?? 2, 2) }}/km</strong></li>
                @if($rideType === 'special')
                    <li>Multiplier: <strong>{{ number_format($matrix->multiplier ?? 1.5, 1) }}x</strong></li>
                @endif
                <li>Bounds: <strong>₱{{ number_format($matrix->min_fare ?? 0, 2) }}</strong> to <strong>₱{{ number_format($matrix->max_fare ?? 999, 2) }}</strong></li>
            </ul>
        </div>
        @if($matrix && $matrix->effective_date)
            <p class="text-xs text-textmuted mt-2">
                <i class="ri-calendar-line me-1"></i> Effective since {{ $matrix->effective_date->format('M d, Y') }}
            </p>
        @endif
    </div>
</div>
