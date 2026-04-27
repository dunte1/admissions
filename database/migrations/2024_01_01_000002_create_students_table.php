<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('id_number', 20)->nullable();
            $table->string('passport_number', 20)->nullable();
            $table->string('nationality', 50)->default('Kenyan');
            $table->string('county', 50)->nullable();
            $table->string('sub_county', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 50)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('alt_phone', 20)->nullable();
            $table->date('birth_certificate')->nullable();
            $table->enum('disability_status', ['none', 'partial', 'full'])->default('none');
            $table->text('disability_description')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single');
            $table->string('religion', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
