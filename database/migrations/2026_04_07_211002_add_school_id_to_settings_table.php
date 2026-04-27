<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if unique constraint exists on key column
        $indexes = DB::select("PRAGMA index_list('settings')");
        $hasKeyUnique = false;
        
        foreach ($indexes as $index) {
            if (strpos($index->name, 'key') !== false) {
                $hasKeyUnique = true;
                // Drop the unique constraint on key
                DB::statement("DROP INDEX " . $index->name);
            }
        }

        // Add composite unique constraint on key + school_id
        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS settings_key_school_unique ON settings(key, school_id)");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS settings_key_school_unique");
    }
};