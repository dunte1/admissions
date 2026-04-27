<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('ai_icon');
            $table->json('allowed_domains')->nullable()->after('embed_script');
            $table->string('default_language')->default('en')->after('allowed_domains');
        });
    }

    public function down(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'allowed_domains', 'default_language']);
        });
    }
};