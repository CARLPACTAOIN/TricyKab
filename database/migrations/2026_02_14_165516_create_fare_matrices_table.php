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
        Schema::create('fare_matrices', function (Blueprint $table) {
            $table->id();
            $table->string('ride_type')->default('shared'); // shared, special
            $table->decimal('base_fare', 8, 2);
            $table->decimal('per_km_rate', 8, 2);
            $table->decimal('minimum_distance', 8, 2)->default(2.0);
            $table->decimal('discount_percentage', 5, 2)->default(20.0); // PWD/Senior/Student
            $table->decimal('per_passenger_addon', 8, 2)->default(0.00); // Per-passenger add-on
            $table->decimal('rush_hour_surcharge', 8, 2)->default(0.00);
            $table->decimal('night_diff_percentage', 5, 2)->default(0.00);
            $table->date('effective_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fare_matrices');
    }
};
