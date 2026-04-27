<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('conversation_id')->nullable()->constrained('ai_conversations')->onDelete('set null');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('program_interest')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('new'); // new, contacted, converted, lost
            $table->string('source')->default('ai_chat');
            $table->timestamps();

            $table->index(['school_id', 'status']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_leads');
    }
};