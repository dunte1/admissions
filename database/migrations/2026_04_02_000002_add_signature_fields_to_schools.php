<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'signature_image')) {
                $table->string('signature_image')->nullable()->after('favicon');
            }
            if (!Schema::hasColumn('schools', 'seal_image')) {
                $table->string('seal_image')->nullable()->after('signature_image');
            }
            if (!Schema::hasColumn('schools', 'signatory_name')) {
                $table->string('signatory_name')->nullable()->after('seal_image');
            }
            if (!Schema::hasColumn('schools', 'signatory_title')) {
                $table->string('signatory_title')->nullable()->after('signatory_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'signature_image')) {
                $table->dropColumn('signature_image');
            }
            if (Schema::hasColumn('schools', 'seal_image')) {
                $table->dropColumn('seal_image');
            }
            if (Schema::hasColumn('schools', 'signatory_name')) {
                $table->dropColumn('signatory_name');
            }
            if (Schema::hasColumn('schools', 'signatory_title')) {
                $table->dropColumn('signatory_title');
            }
        });
    }
};