<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_dispatch_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_attempt_id')->constrained('booking_dispatch_attempts')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->unsignedInteger('rank_order');
            $table->unsignedInteger('distance_meters');
            $table->decimal('standby_score', 8, 4);
            $table->decimal('fairness_score', 8, 4);
            $table->decimal('total_score', 10, 4);
            $table->string('response_status', 20)->default('PENDING');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['dispatch_attempt_id', 'driver_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_dispatch_candidates');
    }
};
