<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PRD §7.14 — adds rating column to trips for passenger→driver ratings.
 * PRD §7.5 — rating_avg is already on drivers (decimal 'rating' column).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            if (! Schema::hasColumn('trips', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('gps_quality_status');
            }
            if (! Schema::hasColumn('trips', 'rated_at')) {
                $table->dateTime('rated_at')->nullable()->after('rating');
            }
        });

        // Add fcm_token to users for Phase 5 FCM push notifications
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'fcm_token')) {
                $table->string('fcm_token', 255)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumnIfExists('rating');
            $table->dropColumnIfExists('rated_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('fcm_token');
        });
    }
};
