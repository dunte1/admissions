<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('verified_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('admin_notes')->nullable()->after('rejection_reason');
            
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verified_by', 'verified_at', 'admin_notes']);
        });
    }
};