<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AICrawlJob extends Model
{
    use HasFactory;
    
    protected $table = 'ai_crawl_jobs';

    protected $fillable = [
        'school_id',
        'url',
        'status',
        'pages_crawled',
        'items_indexed',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function start()
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    public function complete($pagesCrawled, $itemsIndexed)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'pages_crawled' => $pagesCrawled,
            'items_indexed' => $itemsIndexed,
        ]);
    }

    public function fail($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }
}
