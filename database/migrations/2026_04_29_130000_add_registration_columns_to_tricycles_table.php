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
        Schema::table('tricycles', function (Blueprint $table) {
            $table->string('registration_status')->default('ACTIVE')->after('status');
            $table->unsignedTinyInteger('capacity')->default(4)->after('registration_status');
            $table->index('registration_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tricycles', function (Blueprint $table) {
            $table->dropIndex(['registration_status']);
            $table->dropColumn(['registration_status', 'capacity']);
        });
    }
};
