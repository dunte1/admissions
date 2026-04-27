<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $conversationId;
    public int $readerId;
    public string $readerName;
    public array $messageIds;

    public function __construct(int $conversationId, int $readerId, string $readerName, array $messageIds = [])
    {
        $this->conversationId = $conversationId;
        $this->readerId = $readerId;
        $this->readerName = $readerName;
        $this->messageIds = $messageIds;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversationId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message-read';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'reader_id' => $this->readerId,
            'reader_name' => $this->readerName,
            'message_ids' => $this->messageIds,
            'read_at' => now()->toIso8601String(),
        ];
    }
}
