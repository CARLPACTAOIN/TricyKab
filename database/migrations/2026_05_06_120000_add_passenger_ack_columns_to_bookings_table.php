<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('passenger_ack_pickup_at')->nullable()->after('cancellation_reason');
            $table->timestamp('passenger_ack_dropoff_at')->nullable()->after('passenger_ack_pickup_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['passenger_ack_pickup_at', 'passenger_ack_dropoff_at']);
        });
    }
};
