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
        Schema::create('standby_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('toda_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('radius_meters')->default(50);
            $table->decimal('priority_weight', 5, 2)->default(1.00);
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });

        $barangays = DB::table('barangays')->pluck('id', 'code');
        $todas = DB::table('todas')->pluck('id', 'name');
        $timestamp = now();

        if ($barangays->isEmpty() || $todas->isEmpty()) {
            return;
        }

        DB::table('standby_points')->insert([
            ['name' => 'Poblacion Terminal', 'toda_id' => $todas['Poblacion TODA'] ?? null, 'barangay_id' => $barangays['POBLACION'] ?? null, 'latitude' => 7.1260, 'longitude' => 124.8370, 'radius_meters' => 50, 'priority_weight' => 1.50, 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Osias Market Stop', 'toda_id' => $todas['Osias TODA'] ?? null, 'barangay_id' => $barangays['OSIAS'] ?? null, 'latitude' => 7.1180, 'longitude' => 124.8420, 'radius_meters' => 40, 'priority_weight' => 1.20, 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'USM Main Gate', 'toda_id' => $todas['Poblacion TODA'] ?? null, 'barangay_id' => $barangays['OSIAS'] ?? null, 'latitude' => 7.1320, 'longitude' => 124.8510, 'radius_meters' => 60, 'priority_weight' => 1.80, 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Nongnongan Waiting Area', 'toda_id' => $todas['Nongnongan TODA'] ?? null, 'barangay_id' => $barangays['NONGNONGAN'] ?? null, 'latitude' => 7.1050, 'longitude' => 124.8280, 'radius_meters' => 50, 'priority_weight' => 1.00, 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Kabacan Bus Terminal', 'toda_id' => null, 'barangay_id' => $barangays['POBLACION'] ?? null, 'latitude' => 7.1245, 'longitude' => 124.8395, 'radius_meters' => 75, 'priority_weight' => 2.00, 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Kabacan Public Market', 'toda_id' => null, 'barangay_id' => $barangays['POBLACION'] ?? null, 'latitude' => 7.1234, 'longitude' => 124.8380, 'radius_meters' => 50, 'priority_weight' => 1.30, 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Sangsang Waiting Shed', 'toda_id' => $todas['Sangsang TODA'] ?? null, 'barangay_id' => $barangays['SANGSANG'] ?? null, 'latitude' => 7.0894, 'longitude' => 124.8010, 'radius_meters' => 45, 'priority_weight' => 0.90, 'status' => 'INACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standby_points');
    }
};
