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
        Schema::create('tricycles', function (Blueprint $table) {
            $table->id();
            $table->string('body_number')->unique();
            $table->string('plate_number')->unique();
            $table->foreignId('toda_id')->constrained()->cascadeOnDelete();
            $table->string('make_model')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tricycles');
    }
};
