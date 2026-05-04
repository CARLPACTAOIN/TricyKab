<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('availability_status', 20)->default('OFFLINE')->after('status');
            $table->timestamp('last_availability_at')->nullable()->after('availability_status');
            $table->decimal('last_latitude', 10, 7)->nullable()->after('last_availability_at');
            $table->decimal('last_longitude', 10, 7)->nullable()->after('last_latitude');
            $table->decimal('last_accuracy_meters', 10, 2)->nullable()->after('last_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn([
                'availability_status',
                'last_availability_at',
                'last_latitude',
                'last_longitude',
                'last_accuracy_meters',
            ]);
        });
    }
};
