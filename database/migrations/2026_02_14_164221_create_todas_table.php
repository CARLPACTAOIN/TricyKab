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
        Schema::create('todas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('area_coverage')->nullable(); // Barangay/area the TODA covers
            $table->string('operating_hours')->nullable(); // e.g. "5:00 AM - 10:00 PM"
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todas');
    }
};
