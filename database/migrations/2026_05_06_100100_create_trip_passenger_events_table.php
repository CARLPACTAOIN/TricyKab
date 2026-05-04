<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_passenger_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->string('event_type', 30);
            $table->unsignedTinyInteger('quantity');
            $table->string('notes', 255)->nullable();
            $table->foreignId('recorded_by_driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_passenger_events');
    }
};
