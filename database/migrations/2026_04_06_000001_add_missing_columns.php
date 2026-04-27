<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('programs', 'short_name')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->string('short_name', 100)->nullable()->after('code');
            });
        }
        
        if (!Schema::hasColumn('programs', 'program_code')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->string('program_code', 20)->nullable()->after('short_name');
            });
        }

        if (!Schema::hasColumn('schools', 'admission_prefix')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->string('admission_prefix', 20)->nullable()->after('slug');
                $table->string('admission_suffix', 20)->nullable()->after('admission_prefix');
                $table->boolean('admission_include_year')->default(true)->after('admission_suffix');
                $table->boolean('admission_include_program_code')->default(true)->after('admission_include_year');
                $table->integer('admission_number_padding')->default(4)->after('admission_include_program_code');
            });
        }

        if (!Schema::hasColumn('students', 'admission_number')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('admission_number', 50)->nullable()->unique()->after('user_id');
                $table->timestamp('admission_number_generated_at')->nullable()->after('admission_number');
            });
        }
    }

    public function down(): void
    {
        // Keep columns - don't remove
    }
};