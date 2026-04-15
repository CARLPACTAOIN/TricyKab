<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_reference')->nullable()->after('id');
            $table->foreignId('origin_barangay_id')->nullable()->after('destination_address')->constrained('barangays')->nullOnDelete();
            $table->foreignId('destination_barangay_id')->nullable()->after('origin_barangay_id')->constrained('barangays')->nullOnDelete();
        });

        $barangays = DB::table('barangays')->pluck('id', 'code');
        $bookings = DB::table('bookings')->select('id', 'created_at', 'pickup_address', 'destination_address')->orderBy('id')->get();

        foreach ($bookings as $booking) {
            DB::table('bookings')
                ->where('id', $booking->id)
                ->update([
                    'booking_reference' => sprintf(
                        'BK-%s-%04d',
                        date('Y', strtotime((string) ($booking->created_at ?? now()))),
                        $booking->id
                    ),
                    'origin_barangay_id' => $this->resolveBarangayId($booking->pickup_address, $barangays),
                    'destination_barangay_id' => $this->resolveBarangayId($booking->destination_address, $barangays),
                ]);
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->unique('booking_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['booking_reference']);
            $table->dropConstrainedForeignId('destination_barangay_id');
            $table->dropConstrainedForeignId('origin_barangay_id');
            $table->dropColumn('booking_reference');
        });
    }

    private function resolveBarangayId(?string $address, \Illuminate\Support\Collection $barangays): ?int
    {
        if (! $address) {
            return null;
        }

        $normalized = strtolower($address);

        return match (true) {
            str_contains($normalized, 'nongnongan') => $barangays['NONGNONGAN'] ?? null,
            str_contains($normalized, 'osias') => $barangays['OSIAS'] ?? null,
            str_contains($normalized, 'bangilan') => $barangays['BANGILAN'] ?? null,
            str_contains($normalized, 'sangsang') => $barangays['SANGSANG'] ?? null,
            str_contains($normalized, 'usm') => $barangays['OSIAS'] ?? null,
            default => $barangays['POBLACION'] ?? null,
        };
    }
};
