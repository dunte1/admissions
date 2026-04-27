<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('role', 50)->default('student');
            $table->boolean('is_active')->default(true);
            $table->string('verification_token', 64)->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('preferred_language', 10)->default('en');
            $table->boolean('dark_mode')->default(false);
            $table->string('photo')->nullable();
            $table->json('user_settings')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->index(['email', 'role']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
