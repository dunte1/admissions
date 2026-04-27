<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_type')) {
                $table->enum('payment_type', ['admission_fee', 'commitment_fee'])->default('admission_fee')->after('amount');
            }
            
            if (!Schema::hasColumn('payments', 'idempotency_key')) {
                $table->string('idempotency_key', 100)->nullable()->unique()->after('paypal_transaction_id');
            }
            
            if (!Schema::hasColumn('payments', 'initiated_at')) {
                $table->timestamp('initiated_at')->nullable()->after('idempotency_key');
            }
            
            if (!Schema::hasColumn('payments', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('initiated_at');
            }
            
            if (!Schema::hasColumn('payments', 'callback_received_at')) {
                $table->timestamp('callback_received_at')->nullable()->after('completed_at');
            }
            
            if (!Schema::hasColumn('payments', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('callback_received_at');
            }
            
            if (!Schema::hasColumn('payments', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            
            if (!Schema::hasColumn('payments', 'receipt_number')) {
                $table->string('receipt_number', 50)->nullable()->after('user_agent');
            }
            
            $table->index(['payment_type', 'status']);
            $table->index(['school_id', 'payment_type']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payments_payment_type_status_index', 'payments_school_id_payment_type_index']);
            
            if (Schema::hasColumn('payments', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
            if (Schema::hasColumn('payments', 'idempotency_key')) {
                $table->dropColumn('idempotency_key');
            }
            if (Schema::hasColumn('payments', 'initiated_at')) {
                $table->dropColumn('initiated_at');
            }
            if (Schema::hasColumn('payments', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            if (Schema::hasColumn('payments', 'callback_received_at')) {
                $table->dropColumn('callback_received_at');
            }
            if (Schema::hasColumn('payments', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
            if (Schema::hasColumn('payments', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
            if (Schema::hasColumn('payments', 'receipt_number')) {
                $table->dropColumn('receipt_number');
            }
        });
    }
};