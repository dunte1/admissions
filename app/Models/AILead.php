<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AILead extends Model
{
    use HasFactory;
    
    protected $table = 'ai_leads';

    protected $fillable = [
        'school_id',
        'conversation_id',
        'name',
        'email',
        'phone',
        'program_interest',
        'notes',
        'status',
        'source',
    ];

    const STATUS_NEW = 'new';
    const STATUS_CONTACTED = 'contacted';
    const STATUS_CONVERTED = 'converted';
    const STATUS_LOST = 'lost';

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function conversation()
    {
        return $this->belongsTo(AIConversation::class);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeConverted($query)
    {
        return $query->where('status', self::STATUS_CONVERTED);
    }

    public function markAsContacted()
    {
        $this->update(['status' => self::STATUS_CONTACTED]);
    }

    public function markAsConverted()
    {
        $this->update(['status' => self::STATUS_CONVERTED]);
    }

    public function markAsLost()
    {
        $this->update(['status' => self::STATUS_LOST]);
    }

    public static function createFromLeadData(array $data): self
    {
        return static::create([
            'school_id' => $data['school_id'],
            'conversation_id' => $data['conversation_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'program_interest' => $data['program_interest'] ?? null,
            'notes' => $data['notes'] ?? null,
            'source' => $data['source'] ?? 'ai_chat',
        ]);
    }
}