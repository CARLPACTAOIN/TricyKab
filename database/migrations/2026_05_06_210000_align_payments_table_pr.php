<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->char('currency', 3)->default('PHP');
            $table->timestamp('paid_at')->nullable();
            $table->string('recorded_by_role', 20)->nullable();
            $table->string('notes', 500)->nullable();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::table('payments')->whereRaw('LOWER(status) = ?', ['completed'])->update(['status' => 'COMPLETED']);
            DB::table('payments')->whereRaw('LOWER(method) = ?', ['cash'])->update(['method' => 'CASH']);
        } else {
            DB::table('payments')->where('status', 'completed')->update(['status' => 'COMPLETED']);
            DB::table('payments')->where('method', 'cash')->update(['method' => 'CASH']);
        }

        DB::table('payments')->whereNull('paid_at')->update(['paid_at' => now()]);

        Schema::table('payments', function (Blueprint $table) {
            $table->unique('booking_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['booking_id']);
            $table->dropColumn(['currency', 'paid_at', 'recorded_by_role', 'notes']);
        });
    }
};
