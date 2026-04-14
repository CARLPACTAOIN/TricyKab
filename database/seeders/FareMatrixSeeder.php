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
            'multiplier' => 1.00,
            'min_fare' => 0.00,
            'max_fare' => 999.00,
            'effective_date' => now(),
        ]);
    }
}
