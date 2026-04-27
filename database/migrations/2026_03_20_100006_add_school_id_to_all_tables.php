<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });

        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });

        Schema::table('intakes', function (Blueprint $table) {
            if (!Schema::hasColumn('intakes', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });

        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
                $table->index('school_id');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });

        Schema::table('admission_letters', function (Blueprint $table) {
            if (!Schema::hasColumn('admission_letters', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
                $table->index('school_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('intakes', function (Blueprint $table) {
            if (Schema::hasColumn('intakes', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('intakes', function (Blueprint $table) {
            if (Schema::hasColumn('intakes', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('intakes', function (Blueprint $table) {
            if (Schema::hasColumn('intakes', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('admission_letters', function (Blueprint $table) {
            if (Schema::hasColumn('admission_letters', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('admission_letters', function (Blueprint $table) {
            if (Schema::hasColumn('admission_letters', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('admission_letters', function (Blueprint $table) {
            if (Schema::hasColumn('admission_letters', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'school_id')) {
                $table->dropForeign(['school_id']);
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'school_id')) {
                $table->dropIndex(['school_id']);
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });
    }
};