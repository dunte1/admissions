<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // email, sms
            $table->string('event'); // application_received, application_approved, application_rejected, payment_received, etc.
            $table->string('subject')->nullable();
            $table->text('body');
            $table->text('variables')->nullable(); // JSON of available variables
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['school_id', 'type', 'event']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
