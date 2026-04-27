<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('feature', 100);
            $table->enum('status', ['active', 'paused', 'disabled'])->default('disabled');
            $table->json('metadata')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'feature']);
            $table->index(['school_id', 'status']);
            $table->index('feature');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_features');
    }
};