<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Toda;
use Illuminate\Support\Facades\Hash;

class TodaAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $todas = Toda::all();

        foreach ($todas as $toda) {
            $identifier = strtolower(str_replace(' ', '', $toda->name));
            
            User::firstOrCreate(
                ['email' => "admin_{$identifier}@tricykab.local"],
                [
                    'name' => "{$toda->name} Admin",
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'status' => 'ACTIVE',
                    'toda_id' => $toda->id,
                    'phone' => '+63917' . rand(1000000, 9999999), // unique dummy phone
                ]
            );
        }
    }
}
