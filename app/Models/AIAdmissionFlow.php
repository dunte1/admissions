<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIAdmissionFlow extends Model
{
    use HasFactory;
    
    protected $table = 'ai_admission_flows';

    protected $fillable = [
        'school_id',
        'user_id',
        'session_id',
        'status',
        'current_step',
        'collected_data',
        'application_id',
    ];

    protected $casts = [
        'collected_data' => 'array',
    ];

    const STEPS = [
        1 => 'initial_inquiry',
        2 => 'program_interest',
        3 => 'personal_info',
        4 => 'academic_background',
        5 => 'document_requirements',
        6 => 'payment_info',
        7 => 'application_review',
        8 => 'submission',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function getStepNameAttribute()
    {
        return self::STEPS[$this->current_step] ?? 'unknown';
    }

    public function advanceToStep($step, $data = [])
    {
        $collected = $this->collected_data ?? [];
        $collected['step_' . $this->current_step] = $data;
        
        $this->update([
            'current_step' => $step,
            'collected_data' => $collected,
        ]);
    }

    public function collectData($key, $value)
    {
        $collected = $this->collected_data ?? [];
        $collected[$key] = $value;
        $this->update(['collected_data' => $collected]);
    }

    public function complete($applicationId = null)
    {
        $this->update([
            'status' => 'completed',
            'application_id' => $applicationId,
            'current_step' => 8,
        ]);
    }

    public function abandon()
    {
        $this->update(['status' => 'abandoned']);
    }
}
