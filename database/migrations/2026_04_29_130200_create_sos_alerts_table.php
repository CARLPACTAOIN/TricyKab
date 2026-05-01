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
        Schema::create('sos_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('trip_id')->nullable();
            $table->foreignId('passenger_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('passenger_name')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_note')->nullable();
            $table->string('status')->default('OPEN');
            $table->foreignId('acknowledged_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('closed_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sos_alerts');
    }
};
