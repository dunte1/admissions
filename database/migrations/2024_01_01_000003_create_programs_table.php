<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->integer('duration_years')->default(4);
            $table->enum('level', ['certificate', 'diploma', 'degree', 'masters', 'phd'])->default('degree');
            $table->decimal('tuition_per_year', 12, 2)->default(0);
            $table->integer('capacity')->nullable();
            $table->integer('min_capacity')->nullable();
            $table->text('requirements')->nullable();
            $table->text('career_opportunities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
