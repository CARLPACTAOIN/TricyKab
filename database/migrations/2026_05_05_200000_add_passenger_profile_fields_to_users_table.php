<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable()->after('phone');

            $table->string('first_name', 100)->nullable()->after('name');
            $table->string('last_name', 100)->nullable()->after('first_name');

            $table->string('home_address', 255)->nullable()->after('phone_verified_at');
            $table->string('emergency_contact_name', 200)->nullable()->after('home_address');
            $table->string('emergency_contact_phone', 32)->nullable()->after('emergency_contact_name');
            $table->string('profile_photo_url', 500)->nullable()->after('emergency_contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_verified_at',
                'first_name',
                'last_name',
                'home_address',
                'emergency_contact_name',
                'emergency_contact_phone',
                'profile_photo_url',
            ]);
        });
    }
};

