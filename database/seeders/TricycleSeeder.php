<?php

namespace Database\Seeders;

use App\Models\Toda;
use App\Models\Tricycle;
use Illuminate\Database\Seeder;

class TricycleSeeder extends Seeder
{
    public function run(): void
    {
        $target = 10;
        $existing = (int) Tricycle::query()->count();
        $needed = max(0, $target - $existing);

        if ($needed === 0) {
            return;
        }

        /** @var Toda $toda */
        $toda = Toda::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->first()
            ?? Toda::query()->create([
                'name' => 'Default TODA',
                'area_coverage' => 'Kabacan (seed default)',
                'operating_hours' => '5:00 AM - 10:00 PM',
                'status' => 'active',
            ]);

        $start = $existing + 1;
        $end = $existing + $needed;

        for ($i = $start; $i <= $end; $i++) {
            Tricycle::query()->create([
                'body_number' => 'KB-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'plate_number' => 'TC-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'toda_id' => $toda->id,
                'make_model' => collect(['Honda TMX 125', 'Kawasaki Barako', 'Honda XRM 125', 'Yamaha YTX'])->random(),
                'status' => 'active',
                'registration_status' => 'ACTIVE',
                'capacity' => collect([3, 4, 4, 5])->random(),
            ]);
        }
    }
}

