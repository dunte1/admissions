<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intake extends Model
{
    use HasFactory, SchoolScope;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'year',
        'semester',
        'application_start_date',
        'application_end_date',
        'review_start_date',
        'review_end_date',
        'results_release_date',
        'registration_start_date',
        'registration_end_date',
        'is_active',
        'is_current',
        'description',
    ];

    protected $casts = [
        'application_start_date' => 'date',
        'application_end_date' => 'date',
        'review_start_date' => 'date',
        'review_end_date' => 'date',
        'results_release_date' => 'date',
        'registration_start_date' => 'date',
        'registration_end_date' => 'date',
        'is_active' => 'boolean',
        'is_current' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function isOpen(): bool
    {
        $today = now()->toDateString();
        return $this->is_active 
            && $today >= $this->application_start_date->toDateString()
            && $today <= $this->application_end_date->toDateString();
    }

    public function isUpcoming(): bool
    {
        return $this->is_active && now()->lt($this->application_start_date);
    }

    public function isClosed(): bool
    {
        return !$this->is_active || now()->gt($this->application_end_date);
    }

    public function scopeCurrent($query)
    {
        return $query->where(function ($q) {
            $q->where('is_current', true)
              ->orWhere(function ($sub) {
                  $sub->where('is_active', true)
                      ->whereDate('application_end_date', '>=', now());
              });
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getCurrentIntake(): ?self
    {
        return self::where('is_current', true)->first() 
            ?? self::where('is_active', true)->orderBy('application_end_date', 'desc')->first();
    }

    public function getStatusLabel(): string
    {
        if ($this->isUpcoming()) {
            return 'Upcoming';
        }
        if ($this->isOpen()) {
            return 'Open';
        }
        return 'Closed';
    }

    public function getStatusColor(): string
    {
        return match($this->getStatusLabel()) {
            'Open' => 'success',
            'Upcoming' => 'warning',
            default => 'secondary',
        };
    }
}