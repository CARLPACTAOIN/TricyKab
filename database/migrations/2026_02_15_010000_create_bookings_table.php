<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passenger_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('tricycle_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('pickup_lat', 10, 7)->nullable();
            $table->decimal('pickup_lng', 10, 7)->nullable();
            $table->string('pickup_address')->nullable();
            $table->decimal('destination_lat', 10, 7)->nullable();
            $table->decimal('destination_lng', 10, 7)->nullable();
            $table->string('destination_address')->nullable();
            $table->string('ride_type')->default('shared'); // shared, special, cargo
            $table->string('status')->default('pending');
            // Status flow: pending → driver_assigned → in_progress → completed
            // Also: cancelled_by_passenger, cancelled_by_driver, cancelled_no_driver
            $table->decimal('fare_amount', 8, 2)->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
