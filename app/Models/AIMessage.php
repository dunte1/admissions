<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIMessage extends Model
{
    use HasFactory;
    
    protected $table = 'ai_messages';

    protected $fillable = [
        'conversation_id',
        'school_id',
        'role',
        'content',
        'metadata',
        'is_escalation',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_escalation' => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(AIConversation::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
