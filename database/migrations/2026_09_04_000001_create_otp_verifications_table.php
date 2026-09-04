<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 15);
            $table->string('otp_hash');
            $table->enum('purpose', ['register', 'forgot_password']);

            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');

            $table->timestamp('verified_at')->nullable();
            $table->timestamp('consumed_at')->nullable();

            // Issued only after successful OTP verification. Proves the exact
            // phone number completed OTP verification for this purpose.
            $table->string('verification_token_hash')->nullable();
            $table->timestamp('verification_token_expires_at')->nullable();

            $table->timestamp('last_sent_at')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['phone', 'purpose']);
            $table->index('verification_token_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
