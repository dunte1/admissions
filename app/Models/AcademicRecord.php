<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicRecord extends Model
{
    use HasFactory, SchoolScope;

    protected $fillable = [
        'student_id',
        'institution_name',
        'certificate_type',
        'country',
        'start_date',
        'end_date',
        'average_grade',
        'index_number',
        'year_of_completion',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'year_of_completion' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
