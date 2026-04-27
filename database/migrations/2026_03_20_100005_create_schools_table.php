<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('logo')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('domain')->nullable()->unique();
            $table->string('timezone')->default('Africa/Nairobi');
            $table->string('currency')->default('KES');
            $table->string('currency_symbol')->default('KSh');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('domain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};