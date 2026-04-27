<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Applications table - compound indexes for common queries
        Schema::table('applications', function (Blueprint $table) {
            $table->index(['school_id', 'status'], 'applications_school_status_idx');
            $table->index(['school_id', 'intake_id'], 'applications_school_intake_idx');
            $table->index(['status', 'reviewed_at'], 'applications_status_reviewed_idx');
            $table->index(['school_id', 'created_at'], 'applications_school_created_idx');
        });

        // Students table
        Schema::table('students', function (Blueprint $table) {
            $table->index(['school_id'], 'students_school_idx');
            $table->index(['id_number'], 'students_id_number_idx');
            $table->index(['school_id', 'first_name', 'last_name'], 'students_school_name_idx');
        });

        // Documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->index(['school_id'], 'documents_school_idx');
            $table->index(['application_id', 'status'], 'documents_app_status_idx');
            $table->index(['status'], 'documents_status_idx');
        });

        // Payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['school_id', 'status', 'paid_at'], 'payments_school_status_date_idx');
            $table->index(['application_id', 'status'], 'payments_app_status_idx');
            $table->index(['paid_at'], 'payments_paid_at_idx');
        });

        // Audit logs table
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['school_id'], 'audit_logs_school_idx');
            $table->index(['user_id', 'created_at'], 'audit_logs_user_created_idx');
        });

        // Inquiries table
        Schema::table('inquiries', function (Blueprint $table) {
            $table->index(['priority'], 'inquiries_priority_idx');
            $table->index(['status'], 'inquiries_status_idx');
        });

        // Inquiry replies table
        Schema::table('inquiry_replies', function (Blueprint $table) {
            $table->index(['inquiry_id'], 'inquiry_replies_inquiry_idx');
        });

        // Settings table
        Schema::table('settings', function (Blueprint $table) {
            $table->index(['school_id', 'key'], 'settings_school_key_idx');
        });

        // Application notes table
        Schema::table('application_notes', function (Blueprint $table) {
            $table->index(['application_id', 'is_internal'], 'app_notes_app_internal_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('applications_school_status_idx');
            $table->dropIndex('applications_school_intake_idx');
            $table->dropIndex('applications_status_reviewed_idx');
            $table->dropIndex('applications_school_created_idx');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_school_idx');
            $table->dropIndex('students_id_number_idx');
            $table->dropIndex('students_school_name_idx');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_school_idx');
            $table->dropIndex('documents_app_status_idx');
            $table->dropIndex('documents_status_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_school_status_date_idx');
            $table->dropIndex('payments_app_status_idx');
            $table->dropIndex('payments_paid_at_idx');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('audit_logs_school_idx');
            $table->dropIndex('audit_logs_user_created_idx');
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropIndex('inquiries_priority_idx');
            $table->dropIndex('inquiries_status_idx');
        });

        Schema::table('inquiry_replies', function (Blueprint $table) {
            $table->dropIndex('inquiry_replies_inquiry_idx');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex('settings_school_key_idx');
        });

        Schema::table('application_notes', function (Blueprint $table) {
            $table->dropIndex('app_notes_app_internal_idx');
        });
    }
};
