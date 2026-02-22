<?php

namespace Database\Seeders;

use App\Models\FareMatrix;
use Illuminate\Database\Seeder;

class FareMatrixSeeder extends Seeder
{
    public function run(): void
    {
        FareMatrix::create([
            'base_fare' => 15.00,
            'per_km_rate' => 2.00,
            'minimum_distance' => 2.00,
            'discount_percentage' => 20.00,
            'rush_hour_surcharge' => 0.00,
            'night_diff_percentage' => 0.00,
            'effective_date' => now(),
        ]);
    }
}
