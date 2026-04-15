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
        Schema::create('barangays', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->timestamps();
        });

        $timestamp = now();

        DB::table('barangays')->insert([
            ['name' => 'Poblacion', 'code' => 'POBLACION', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Osias', 'code' => 'OSIAS', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Nongnongan', 'code' => 'NONGNONGAN', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Bangilan', 'code' => 'BANGILAN', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Sangsang', 'code' => 'SANGSANG', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangays');
    }
};
