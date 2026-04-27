<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->text('header_content')->nullable();
            $table->text('body_content')->nullable();
            $table->text('footer_content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->unique(['school_id', 'type']);
            $table->index('type');
        });

        Schema::create('letter_template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_template_id')->constrained('letter_templates')->cascadeOnDelete();
            $table->integer('version');
            $table->text('header_content')->nullable();
            $table->text('body_content')->nullable();
            $table->text('footer_content')->nullable();
            $table->text('change_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at');
            
            $table->unique(['letter_template_id', 'version']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_template_versions');
        Schema::dropIfExists('letter_templates');
    }
};