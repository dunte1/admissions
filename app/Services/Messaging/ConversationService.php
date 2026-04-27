<?php

namespace App\Services\Messaging;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\NewMessageEvent;
use App\Events\TypingEvent;
use App\Events\MessageReadEvent;
use App\Events\ConversationUpdatedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ConversationService
{
    public function getConversationsForUser(User $user, ?int $schoolId = null): Collection
    {
        $query = Conversation::query()
            ->forUser($user->id)
            ->active()
            ->with(['initiator', 'recipient', 'participants', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc');

        if ($schoolId) {
            $query->forSchool($schoolId);
        }

        return $query->get()->map(function ($conversation) use ($user) {
            $conversation->unread_count = $conversation->getUnreadCount($user->id);
            return $conversation;
        });
    }

    public function getConversation(int $conversationId, int $userId): ?Conversation
    {
        $conversation = Conversation::with(['initiator', 'recipient', 'participants', 'messages.sender'])
            ->find($conversationId);

        if (!$conversation || !$conversation->isParticipant($userId)) {
            return null;
        }

        return $conversation;
    }

    public function createConversation(User $initiator, User $recipient, ?string $subject = null, ?int $schoolId = null): Conversation
    {
        $existingConversation = $this->findExistingConversation($initiator->id, $recipient->id);
        
        if ($existingConversation) {
            return $existingConversation;
        }

        return DB::transaction(function () use ($initiator, $recipient, $subject, $schoolId) {
            $conversation = Conversation::create([
                'school_id' => $schoolId ?? $initiator->school_id,
                'initiator_id' => $initiator->id,
                'recipient_id' => $recipient->id,
                'subject' => $subject,
                'status' => 'active',
            ]);

            $conversation->participants()->attach([
                $initiator->id => ['joined_at' => now(), 'unread_count' => 0],
                $recipient->id => ['joined_at' => now(), 'unread_count' => 0],
            ]);

            return $conversation;
        });
    }

    public function findExistingConversation(int $userId1, int $userId2): ?Conversation
    {
        return Conversation::where(function ($query) use ($userId1, $userId2) {
            $query->where('initiator_id', $userId1)
                  ->where('recipient_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('initiator_id', $userId2)
                  ->where('recipient_id', $userId1);
        })->active()->first();
    }

    public function sendMessage(Conversation $conversation, User $sender, string $content, ?array $attachment = null): Message
    {
        if (!$conversation->isParticipant($sender->id)) {
            throw new \Exception('User is not a participant in this conversation');
        }

        return DB::transaction(function () use ($conversation, $sender, $content, $attachment) {
            $messageData = [
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'content' => $content,
                'type' => 'text',
            ];

            if ($attachment) {
                $messageData['type'] = 'file';
                $messageData['attachment_path'] = $attachment['path'];
                $messageData['attachment_name'] = $attachment['name'];
                $messageData['attachment_size'] = $attachment['size'];
                $messageData['attachment_mime_type'] = $attachment['mime_type'];
            }

            $message = Message::create($messageData);

            $conversation->update(['last_message_at' => now()]);

            $recipientId = $conversation->initiator_id === $sender->id 
                ? $conversation->recipient_id 
                : $conversation->initiator_id;

            $conversation->participants()->updateExistingPivot($recipientId, [
                'unread_count' => DB::raw('unread_count + 1'),
            ]);

            $this->broadcastNewMessage($message, $recipientId);

            return $message;
        });
    }

    public function markAsRead(Conversation $conversation, int $userId): void
    {
        if (!$conversation->isParticipant($userId)) {
            return;
        }

        DB::transaction(function () use ($conversation, $userId) {
            $conversation->markAsRead($userId);

            $recipientId = $conversation->initiator_id === $userId 
                ? $conversation->recipient_id 
                : $conversation->initiator_id;

            $unreadMessages = $conversation->messages()
                ->where('sender_id', $recipientId)
                ->whereNull('read_at')
                ->pluck('id')
                ->toArray();

            if (!empty($unreadMessages)) {
                MessageReadEvent::dispatch(
                    $conversation->id,
                    $userId,
                    auth()->user()->fullName(),
                    $unreadMessages
                );
            }
        });
    }

    public function setTyping(Conversation $conversation, User $user, bool $isTyping): void
    {
        if (!$conversation->isParticipant($user->id)) {
            return;
        }

        $conversation->setTyping($user->id, $isTyping);

        TypingEvent::dispatch(
            $conversation->id,
            $user->id,
            $user->fullName(),
            $isTyping
        );
    }

    public function archiveConversation(Conversation $conversation, int $userId): void
    {
        if (!$conversation->isParticipant($userId)) {
            return;
        }

        $conversation->update(['status' => 'archived']);

        ConversationUpdatedEvent::dispatch(
            $conversation->id,
            'archived',
            ['user_id' => $userId]
        );
    }

    public function getOrCreateConversation(User $user1, User $user2, ?int $schoolId = null): Conversation
    {
        $existing = $this->findExistingConversation($user1->id, $user2->id);
        
        if ($existing) {
            return $existing;
        }

        return $this->createConversation($user1, $user2, null, $schoolId);
    }

    protected function broadcastNewMessage(Message $message, int $recipientId): void
    {
        NewMessageEvent::dispatch(
            $message,
            $message->conversation_id,
            $recipientId
        );
    }

    public function canMessage(User $sender, User $recipient): bool
    {
        if ($sender->id === $recipient->id) {
            return false;
        }

        if ($sender->isSuperAdmin()) {
            return true;
        }

        if ($recipient->isSuperAdmin()) {
            return $sender->isSuperAdmin();
        }

        if ($sender->school_id !== $recipient->school_id) {
            return false;
        }

        if ($sender->isStudent() && $recipient->isStudent()) {
            return false;
        }

        if ($sender->isStudent() && !$recipient->isStudent()) {
            return true;
        }

        if (!$sender->isStudent() && $recipient->isStudent()) {
            return true;
        }

        return true;
    }

    public function getUnreadCount(int $userId): int
    {
        return DB::table('conversation_participants')
            ->where('user_id', $userId)
            ->sum('unread_count');
    }

    public function getRecentConversations(User $user, int $limit = 5): Collection
    {
        return $this->getConversationsForUser($user, $user->school_id)
            ->take($limit);
    }
}
