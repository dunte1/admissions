<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('letter_number')->unique();
            $table->enum('type', ['admission', 'rejection', 'provisional', 'deferral', 'calling']);
            $table->enum('status', ['draft', 'sent', 'acknowledged', 'accepted', 'declined']);
            $table->date('issue_date');
            $table->date('response_deadline')->nullable();
            $table->date('responded_at')->nullable();
            $table->text('additional_conditions')->nullable();
            $table->text('remarks')->nullable();
            $table->string('pdf_path')->nullable();
            
            // Additional fields for calling letter
            $table->datetime('interview_date')->nullable();
            $table->string('interview_venue')->nullable();
            $table->text('interview_instructions')->nullable();
            
            $table->timestamps();
            
            $table->index('status');
            $table->index(['application_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_letters');
    }
};