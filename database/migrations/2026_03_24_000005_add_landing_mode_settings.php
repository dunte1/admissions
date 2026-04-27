<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->string('landing_ai_name')->nullable()->after('ai_name');
            $table->string('landing_mode')->default('sales')->after('mode');
            $table->boolean('landing_enabled')->default(true)->after('landing_mode');
            $table->string('landing_welcome_message')->nullable()->after('landing_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn(['landing_ai_name', 'landing_mode', 'landing_enabled', 'landing_welcome_message']);
        });
    }
};
