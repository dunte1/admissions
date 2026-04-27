<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('initiator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->string('subject')->nullable();
            $table->enum('status', ['active', 'archived', 'blocked'])->default('active');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'last_message_at']);
            $table->index(['initiator_id', 'recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
