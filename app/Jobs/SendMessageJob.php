<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Conversation;
use App\Events\NewMessageEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $messageId;
    public int $recipientId;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(int $messageId, int $recipientId)
    {
        $this->messageId = $messageId;
        $this->recipientId = $recipientId;
    }

    public function handle(): void
    {
        $message = Message::with('conversation')->find($this->messageId);

        if (!$message) {
            return;
        }

        $message->markAsDelivered();

        NewMessageEvent::dispatch($message, $message->conversation_id, $this->recipientId);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('SendMessageJob failed', [
            'message_id' => $this->messageId,
            'recipient_id' => $this->recipientId,
            'error' => $exception->getMessage(),
        ]);
    }
}
