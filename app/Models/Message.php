<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'type',
        'attachment_path',
        'attachment_name',
        'attachment_size',
        'attachment_mime_type',
        'is_edited',
        'read_at',
        'delivered_at',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'read_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function scopeInConversation(Builder $query, int $conversationId): Builder
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function scopeUnread(Builder $query, int $userId): Builder
    {
        return $query->where('sender_id', '!=', $userId)
            ->whereNull('read_at');
    }

    public function scopeWithAttachments(Builder $query): Builder
    {
        return $query->whereNotNull('attachment_path');
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsDelivered(): void
    {
        if (!$this->delivered_at) {
            $this->update(['delivered_at' => now()]);
        }
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }
        return asset("storage/{$this->attachment_path}");
    }

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->attachment_size) {
            return '';
        }
        
        $bytes = $this->attachment_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    public function getIsImageAttribute(): bool
    {
        if (!$this->attachment_mime_type) {
            return false;
        }
        return in_array($this->attachment_mime_type, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        ]);
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }
}
