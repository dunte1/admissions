<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::create('students_temp', function ($table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('school_id')->nullable();
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('last_name');
                $table->date('date_of_birth')->nullable();
                $table->string('gender')->nullable();
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
                $table->string('disability_status')->default('none');
                $table->text('disability_description')->nullable();
                $table->string('marital_status')->default('single');
                $table->string('religion', 50)->nullable();
                $table->timestamps();
            });
            
            DB::statement('INSERT INTO students_temp SELECT * FROM students');
            Schema::drop('students');
            Schema::rename('students_temp', 'students');
        } else {
            Schema::table('students', function ($table) {
                $table->date('date_of_birth')->nullable()->change();
                $table->string('gender')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::create('students_old', function ($table) {
                $table->id();
                $table->foreignId('user_id');
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('last_name');
                $table->date('date_of_birth');
                $table->string('gender');
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
                $table->string('disability_status')->default('none');
                $table->text('disability_description')->nullable();
                $table->string('marital_status')->default('single');
                $table->string('religion', 50)->nullable();
                $table->timestamps();
            });
            
            DB::statement('INSERT INTO students_old SELECT * FROM students');
            Schema::drop('students');
            Schema::rename('students_old', 'students');
        } else {
            Schema::table('students', function ($table) {
                $table->date('date_of_birth')->change();
                $table->string('gender')->change();
            });
        }
    }
};
