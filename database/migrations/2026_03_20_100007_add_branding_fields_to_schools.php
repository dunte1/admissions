<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'primary_color')) {
                $table->string('primary_color')->default('#7C3AED')->after('description');
            }
            if (!Schema::hasColumn('schools', 'secondary_color')) {
                $table->string('secondary_color')->default('#10B981')->after('primary_color');
            }
            if (!Schema::hasColumn('schools', 'favicon')) {
                $table->string('favicon')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('schools', 'custom_footer_text')) {
                $table->text('custom_footer_text')->nullable()->after('favicon');
            }
            if (!Schema::hasColumn('schools', 'enable_white_label')) {
                $table->boolean('enable_white_label')->default(false)->after('custom_footer_text');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'type')) {
                $table->string('type')->default('string')->after('group');
            }
            if (!Schema::hasColumn('settings', 'description')) {
                $table->text('description')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'primary_color')) {
                $table->dropColumn('primary_color');
            }
            if (Schema::hasColumn('schools', 'secondary_color')) {
                $table->dropColumn('secondary_color');
            }
            if (Schema::hasColumn('schools', 'favicon')) {
                $table->dropColumn('favicon');
            }
            if (Schema::hasColumn('schools', 'custom_footer_text')) {
                $table->dropColumn('custom_footer_text');
            }
            if (Schema::hasColumn('schools', 'enable_white_label')) {
                $table->dropColumn('enable_white_label');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('settings', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};