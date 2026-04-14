<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Toda;
use App\Models\Tricycle;
use App\Models\Driver;
use App\Models\FareMatrix;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Admin User ---
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@tricykab.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // --- TODAs (Kabacan, Cotabato) ---
        $todaPoblacion = Toda::create([
            'name' => 'Poblacion TODA',
            'area_coverage' => 'Brgy. Poblacion, Kabacan Market Area',
            'operating_hours' => '5:00 AM - 10:00 PM',
            'status' => 'active',
        ]);

        $todaOsias = Toda::create([
            'name' => 'Osias TODA',
            'area_coverage' => 'Brgy. Osias, USM Campus Area',
            'operating_hours' => '6:00 AM - 9:00 PM',
            'status' => 'active',
        ]);

        $todaNongnongan = Toda::create([
            'name' => 'Nongnongan TODA',
            'area_coverage' => 'Brgy. Nongnongan, Highway Area',
            'operating_hours' => '5:30 AM - 9:00 PM',
            'status' => 'active',
        ]);

        $todaBangilan = Toda::create([
            'name' => 'Bangilan TODA',
            'area_coverage' => 'Brgy. Bangilan, Rural Route',
            'operating_hours' => '6:00 AM - 8:00 PM',
            'status' => 'active',
        ]);

        $todaSangsang = Toda::create([
            'name' => 'Sangsang TODA',
            'area_coverage' => 'Brgy. Sangsang, Agricultural Area',
            'operating_hours' => '6:00 AM - 7:00 PM',
            'status' => 'inactive',
        ]);

        // --- Tricycles ---
        $tricycles = [];
        $todaList = [$todaPoblacion, $todaOsias, $todaNongnongan, $todaBangilan];
        $counter = 1;

        foreach ($todaList as $toda) {
            for ($i = 0; $i < 5; $i++) {
                $tricycles[] = Tricycle::create([
                    'body_number' => 'KB-' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                    'plate_number' => 'TC-' . str_pad($counter, 4, '0', STR_PAD_LEFT),
                    'toda_id' => $toda->id,
                    'make_model' => collect(['Honda TMX 125', 'Kawasaki Barako', 'Honda XRM 125', 'Yamaha YTX'])->random(),
                    'status' => $counter <= 18 ? 'active' : 'maintenance',
                ]);
                $counter++;
            }
        }

        // --- Drivers ---
        $firstNames = ['Juan', 'Pedro', 'Jose', 'Mario', 'Roberto', 'Eduardo', 'Ricardo', 'Antonio', 'Fernando', 'Carlos',
                        'Miguel', 'Rafael', 'Manuel', 'Andres', 'Francisco', 'Ernesto', 'Danilo', 'Romeo', 'Ruben', 'Oscar'];
        $lastNames = ['Dela Cruz', 'Santos', 'Reyes', 'Cruz', 'Bautista', 'Gonzales', 'Lopez', 'Garcia', 'Mendoza', 'Torres',
                      'Rivera', 'Flores', 'Ramos', 'Villanueva', 'Castro', 'Martinez', 'Morales', 'Aquino', 'Navarro', 'Peña'];

        foreach ($tricycles as $index => $tricycle) {
            if ($index >= 18) continue; // Only assign drivers to active tricycles

            Driver::create([
                'first_name' => $firstNames[$index],
                'last_name' => $lastNames[$index],
                'license_number' => 'N12-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                'contact_number' => '09' . rand(10, 99) . '-' . rand(100, 999) . '-' . rand(1000, 9999),
                'address' => 'Kabacan, Cotabato',
                'rating' => round(rand(35, 50) / 10, 2),
                'status' => 'active',
                'tricycle_id' => $tricycle->id,
                'toda_id' => $tricycle->toda_id,
            ]);
        }

        // Two unassigned drivers
        Driver::create([
            'first_name' => 'Rolando',
            'last_name' => 'Delos Reyes',
            'license_number' => 'N12-21-654321',
            'contact_number' => '0912-345-6789',
            'address' => 'Kabacan, Cotabato',
            'rating' => 4.20,
            'status' => 'inactive',
            'tricycle_id' => null,
            'toda_id' => $todaPoblacion->id,
        ]);

        // --- Fare Matrices ---
        FareMatrix::create([
            'ride_type' => 'shared',
            'base_fare' => 15.00,
            'per_km_rate' => 2.50,
            'multiplier' => 1.00,
            'min_fare' => 0.00,
            'max_fare' => 999.00,
            'effective_date' => '2026-02-01',
        ]);

        FareMatrix::create([
            'ride_type' => 'special',
            'base_fare' => 50.00,
            'per_km_rate' => 5.00,
            'multiplier' => 1.50,
            'min_fare' => 0.00,
            'max_fare' => 999.00,
            'effective_date' => '2026-02-01',
        ]);

        // --- Sample Passenger ---
        $passenger = User::create([
            'name' => 'Maria Clara',
            'email' => 'maria@example.com',
            'password' => bcrypt('password'),
            'role' => 'passenger',
            'phone' => '0917-123-4567',
        ]);

        // --- Sample Bookings ---
        $sampleDriver = Driver::first();

        Booking::create([
            'passenger_id' => $passenger->id,
            'driver_id' => $sampleDriver->id,
            'tricycle_id' => $sampleDriver->tricycle_id,
            'pickup_address' => 'Kabacan Public Market',
            'pickup_lat' => 7.1083,
            'pickup_lng' => 124.8295,
            'destination_address' => 'USM Kabacan',
            'destination_lat' => 7.1117,
            'destination_lng' => 124.8419,
            'ride_type' => 'shared',
            'status' => 'COMPLETED',
            'fare_amount' => 20.00,
            'distance_km' => 3.50,
            'accepted_at' => now()->subHours(3),
            'started_at' => now()->subHours(3)->addMinutes(5),
            'completed_at' => now()->subHours(3)->addMinutes(20),
        ]);

        Booking::create([
            'passenger_id' => $passenger->id,
            'driver_id' => $sampleDriver->id,
            'tricycle_id' => $sampleDriver->tricycle_id,
            'pickup_address' => 'USM Kabacan',
            'pickup_lat' => 7.1117,
            'pickup_lng' => 124.8419,
            'destination_address' => 'Kabacan Bus Terminal',
            'destination_lat' => 7.1060,
            'destination_lng' => 124.8270,
            'ride_type' => 'shared',
            'status' => 'COMPLETED',
            'fare_amount' => 18.00,
            'distance_km' => 2.80,
            'accepted_at' => now()->subHours(1),
            'started_at' => now()->subHours(1)->addMinutes(4),
            'completed_at' => now()->subHours(1)->addMinutes(18),
        ]);

        Booking::create([
            'passenger_id' => $passenger->id,
            'driver_id' => null,
            'tricycle_id' => null,
            'pickup_address' => 'Kabacan Public Market',
            'pickup_lat' => 7.1083,
            'pickup_lng' => 124.8295,
            'destination_address' => 'Brgy. Nongnongan',
            'destination_lat' => 7.0950,
            'destination_lng' => 124.8150,
            'ride_type' => 'special',
            'status' => 'SEARCHING_DRIVER',
            'fare_amount' => 80.00,
            'distance_km' => 5.20,
        ]);

        // Create payment for completed bookings
        $completedBookings = Booking::where('status', 'COMPLETED')->get();
        foreach ($completedBookings as $booking) {
            Payment::create([
                'booking_id' => $booking->id,
                'method' => 'cash',
                'amount' => $booking->fare_amount,
                'status' => 'completed',
            ]);
        }

        $this->command->info('✅ Database seeded with Kabacan sample data!');
        $this->command->info('   Admin: admin@tricykab.test / password');
    }
}
