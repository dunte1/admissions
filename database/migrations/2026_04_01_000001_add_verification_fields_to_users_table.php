<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('email_verified_at');
            $table->boolean('is_first_login')->default(true)->after('is_verified');
            $table->timestamp('last_verified_at')->nullable()->after('is_first_login');
            $table->string('phone_verification_token', 64)->nullable()->after('last_verified_at');
            $table->timestamp('phone_verified_at')->nullable()->after('phone_verification_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_verified',
                'is_first_login',
                'last_verified_at',
                'phone_verification_token',
                'phone_verified_at',
            ]);
        });
    }
};