<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('domain', 255)->unique();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('ssl_enabled')->default(false);
            $table->string('ssl_cert_path')->nullable();
            $table->string('ssl_key_path')->nullable();
            $table->timestamps();

            $table->index('school_id');
            $table->index('domain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_domains');
    }
};