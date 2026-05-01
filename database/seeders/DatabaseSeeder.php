<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barangay;
use App\Models\Toda;
use App\Models\Tricycle;
use App\Models\Driver;
use App\Models\Dispute;
use App\Models\FareMatrix;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\SosAlert;
use App\Models\AuditLog;
use App\Models\StandbyPoint;
use Carbon\Carbon;
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
        $barangays = collect([
            'POBLACION' => Barangay::query()->firstOrCreate(
                ['code' => 'POBLACION'],
                ['name' => 'Poblacion']
            ),
            'OSIAS' => Barangay::query()->firstOrCreate(
                ['code' => 'OSIAS'],
                ['name' => 'Osias']
            ),
            'NONGNONGAN' => Barangay::query()->firstOrCreate(
                ['code' => 'NONGNONGAN'],
                ['name' => 'Nongnongan']
            ),
            'BANGILAN' => Barangay::query()->firstOrCreate(
                ['code' => 'BANGILAN'],
                ['name' => 'Bangilan']
            ),
            'SANGSANG' => Barangay::query()->firstOrCreate(
                ['code' => 'SANGSANG'],
                ['name' => 'Sangsang']
            ),
        ]);

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
                    'registration_status' => $counter <= 16 ? 'ACTIVE' : ($counter <= 18 ? 'PENDING' : 'EXPIRED'),
                    'capacity' => collect([3, 4, 4, 5])->random(),
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

        $locations = [
            'kabacan_public_market' => [
                'address' => 'Kabacan Public Market',
                'lat' => 7.1083,
                'lng' => 124.8295,
                'barangay_id' => $barangays['POBLACION']->id,
            ],
            'kabacan_bus_terminal' => [
                'address' => 'Kabacan Bus Terminal',
                'lat' => 7.1060,
                'lng' => 124.8270,
                'barangay_id' => $barangays['POBLACION']->id,
            ],
            'usm_main_gate' => [
                'address' => 'USM Main Gate',
                'lat' => 7.1117,
                'lng' => 124.8419,
                'barangay_id' => $barangays['OSIAS']->id,
            ],
            'poblacion_terminal' => [
                'address' => 'Poblacion Terminal',
                'lat' => 7.1260,
                'lng' => 124.8370,
                'barangay_id' => $barangays['POBLACION']->id,
            ],
            'osias_market' => [
                'address' => 'Osias Market',
                'lat' => 7.1180,
                'lng' => 124.8420,
                'barangay_id' => $barangays['OSIAS']->id,
            ],
            'nongnongan_barangay_hall' => [
                'address' => 'Nongnongan Barangay Hall',
                'lat' => 7.0950,
                'lng' => 124.8150,
                'barangay_id' => $barangays['NONGNONGAN']->id,
            ],
            'nongnongan_church' => [
                'address' => 'Nongnongan Church',
                'lat' => 7.1005,
                'lng' => 124.8215,
                'barangay_id' => $barangays['NONGNONGAN']->id,
            ],
            'kabacan_national_hs' => [
                'address' => 'Kabacan National High School',
                'lat' => 7.1135,
                'lng' => 124.8330,
                'barangay_id' => $barangays['POBLACION']->id,
            ],
            'usm_south_gate' => [
                'address' => 'USM South Gate',
                'lat' => 7.1145,
                'lng' => 124.8452,
                'barangay_id' => $barangays['OSIAS']->id,
            ],
            'osias_elementary_school' => [
                'address' => 'Osias Elementary School',
                'lat' => 7.1206,
                'lng' => 124.8468,
                'barangay_id' => $barangays['OSIAS']->id,
            ],
            'osias_barangay_hall' => [
                'address' => 'Osias Barangay Hall',
                'lat' => 7.1162,
                'lng' => 124.8435,
                'barangay_id' => $barangays['OSIAS']->id,
            ],
            'nongnongan_terminal' => [
                'address' => 'Nongnongan Terminal',
                'lat' => 7.0982,
                'lng' => 124.8194,
                'barangay_id' => $barangays['NONGNONGAN']->id,
            ],
        ];

        // --- Standby Points ---
        $standbyPoints = [
            ['name' => 'Poblacion Terminal', 'toda_id' => $todaPoblacion->id, 'barangay_id' => $barangays['POBLACION']->id, 'latitude' => 7.1260, 'longitude' => 124.8370, 'radius_meters' => 50, 'priority_weight' => 1.50, 'status' => 'ACTIVE'],
            ['name' => 'Osias Market Stop', 'toda_id' => $todaOsias->id, 'barangay_id' => $barangays['OSIAS']->id, 'latitude' => 7.1180, 'longitude' => 124.8420, 'radius_meters' => 40, 'priority_weight' => 1.20, 'status' => 'ACTIVE'],
            ['name' => 'USM Main Gate', 'toda_id' => $todaPoblacion->id, 'barangay_id' => $barangays['OSIAS']->id, 'latitude' => 7.1320, 'longitude' => 124.8510, 'radius_meters' => 60, 'priority_weight' => 1.80, 'status' => 'ACTIVE'],
            ['name' => 'Nongnongan Waiting Area', 'toda_id' => $todaNongnongan->id, 'barangay_id' => $barangays['NONGNONGAN']->id, 'latitude' => 7.1050, 'longitude' => 124.8280, 'radius_meters' => 50, 'priority_weight' => 1.00, 'status' => 'ACTIVE'],
            ['name' => 'Kabacan Bus Terminal', 'toda_id' => null, 'barangay_id' => $barangays['POBLACION']->id, 'latitude' => 7.1245, 'longitude' => 124.8395, 'radius_meters' => 75, 'priority_weight' => 2.00, 'status' => 'ACTIVE'],
            ['name' => 'Kabacan Public Market', 'toda_id' => null, 'barangay_id' => $barangays['POBLACION']->id, 'latitude' => 7.1234, 'longitude' => 124.8380, 'radius_meters' => 50, 'priority_weight' => 1.30, 'status' => 'ACTIVE'],
            ['name' => 'Sangsang Waiting Shed', 'toda_id' => $todaSangsang->id, 'barangay_id' => $barangays['SANGSANG']->id, 'latitude' => 7.0894, 'longitude' => 124.8010, 'radius_meters' => 45, 'priority_weight' => 0.90, 'status' => 'INACTIVE'],
        ];

        foreach ($standbyPoints as $standbyPoint) {
            StandbyPoint::create($standbyPoint);
        }

        // --- Sample Passengers ---
        $passengers = collect([
            ['name' => 'Maria Clara', 'email' => 'maria@example.com', 'phone' => '0917-123-4567'],
            ['name' => 'Juan Dela Cruz', 'email' => 'juan@example.com', 'phone' => '0917-555-1200'],
            ['name' => 'Amy Reyes', 'email' => 'amy@example.com', 'phone' => '0917-555-1201'],
            ['name' => 'Rosa Santos', 'email' => 'rosa@example.com', 'phone' => '0917-555-1202'],
            ['name' => 'Elena Torres', 'email' => 'elena@example.com', 'phone' => '0917-555-1203'],
            ['name' => 'Marco Villanueva', 'email' => 'marco@example.com', 'phone' => '0917-555-1204'],
            ['name' => 'Patricia Lim', 'email' => 'patricia@example.com', 'phone' => '0917-555-1205'],
            ['name' => 'Daniel Ocampo', 'email' => 'daniel@example.com', 'phone' => '0917-555-1206'],
            ['name' => 'Grace Bautista', 'email' => 'grace@example.com', 'phone' => '0917-555-1207'],
            ['name' => 'Leo Fernandez', 'email' => 'leo@example.com', 'phone' => '0917-555-1208'],
            ['name' => 'Nica Flores', 'email' => 'nica@example.com', 'phone' => '0917-555-1209'],
        ])->mapWithKeys(function ($passenger) {
            $user = User::create([
                'name' => $passenger['name'],
                'email' => $passenger['email'],
                'password' => bcrypt('password'),
                'role' => 'passenger',
                'phone' => $passenger['phone'],
            ]);

            return [$user->email => $user];
        });

        // --- Sample Bookings ---
        $drivers = Driver::with('tricycle')->get()->values();
        $bookingDefinitions = [
            ['passenger' => 'maria@example.com', 'pickup' => 'kabacan_public_market', 'destination' => 'usm_main_gate', 'ride_type' => 'shared', 'status' => Booking::STATUS_COMPLETED, 'fare' => 45.00, 'distance_km' => 3.20, 'driver_index' => 0, 'created_at' => Carbon::now()->subHours(3), 'accepted_after' => 8, 'started_after' => 13, 'completed_after' => 28],
            ['passenger' => 'juan@example.com', 'pickup' => 'poblacion_terminal', 'destination' => 'nongnongan_barangay_hall', 'ride_type' => 'special', 'status' => Booking::STATUS_DRIVER_ASSIGNED, 'fare' => 95.00, 'distance_km' => 5.80, 'driver_index' => 1, 'created_at' => Carbon::now()->subHours(2)->subMinutes(20), 'accepted_after' => 6],
            ['passenger' => 'amy@example.com', 'pickup' => 'osias_market', 'destination' => 'kabacan_public_market', 'ride_type' => 'shared', 'status' => Booking::STATUS_SEARCHING_DRIVER, 'fare' => 35.00, 'distance_km' => 2.80, 'driver_index' => null, 'created_at' => Carbon::now()->subHours(2)->subMinutes(5)],
            ['passenger' => 'rosa@example.com', 'pickup' => 'poblacion_terminal', 'destination' => 'osias_elementary_school', 'ride_type' => 'shared', 'status' => Booking::STATUS_TRIP_IN_PROGRESS, 'fare' => 40.00, 'distance_km' => 3.10, 'driver_index' => 2, 'created_at' => Carbon::now()->subHour()->subMinutes(40), 'accepted_after' => 5, 'started_after' => 15],
            ['passenger' => 'elena@example.com', 'pickup' => 'nongnongan_church', 'destination' => 'kabacan_bus_terminal', 'ride_type' => 'special', 'status' => Booking::STATUS_DRIVER_ON_THE_WAY, 'fare' => 110.00, 'distance_km' => 6.10, 'driver_index' => 3, 'created_at' => Carbon::now()->subHour()->subMinutes(55), 'accepted_after' => 4],
            ['passenger' => 'marco@example.com', 'pickup' => 'usm_south_gate', 'destination' => 'poblacion_terminal', 'ride_type' => 'shared', 'status' => Booking::STATUS_DRIVER_ARRIVED, 'fare' => 35.00, 'distance_km' => 2.60, 'driver_index' => 4, 'created_at' => Carbon::now()->subHours(5), 'accepted_after' => 5],
            ['passenger' => 'patricia@example.com', 'pickup' => 'kabacan_national_hs', 'destination' => 'osias_market', 'ride_type' => 'shared', 'status' => Booking::STATUS_CANCELLED_BY_PASSENGER, 'fare' => 35.00, 'distance_km' => 2.70, 'driver_index' => null, 'created_at' => Carbon::now()->subHours(6)],
            ['passenger' => 'daniel@example.com', 'pickup' => 'osias_barangay_hall', 'destination' => 'usm_main_gate', 'ride_type' => 'special', 'status' => Booking::STATUS_CANCELLED_BY_DRIVER, 'fare' => 80.00, 'distance_km' => 3.40, 'driver_index' => 5, 'created_at' => Carbon::now()->subDay()->subHours(2), 'accepted_after' => 7],
            ['passenger' => 'grace@example.com', 'pickup' => 'nongnongan_terminal', 'destination' => 'poblacion_terminal', 'ride_type' => 'shared', 'status' => Booking::STATUS_CANCELLED_NO_DRIVER, 'fare' => 40.00, 'distance_km' => 4.00, 'driver_index' => null, 'created_at' => Carbon::now()->subDay()->subHours(4)],
            ['passenger' => 'leo@example.com', 'pickup' => 'kabacan_public_market', 'destination' => 'nongnongan_church', 'ride_type' => 'shared', 'status' => Booking::STATUS_NO_SHOW_PASSENGER, 'fare' => 40.00, 'distance_km' => 4.30, 'driver_index' => 6, 'created_at' => Carbon::now()->subDay()->subHours(10), 'accepted_after' => 6],
            ['passenger' => 'nica@example.com', 'pickup' => 'osias_market', 'destination' => 'kabacan_bus_terminal', 'ride_type' => 'special', 'status' => Booking::STATUS_NO_SHOW_DRIVER, 'fare' => 90.00, 'distance_km' => 4.70, 'driver_index' => 7, 'created_at' => Carbon::now()->subDays(2)->subHours(2), 'accepted_after' => 5],
            ['passenger' => 'maria@example.com', 'pickup' => 'usm_main_gate', 'destination' => 'kabacan_bus_terminal', 'ride_type' => 'shared', 'status' => Booking::STATUS_COMPLETED, 'fare' => 38.00, 'distance_km' => 2.80, 'driver_index' => 8, 'created_at' => Carbon::now()->subDays(3)->subHours(1), 'accepted_after' => 4, 'started_after' => 9, 'completed_after' => 22],
        ];

        foreach ($bookingDefinitions as $definition) {
            $pickup = $locations[$definition['pickup']];
            $destination = $locations[$definition['destination']];
            $driver = $definition['driver_index'] !== null ? $drivers[$definition['driver_index']] ?? null : null;
            $createdAt = $definition['created_at'];

            $booking = Booking::create([
                'passenger_id' => $passengers[$definition['passenger']]->id,
                'driver_id' => $driver?->id,
                'tricycle_id' => $driver?->tricycle_id,
                'pickup_address' => $pickup['address'],
                'pickup_lat' => $pickup['lat'],
                'pickup_lng' => $pickup['lng'],
                'destination_address' => $destination['address'],
                'destination_lat' => $destination['lat'],
                'destination_lng' => $destination['lng'],
                'origin_barangay_id' => $pickup['barangay_id'],
                'destination_barangay_id' => $destination['barangay_id'],
                'ride_type' => $definition['ride_type'],
                'status' => $definition['status'],
                'fare_amount' => $definition['fare'],
                'distance_km' => $definition['distance_km'],
                'accepted_at' => isset($definition['accepted_after']) ? $createdAt->copy()->addMinutes($definition['accepted_after']) : null,
                'started_at' => isset($definition['started_after']) ? $createdAt->copy()->addMinutes($definition['started_after']) : null,
                'completed_at' => isset($definition['completed_after']) ? $createdAt->copy()->addMinutes($definition['completed_after']) : null,
                'cancelled_at' => str_starts_with($definition['status'], 'CANCELLED') || str_starts_with($definition['status'], 'NO_SHOW')
                    ? $createdAt->copy()->addMinutes(18)
                    : null,
                'created_at' => $createdAt,
                'updated_at' => isset($definition['completed_after'])
                    ? $createdAt->copy()->addMinutes($definition['completed_after'])
                    : $createdAt->copy()->addMinutes($definition['accepted_after'] ?? 5),
            ]);

            $booking->forceFill([
                'booking_reference' => Booking::makeBookingReference($booking->id, $booking->created_at),
            ])->saveQuietly();
        }

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

        // --- Disputes ---
        $bookingsWithDrivers = Booking::query()->whereNotNull('driver_id')->take(6)->get();
        foreach ($bookingsWithDrivers as $index => $booking) {
            Dispute::create([
                'booking_id' => $booking->id,
                'driver_id' => $booking->driver_id,
                'reported_by_role' => $index % 2 === 0 ? 'PASSENGER' : 'DRIVER',
                'reported_by_name' => $index % 2 === 0 ? 'Passenger Report '.($index + 1) : 'Driver Report '.($index + 1),
                'dispute_type' => collect(['FARE', 'NO_SHOW', 'GPS', 'CONDUCT'])->random(),
                'description' => 'Seeded dispute record for admin workflow testing.',
                'status' => collect(['OPEN', 'UNDER_REVIEW', 'RESOLVED', 'REJECTED'])->random(),
                'resolved_by_admin_id' => $admin->id,
                'resolved_at' => now()->subHours(rand(1, 48)),
            ]);
        }

        // --- SOS Alerts ---
        $sampleSosBookings = Booking::query()->take(4)->get();
        foreach ($sampleSosBookings as $index => $booking) {
            SosAlert::create([
                'booking_id' => $booking->id,
                'passenger_id' => $booking->passenger_id,
                'passenger_name' => $booking->passenger?->name,
                'latitude' => $booking->pickup_lat,
                'longitude' => $booking->pickup_lng,
                'location_note' => $booking->pickup_address,
                'status' => ['OPEN', 'ACKNOWLEDGED', 'CLOSED', 'CLOSED'][$index],
                'acknowledged_by_admin_id' => $index > 0 ? $admin->id : null,
                'acknowledged_at' => $index > 0 ? now()->subHours(3 + $index) : null,
                'closed_by_admin_id' => $index > 1 ? $admin->id : null,
                'closed_at' => $index > 1 ? now()->subHours(1 + $index) : null,
            ]);
        }

        // --- Audit Logs ---
        foreach (range(1, 12) as $i) {
            AuditLog::create([
                'actor_user_id' => $admin->id,
                'actor_type' => 'USER',
                'actor_name' => $admin->name,
                'object_type' => collect(['BOOKING', 'DISPUTE', 'SOS_ALERT', 'DRIVER', 'TRICYCLE'])->random(),
                'object_id' => rand(1, 20),
                'action' => collect(['CREATE', 'UPDATE', 'DISPUTE_STATUS_UPDATED', 'SOS_STATUS_UPDATED', 'OVERRIDE'])->random(),
                'reason' => 'Seeded audit entry',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder',
                'created_at' => now()->subHours($i * 2),
                'updated_at' => now()->subHours($i * 2),
            ]);
        }

        $this->command->info('✅ Database seeded with Kabacan sample data!');
        $this->command->info('   Admin: admin@tricykab.test / password');
    }
}
