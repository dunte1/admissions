<?php

namespace App\Jobs;

use App\Models\AICrawlJob;
use App\Models\AIKnowledge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessWebCrawlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        public AICrawlJob $crawlJob
    ) {}

    public function handle(): void
    {
        $this->crawlJob->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            $url = $this->crawlJob->url;
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                throw new \Exception("Failed to fetch URL: " . $response->status());
            }

            $html = $response->body();
            $text = $this->extractText($html);

            $this->crawlJob->update([
                'pages_crawled' => 1,
            ]);

            $chunks = $this->chunkText($text, 1000);

            foreach ($chunks as $index => $chunk) {
                AIKnowledge::create([
                    'school_id' => $this->crawlJob->school_id,
                    'title' => "Crawled Content - Page " . ($index + 1),
                    'content' => $chunk,
                    'type' => 'crawled',
                    'source_url' => $url,
                    'source_type' => 'crawl',
                    'is_active' => true,
                    'tokens' => str_word_count($chunk),
                ]);

                $this->crawlJob->increment('items_indexed');
            }

            $this->crawlJob->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            Log::info("Web crawl completed", [
                'job_id' => $this->crawlJob->id,
                'url' => $url,
                'items_indexed' => $this->crawlJob->items_indexed,
            ]);

        } catch (\Exception $e) {
            $this->crawlJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Web crawl failed", [
                'job_id' => $this->crawlJob->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function extractText(string $html): string
    {
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<nav[^>]*>.*?<\/nav>/is', '', $html);
        $html = preg_replace('/<footer[^>]*>.*?<\/footer>/is', '', $html);
        $html = preg_replace('/<header[^>]*>.*?<\/header>/is', '', $html);
        $html = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $html);

        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }

    protected function chunkText(string $text, int $chunkSize): array
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', $text);
        $chunks = [];
        $currentChunk = '';

        foreach ($sentences as $sentence) {
            if (strlen($currentChunk) + strlen($sentence) > $chunkSize) {
                if ($currentChunk) {
                    $chunks[] = trim($currentChunk);
                }
                $currentChunk = $sentence;
            } else {
                $currentChunk .= ' ' . $sentence;
            }
        }

        if ($currentChunk) {
            $chunks[] = trim($currentChunk);
        }

        return array_filter($chunks);
    }
}
