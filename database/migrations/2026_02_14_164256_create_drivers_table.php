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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('license_number')->unique();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->string('photo')->nullable(); // Profile photo path
            $table->decimal('rating', 3, 2)->default(5.00); // Average rating (1.00-5.00)
            $table->string('status')->default('active'); // active, inactive
            $table->foreignId('tricycle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('toda_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
