<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PRD §7.5 — adds acceptance_rate_snapshot and rating_avg to drivers.
 * PRD §7.21 — adds target_fields to audit_logs for richer change context.
 *
 * All columns are nullable additive additions — no existing data affected.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // PRD §7.5 — cached acceptance rate metric
            if (! Schema::hasColumn('drivers', 'acceptance_rate_snapshot')) {
                $table->decimal('acceptance_rate_snapshot', 5, 2)->nullable()->after('rating');
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            // PRD §7.21 — which specific fields changed during an override
            if (! Schema::hasColumn('audit_logs', 'target_fields')) {
                $table->json('target_fields')->nullable()->after('new_state_json');
            }
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumnIfExists('acceptance_rate_snapshot');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumnIfExists('target_fields');
        });
    }
};
