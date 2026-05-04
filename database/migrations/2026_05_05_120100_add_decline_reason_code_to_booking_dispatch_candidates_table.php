<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_dispatch_candidates', function (Blueprint $table) {
            $table->string('decline_reason_code', 64)->nullable()->after('responded_at');
        });
    }

    public function down(): void
    {
        Schema::table('booking_dispatch_candidates', function (Blueprint $table) {
            $table->dropColumn('decline_reason_code');
        });
    }
};
