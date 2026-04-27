<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory, SchoolScope;

    protected $fillable = [
        'school_id',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'id_number',
        'passport_number',
        'nationality',
        'country',
        'county',
        'sub_county',
        'ward',
        'address',
        'city',
        'postal_code',
        'phone',
        'alt_phone',
        'primary_phone',
        'primary_email',
        'guardian_id_number',
        'birth_certificate',
        'disability_status',
        'disability_description',
        'marital_status',
        'religion',
        'admission_number',
        'admission_number_generated_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'birth_certificate' => 'date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fullName(): string
    {
        return trim(implode(' ', [
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]));
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function academicRecords(): HasMany
    {
        return $this->hasMany(AcademicRecord::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} " . ($this->middle_name ? "{$this->middle_name} " : '') . $this->last_name);
    }
}
