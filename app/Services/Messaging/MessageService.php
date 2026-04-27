<?php

namespace App\Services\Messaging;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class MessageService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getMessages(Conversation $conversation, int $userId, int $perPage = 50): \Illuminate\Pagination\LengthAwarePaginator
    {
        if (!$conversation->isParticipant($userId)) {
            throw new \Exception('User is not a participant in this conversation');
        }

        return $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getOlderMessages(Conversation $conversation, int $userId, ?int $beforeMessageId, int $limit = 20): Collection
    {
        if (!$conversation->isParticipant($userId)) {
            throw new \Exception('User is not a participant in this conversation');
        }

        $query = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc');

        if ($beforeMessageId) {
            $beforeMessage = Message::find($beforeMessageId);
            if ($beforeMessage) {
                $query->where('created_at', '<', $beforeMessage->created_at);
            }
        }

        return $query->limit($limit)->get()->reverse()->values();
    }

    public function sendMessage(Conversation $conversation, User $sender, string $content, ?UploadedFile $attachment = null): Message
    {
        if (!$conversation->isParticipant($sender->id)) {
            throw new \Exception('User is not a participant in this conversation');
        }

        $attachmentData = null;

        if ($attachment) {
            $attachmentData = $this->processAttachment($attachment);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'content' => $content,
            'type' => $attachmentData ? 'file' : 'text',
            'attachment_path' => $attachmentData['path'] ?? null,
            'attachment_name' => $attachmentData['name'] ?? null,
            'attachment_size' => $attachmentData['size'] ?? null,
            'attachment_mime_type' => $attachmentData['mime_type'] ?? null,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $recipientId = $conversation->initiator_id === $sender->id 
            ? $conversation->recipient_id 
            : $conversation->initiator_id;

        $conversation->participants()->updateExistingPivot($recipientId, [
            'unread_count' => \DB::raw('unread_count + 1'),
        ]);

        return $message->load('sender');
    }

    public function editMessage(Message $message, User $user, string $newContent): Message
    {
        if ($message->sender_id !== $user->id) {
            throw new \Exception('User can only edit their own messages');
        }

        $message->update([
            'content' => $newContent,
            'is_edited' => true,
        ]);

        return $message->fresh();
    }

    public function deleteMessage(Message $message, User $user): bool
    {
        if ($message->sender_id !== $user->id) {
            throw new \Exception('User can only delete their own messages');
        }

        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        return $message->delete();
    }

    public function markMessagesAsRead(Conversation $conversation, int $userId, array $messageIds = []): void
    {
        if (!$conversation->isParticipant($userId)) {
            return;
        }

        $query = $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at');

        if (!empty($messageIds)) {
            $query->whereIn('id', $messageIds);
        }

        $query->update(['read_at' => now()]);
    }

    public function getUnreadMessagesCount(int $userId): int
    {
        return Message::whereHas('conversation', function ($q) use ($userId) {
            $q->forUser($userId);
        })
        ->where('sender_id', '!=', $userId)
        ->whereNull('read_at')
        ->count();
    }

    protected function processAttachment(UploadedFile $file): array
    {
        $this->validateAttachment($file);

        $path = $file->store('message-attachments', 'public');

        return [
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    protected function validateAttachment(UploadedFile $file): void
    {
        $maxSize = 10 * 1024 * 1024;
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'application/zip',
        ];

        if ($file->getSize() > $maxSize) {
            throw new \Exception('File size exceeds maximum allowed size of 10MB');
        }

        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \Exception('File type is not allowed');
        }
    }

    public function searchMessages(int $userId, string $query, ?int $conversationId = null): Collection
    {
        $messageQuery = Message::whereHas('conversation', function ($q) use ($userId) {
            $q->forUser($userId)->active();
        })
        ->where('content', 'like', "%{$query}%")
        ->with(['conversation', 'sender'])
        ->orderBy('created_at', 'desc');

        if ($conversationId) {
            $messageQuery->where('conversation_id', $conversationId);
        }

        return $messageQuery->limit(50)->get();
    }
}
