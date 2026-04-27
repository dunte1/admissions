<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event');
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['payment_id', 'event']);
            $table->index(['school_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('application_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('payments', 'idempotency_key')) {
                $table->string('idempotency_key', 64)->nullable()->unique()->after('paypal_transaction_id');
            }
            if (!Schema::hasColumn('payments', 'initiated_at')) {
                $table->timestamp('initiated_at')->nullable()->after('paid_at');
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
                $table->string('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
        
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
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
        });
    }
};
