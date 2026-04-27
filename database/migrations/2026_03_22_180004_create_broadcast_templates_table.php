<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('subject')->nullable();
            $table->text('content');
            $table->enum('type', ['in_app', 'email', 'sms', 'all'])->default('in_app');
            $table->string('category')->nullable();
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['school_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_templates');
    }
};
