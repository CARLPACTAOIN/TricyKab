<?php

namespace App\Services;

use App\Models\FareMatrix;

class FareCalculatorService
{
    /**
     * Calculate the fare based on distance and passenger type.
     *
     * @param float $distanceInKm
     * @param bool $isDiscounted (PWD, Senior, Student)
     * @return array
     */
    public function calculate(float $distanceInKm, bool $isDiscounted = false): array
    {
        // Get the effective fare matrix
        $matrix = FareMatrix::where('effective_date', '<=', now())
            ->latest('effective_date')
            ->first();

        if (!$matrix) {
            // Fallback default values if no matrix is set
            $matrix = new FareMatrix([
                'base_fare' => 10.00,
                'per_km_rate' => 2.00,
                'minimum_distance' => 2.00,
                'discount_percentage' => 20.00,
            ]);
        }

        // 1. Calculate Gross Fare
        $baseFare = $matrix->base_fare;
        $excessDistance = max(0, $distanceInKm - $matrix->minimum_distance);
        $distanceFare = $excessDistance * $matrix->per_km_rate;
        
        $grossFare = $baseFare + $distanceFare;

        // 2. Calculate Discount
        $discountAmount = 0.00;
        if ($isDiscounted) {
            $discountAmount = $grossFare * ($matrix->discount_percentage / 100);
        }

        // 3. Final Fare
        $totalFare = max(0, $grossFare - $discountAmount);

        return [
            'distance_km' => round($distanceInKm, 2),
            'base_fare' => $baseFare,
            'distance_fare' => round($distanceFare, 2),
            'gross_fare' => round($grossFare, 2),
            'is_discounted' => $isDiscounted,
            'discount_amount' => round($discountAmount, 2),
            'total_fare' => round($totalFare, 2), // Standard rounding to 2 decimal places
            'matrix_used' => $matrix->toArray(),
        ];
    }

    /**
     * Calculate a suggested special (Pakyaw) fare.
     * Usually double the standard fare or negotiated.
     *
     * @param float $distanceInKm
     * @return array
     */
    public function calculateSpecial(float $distanceInKm): array
    {
        $standard = $this->calculate($distanceInKm);
        
        // Example logic: Special trips might be 1.5x or 2x the standard fate
        // For now, we return the standard fare as a "Baseline" for negotiation
        $suggestedFare = $standard['gross_fare'] * 1.5; 

        return array_merge($standard, [
            'type' => 'special',
            'suggested_fare' => round($suggestedFare, 2),
            'note' => 'Suggested fare is 1.5x the standard rate. Final price is negotiated.',
        ]);
    }
}
