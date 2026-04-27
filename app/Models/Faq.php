<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use SchoolScope;

    protected $fillable = [
        'school_id',
        'question',
        'answer',
        'category',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('school_id');
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where(function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId)
              ->orWhereNull('school_id');
        });
    }

    public static function getForStudent($schoolId = null)
    {
        $query = self::active()
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($schoolId) {
            return $query->forSchool($schoolId)->get();
        }

        return $query->global()->get();
    }
}
