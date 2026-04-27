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
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedBigInteger('program_choice_2')->nullable()->after('program_id');
            $table->unsignedBigInteger('program_choice_3')->nullable()->after('program_choice_2');
            
            $table->foreign('program_choice_2')->references('id')->on('programs')->onDelete('set null');
            $table->foreign('program_choice_3')->references('id')->on('programs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            //
        });
    }
};
