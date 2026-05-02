<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_challenges', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 20);
            $table->string('role_hint', 20)->nullable();
            $table->string('otp_hash', 255);
            $table->dateTime('expires_at');
            $table->unsignedTinyInteger('verify_attempts')->default(0);
            $table->unsignedTinyInteger('resend_count')->default(0);
            $table->dateTime('locked_at')->nullable();
            $table->dateTime('consumed_at')->nullable();
            $table->timestamps();

            $table->index(['phone_number', 'role_hint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_challenges');
    }
};
