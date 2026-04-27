<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->string('ai_provider')->nullable()->after('mode');
            $table->string('ai_model')->nullable()->after('ai_provider');
            $table->integer('rate_limit_per_minute')->default(10)->after('ai_model');
            $table->integer('max_tokens')->default(500)->after('rate_limit_per_minute');
            $table->float('temperature')->default(0.7)->after('max_tokens');
        });
    }

    public function down(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn(['ai_provider', 'ai_model', 'rate_limit_per_minute', 'max_tokens', 'temperature']);
        });
    }
};
