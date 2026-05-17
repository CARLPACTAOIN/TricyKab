<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sos_alerts', function (Blueprint $table) {
            $table->string('reporter_role', 20)->default('PASSENGER')->after('passenger_name');
            $table->foreignId('driver_id')->nullable()->after('reporter_role')->constrained('drivers')->nullOnDelete();
            $table->string('driver_name')->nullable()->after('driver_id');
        });
    }

    public function down(): void
    {
        Schema::table('sos_alerts', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['reporter_role', 'driver_id', 'driver_name']);
        });
    }
};
