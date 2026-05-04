<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_dispatch_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->unsignedInteger('attempt_no');
            $table->unsignedInteger('search_radius_meters');
            $table->timestamp('broadcast_started_at');
            $table->timestamp('broadcast_expires_at');
            $table->unsignedInteger('candidate_count')->default(0);
            $table->foreignId('winner_driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->string('status', 20);
            $table->timestamps();

            $table->unique(['booking_id', 'attempt_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_dispatch_attempts');
    }
};
