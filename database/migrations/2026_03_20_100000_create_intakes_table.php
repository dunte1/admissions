<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intakes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->year('year')->nullable();
            $table->enum('semester', ['1', '2', '3'])->nullable();
            $table->date('application_start_date')->nullable();
            $table->date('application_end_date')->nullable();
            $table->date('review_start_date')->nullable();
            $table->date('review_end_date')->nullable();
            $table->date('results_release_date')->nullable();
            $table->date('registration_start_date')->nullable();
            $table->date('registration_end_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['year', 'semester']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intakes');
    }
};