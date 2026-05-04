<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_location_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy_meters', 10, 2)->nullable();
            $table->decimal('speed_mps', 8, 2)->nullable();
            $table->decimal('heading_degrees', 8, 2)->nullable();
            $table->timestamp('captured_at');
            $table->string('source', 20)->default('DRIVER_APP');
            $table->timestamps();

            $table->index(['trip_id', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_location_logs');
    }
};
