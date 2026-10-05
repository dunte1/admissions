<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Applications - compound indexes for common queries
        Schema::table('applications', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_applications_user_status');
            $table->index(['school_id', 'status', 'created_at'], 'idx_applications_school_status_date');
            $table->index(['program_id', 'status'], 'idx_applications_program_status');
            $table->index(['intake_id', 'status'], 'idx_applications_intake_status');
        });

        // Payments - compound indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['application_id', 'status'], 'idx_payments_application_status');
            $table->index(['user_id', 'status'], 'idx_payments_user_status');
            $table->index(['school_id', 'status', 'paid_at'], 'idx_payments_school_status_date');
            $table->index(['payment_type', 'status'], 'idx_payments_type_status');
            $table->index(['idempotency_key', 'created_at'], 'idx_payments_idempotency');
        });

        // Documents
        Schema::table('documents', function (Blueprint $table) {
            $table->index(['application_id', 'status'], 'idx_documents_application_status');
            $table->index(['user_id', 'status'], 'idx_documents_user_status');
            $table->index(['school_id', 'status'], 'idx_documents_school_status');
        });

        // Activity Logs
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_activity_user_date');
            $table->index(['action', 'created_at'], 'idx_activity_action_date');
        });

        // Audit Logs
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['auditable_type', 'auditable_id'], 'idx_audit_subject');
            $table->index(['school_id', 'created_at'], 'idx_audit_school_date');
        });

        // Conversations & Messages
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['sender_id', 'created_at'], 'idx_messages_sender_date');
            $table->index(['conversation_id', 'read_at'], 'idx_messages_conversation_read');
        });

        // Broadcasts
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->index(['school_id', 'status', 'created_at'], 'idx_broadcasts_school_status');
        });

        // AI Conversations
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->index(['school_id', 'status'], 'idx_ai_conversations_school_status');
            $table->index(['session_id'], 'idx_ai_conversations_session');
        });

        // Leads
        Schema::table('ai_leads', function (Blueprint $table) {
            $table->index(['school_id', 'status'], 'idx_ai_leads_school_status');
            $table->index(['email'], 'idx_ai_leads_email');
        });

        // Notifications
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_id', 'read_at'], 'idx_notifications_user_read');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('idx_applications_user_status');
            $table->dropIndex('idx_applications_school_status_date');
            $table->dropIndex('idx_applications_program_status');
            $table->dropIndex('idx_applications_intake_status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_application_status');
            $table->dropIndex('idx_payments_user_status');
            $table->dropIndex('idx_payments_school_status_date');
            $table->dropIndex('idx_payments_type_status');
            $table->dropIndex('idx_payments_idempotency');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('idx_documents_application_status');
            $table->dropIndex('idx_documents_user_status');
            $table->dropIndex('idx_documents_school_status');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_activity_user_date');
            $table->dropIndex('idx_activity_action_date');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('idx_audit_subject');
            $table->dropIndex('idx_audit_school_date');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('idx_messages_sender_date');
            $table->dropIndex('idx_messages_conversation_read');
        });

        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropIndex('idx_broadcasts_school_status');
        });

        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->dropIndex('idx_ai_conversations_school_status');
            $table->dropIndex('idx_ai_conversations_session');
        });

        Schema::table('ai_leads', function (Blueprint $table) {
            $table->dropIndex('idx_ai_leads_school_status');
            $table->dropIndex('idx_ai_leads_email');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_user_read');
        });
    }
};
