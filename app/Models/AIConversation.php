<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIConversation extends Model
{
    use HasFactory, SchoolScope;
    
    protected $table = 'ai_conversations';

    protected $fillable = [
        'school_id',
        'user_id',
        'session_id',
        'mode',
        'initial_message',
        'status',
        'escalated_to',
        'resolution_notes',
        'context',
        'message_count',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function escalatedTo()
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    public function messages()
    {
        return $this->hasMany(AIMessage::class)->orderBy('created_at', 'asc');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function escalateTo($userId, $notes = null)
    {
        $this->update([
            'status' => 'escalated',
            'escalated_to' => $userId,
            'resolution_notes' => $notes,
        ]);
    }

    public function markResolved($notes = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolution_notes' => $notes,
        ]);
    }

    public function incrementMessageCount()
    {
        $this->increment('message_count');
    }
}
