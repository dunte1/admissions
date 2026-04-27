<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public int $conversationId;
    public int $recipientId;

    public function __construct(Message $message, int $conversationId, int $recipientId)
    {
        $this->message = $message;
        $this->conversationId = $conversationId;
        $this->recipientId = $recipientId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->recipientId),
            new PrivateChannel('conversation.' . $this->conversationId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-message';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->fullName(),
            'sender_avatar' => $this->message->sender->avatar_url,
            'content' => $this->message->content,
            'type' => $this->message->type,
            'attachment_url' => $this->message->attachment_url,
            'attachment_name' => $this->message->attachment_name,
            'attachment_size' => $this->message->attachment_size,
            'attachment_mime_type' => $this->message->attachment_mime_type,
            'is_edited' => $this->message->is_edited,
            'created_at' => $this->message->created_at->toIso8601String(),
            'created_at_human' => $this->message->created_at->diffForHumans(),
        ];
    }
}
