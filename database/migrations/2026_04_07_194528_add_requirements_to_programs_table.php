<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('min_mean_grade')->nullable()->after('requirements');
            $table->json('subject_requirements')->nullable()->after('min_mean_grade');
            $table->string('alternative_qualification')->nullable()->after('subject_requirements');
            $table->string('duration_display')->nullable()->after('alternative_qualification');
            $table->string('certification_authority')->nullable()->after('duration_display');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'min_mean_grade',
                'subject_requirements',
                'alternative_qualification',
                'duration_display',
                'certification_authority',
            ]);
        });
    }
};