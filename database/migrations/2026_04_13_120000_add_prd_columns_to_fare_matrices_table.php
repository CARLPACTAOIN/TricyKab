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
        Schema::table('fare_matrices', function (Blueprint $table) {
            $table->decimal('multiplier', 8, 4)->default(1.0000)->after('per_km_rate');
            $table->decimal('min_fare', 8, 2)->default(0.00)->after('multiplier');
            $table->decimal('max_fare', 8, 2)->default(999999.99)->after('min_fare');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fare_matrices', function (Blueprint $table) {
            $table->dropColumn(['multiplier', 'min_fare', 'max_fare']);
        });
    }
};
