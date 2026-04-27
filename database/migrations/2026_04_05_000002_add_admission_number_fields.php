<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('admission_prefix', 20)->nullable()->after('slug');
            $table->string('admission_suffix', 20)->nullable()->after('admission_prefix');
            $table->boolean('admission_include_year')->default(true)->after('admission_suffix');
            $table->boolean('admission_include_program_code')->default(true)->after('admission_include_year');
            $table->integer('admission_number_padding')->default(4)->after('admission_include_program_code');
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->string('program_code', 20)->nullable()->after('short_name');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('admission_number', 50)->nullable()->unique()->after('user_id');
            $table->timestamp('admission_number_generated_at')->nullable()->after('admission_number');
            $table->index(['school_id', 'admission_number']);
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['admission_prefix', 'admission_suffix', 'admission_include_year', 'admission_include_program_code', 'admission_number_padding']);
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('program_code');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['admission_number', 'admission_number_generated_at']);
        });
    }
};