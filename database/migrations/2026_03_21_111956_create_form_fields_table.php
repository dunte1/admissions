<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('fa-circle');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_section_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('key')->index();
            $table->string('type'); // text, email, tel, number, date, select, textarea, checkbox, radio, file
            $table->text('label');
            $table->text('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->text('options')->nullable(); // JSON for select/radio/checkbox options
            $table->string('validation')->nullable(); // required, email, min:3, etc.
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(false);
            $table->string('css_class')->nullable();
            $table->string('depends_on')->nullable(); // Field key this depends on
            $table->text('depends_value')->nullable(); // Value that triggers visibility
            $table->string('file_types')->nullable(); // For file inputs: pdf,jpg,png
            $table->integer('max_file_size')->nullable(); // In KB
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('form_sections');
    }
};
