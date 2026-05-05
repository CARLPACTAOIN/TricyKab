<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // If the DB already contains duplicate phone numbers (common in dev),
        // keep the earliest record and null out subsequent duplicates so we can
        // safely enforce uniqueness going forward.
        $duplicates = DB::table('users')
            ->select('phone', DB::raw('COUNT(*) as c'))
            ->whereNotNull('phone')
            ->groupBy('phone')
            ->having('c', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            $phone = $dup->phone;
            if (! is_string($phone) || $phone === '') {
                continue;
            }

            $keepId = DB::table('users')
                ->where('phone', $phone)
                ->min('id');

            DB::table('users')
                ->where('phone', $phone)
                ->where('id', '!=', $keepId)
                ->update(['phone' => null]);
        }

        Schema::table('users', function (Blueprint $table) {
            // Ensures a phone number cannot be reused across any role.
            // Multiple NULLs are still allowed by MySQL/Postgres.
            $table->unique('phone', 'users_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_phone_unique');
        });
    }
};

