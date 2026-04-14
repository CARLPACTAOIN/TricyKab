<?php

namespace App\Services;

use App\Models\FareMatrix;

class FareCalculatorService
{
    /**
     * Calculate shared ride fare estimate.
     */
    public function calculate(float $distanceInKm): array
    {
        $matrix = $this->resolveMatrix(FareMatrix::TYPE_SHARED);

        $rawFare = $matrix->base_fare + ($distanceInKm * $matrix->per_km_rate);
        $totalFare = $this->applyBounds($rawFare, $matrix);

        return [
            'type' => 'shared',
            'distance_km' => round($distanceInKm, 2),
            'base_fare' => round($matrix->base_fare, 2),
            'per_km_rate' => round($matrix->per_km_rate, 2),
            'min_fare' => round($matrix->min_fare, 2),
            'max_fare' => round($matrix->max_fare, 2),
            'total_fare' => round($totalFare, 2),
            'matrix_used' => $matrix->toArray(),
        ];
    }

    /**
     * Calculate suggested special ride fare estimate.
     */
    public function calculateSpecial(float $distanceInKm): array
    {
        $matrix = $this->resolveMatrix(FareMatrix::TYPE_SPECIAL);

        $rawFare = $matrix->base_fare + ($distanceInKm * $matrix->per_km_rate * $matrix->multiplier);
        $suggestedFare = $this->applyBounds($rawFare, $matrix);

        return [
            'type' => 'special',
            'distance_km' => round($distanceInKm, 2),
            'base_fare' => round($matrix->base_fare, 2),
            'per_km_rate' => round($matrix->per_km_rate, 2),
            'multiplier' => round($matrix->multiplier, 2),
            'min_fare' => round($matrix->min_fare, 2),
            'max_fare' => round($matrix->max_fare, 2),
            'suggested_fare' => round($suggestedFare, 2),
            'matrix_used' => $matrix->toArray(),
        ];
    }

    private function resolveMatrix(string $rideType): FareMatrix
    {
        $matrix = FareMatrix::where('ride_type', $rideType)
            ->where('effective_date', '<=', now())
            ->latest('effective_date')
            ->first();

        if ($matrix) {
            return $matrix;
        }

        return new FareMatrix([
            'ride_type' => $rideType,
            'base_fare' => $rideType === FareMatrix::TYPE_SPECIAL ? 50.00 : 15.00,
            'per_km_rate' => $rideType === FareMatrix::TYPE_SPECIAL ? 5.00 : 2.50,
            'multiplier' => $rideType === FareMatrix::TYPE_SPECIAL ? 1.5 : 1.0,
            'min_fare' => 0.00,
            'max_fare' => 9999.00,
        ]);
    }

    private function applyBounds(float $amount, FareMatrix $matrix): float
    {
        $min = $matrix->min_fare ?? 0.00;
        $max = $matrix->max_fare ?? $amount;

        return min(max($amount, $min), $max);
    }
}
