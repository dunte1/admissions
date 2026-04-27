<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIKnowledge extends Model
{
    use HasFactory;
    
    protected $table = 'ai_knowledge';

    protected $fillable = [
        'school_id',
        'title',
        'content',
        'type',
        'source_url',
        'source_type',
        'is_active',
        'tokens',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
