<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'initiator_id',
        'recipient_id',
        'subject',
        'status',
        'last_message_at',
        'last_read_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'last_read_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot(['unread_count', 'last_read_at', 'last_typing_at', 'is_typing'])
            ->withTimestamps();
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_at');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('initiator_id', $userId)
              ->orWhere('recipient_id', $userId);
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeForSchool(Builder $query, ?int $schoolId): Builder
    {
        if ($schoolId) {
            return $query->where('school_id', $schoolId);
        }
        return $query->whereNull('school_id');
    }

    public function scopeWithUnread(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('initiator_id', $userId)
              ->orWhere('recipient_id', $userId);
        })
        ->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->where('unread_count', '>', 0);
        });
    }

    public function getOtherParticipant(int $userId): ?User
    {
        return $this->initiator_id === $userId ? $this->recipient : $this->initiator;
    }

    public function isParticipant(int $userId): bool
    {
        return $this->initiator_id === $userId || $this->recipient_id === $userId;
    }

    public function getUnreadCount(int $userId): int
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        return $participant?->pivot?->unread_count ?? 0;
    }

    public function markAsRead(int $userId): void
    {
        $this->participants()->updateExistingPivot($userId, [
            'unread_count' => 0,
            'last_read_at' => now(),
        ]);
        
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function incrementUnread(int $userId): void
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        if ($participant) {
            $this->participants()->updateExistingPivot($userId, [
                'unread_count' => $participant->pivot->unread_count + 1,
            ]);
        }
    }

    public function setTyping(int $userId, bool $isTyping): void
    {
        $this->participants()->updateExistingPivot($userId, [
            'is_typing' => $isTyping,
            'last_typing_at' => now(),
        ]);
    }

    public function getOtherTyping(int $userId): bool
    {
        $otherId = $this->initiator_id === $userId ? $this->recipient_id : $this->initiator_id;
        $participant = $this->participants()->where('user_id', $otherId)->first();
        return $participant?->pivot?->is_typing ?? false;
    }
}
