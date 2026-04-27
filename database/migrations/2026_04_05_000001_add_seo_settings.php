<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('settings', 'school_id')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('school_id')->nullable()->after('key')->index();
            });
        }
        
        try {
            $seoSettings = [
                'meta_title' => null,
                'meta_description' => null,
                'meta_keywords' => null,
                'og_title' => null,
                'og_description' => null,
                'og_image' => null,
                'twitter_card' => 'summary_large_image',
                'canonical_url' => null,
            ];

            foreach ($seoSettings as $key => $value) {
                $exists = \App\Models\Setting::withoutGlobalScope(\App\Scopes\SchoolScope::class)
                    ->where('key', $key)
                    ->whereNull('school_id')
                    ->exists();
                    
                if (!$exists) {
                    \App\Models\Setting::create([
                        'key' => $key,
                        'school_id' => null,
                        'value' => $value,
                        'group' => 'seo'
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Ignore if already exists
        }
    }

    public function down(): void
    {
    }
};
