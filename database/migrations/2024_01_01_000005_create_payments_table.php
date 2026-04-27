<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['mpesa', 'paypal', 'visa', 'mastercard', 'bank_transfer']);
            $table->string('transaction_id', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('mpesa_receipt')->nullable();
            $table->string('paypal_transaction_id')->nullable();
            $table->timestamps();
            $table->index(['application_id', 'status']);
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
