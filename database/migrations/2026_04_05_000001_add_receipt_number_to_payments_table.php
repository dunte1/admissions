<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'receipt_number')) {
                $table->string('receipt_number', 50)->nullable()->unique()->after('transaction_id');
                $table->index('receipt_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'receipt_number')) {
                $table->dropIndex(['receipt_number']);
                $table->dropColumn('receipt_number');
            }
        });
    }
};