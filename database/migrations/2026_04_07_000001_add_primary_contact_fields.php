<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('students', 'primary_phone')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('primary_phone', 20)->nullable()->after('phone');
                $table->string('primary_email', 100)->nullable()->after('postal_code');
                $table->string('country', 50)->default('Kenya')->after('nationality');
                $table->string('ward', 100)->nullable()->after('sub_county');
            });
        }

        if (!Schema::hasColumn('students', 'guardian_id_number')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('guardian_id_number', 30)->nullable()->after('alt_phone');
            });
        }

        if (!Schema::hasColumn('applications', 'intake_id')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->foreignId('intake_id')->nullable()->constrained('intakes')->onDelete('set null')->after('program_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'primary_phone')) {
                $table->dropColumn(['primary_phone', 'primary_email', 'country', 'ward', 'guardian_id_number']);
            }
        });

        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'intake_id')) {
                $table->dropForeign(['intake_id']);
                $table->dropColumn(['intake_id']);
            }
        });
    }
};