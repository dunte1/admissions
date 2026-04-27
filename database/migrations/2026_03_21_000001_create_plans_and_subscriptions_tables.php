<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('billing_period', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->integer('duration_days')->default(30);
            $table->integer('max_students')->nullable()->comment('null = unlimited');
            $table->integer('max_staff')->nullable()->comment('null = unlimited');
            $table->integer('max_programs')->nullable()->comment('null = unlimited');
            $table->integer('max_applications')->nullable()->comment('null = unlimited');
            $table->boolean('allow_document_upload')->default(true);
            $table->boolean('allow_payment_gateway')->default(true);
            $table->boolean('allow_custom_branding')->default(false);
            $table->boolean('allow_api_access')->default(false);
            $table->boolean('allow_priority_support')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('features')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->string('status')->default('active');
            $table->dateTime('starts_at');
            $table->dateTime('expires_at');
            $table->dateTime('cancelled_at')->nullable();
            $table->string('billing_cycles')->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['school_id', 'status']);
            $table->index(['expires_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
