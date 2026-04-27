<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory, SchoolScope;

    protected $fillable = [
        'school_id',
        'key',
        'value',
        'group',
    ];

    public $timestamps = false;

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public static function get($key, $default = null)
    {
        $schoolId = School::getCurrentId();
        
        if ($schoolId) {
            $setting = static::where('key', $key)->where('school_id', $schoolId)->first();
            return $setting ? $setting->value : $default;
        }
        
        $setting = static::where('key', $key)->whereNull('school_id')->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $group = 'general'): void
    {
        $schoolId = School::getCurrentId();
        
        static::updateOrCreate(
            ['key' => $key, 'school_id' => $schoolId],
            ['value' => $value, 'group' => $group]
        );
    }

    public static function getByGroup($group)
    {
        $schoolId = School::getCurrentId();
        
        $query = static::where('group', $group);
        
        if ($schoolId) {
            $query->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)->orWhereNull('school_id');
            });
        } else {
            $query->whereNull('school_id');
        }
        
        return $query->pluck('value', 'key')->toArray();
    }

    public static function setMany(array $settings, $group = 'general'): void
    {
        foreach ($settings as $key => $value) {
            static::set($key, $value, $group);
        }
    }

    public static function getGlobal($key, $default = null)
    {
        $setting = static::where('key', $key)->whereNull('school_id')->first();
        return $setting ? $setting->value : $default;
    }

    public static function setGlobal($key, $value, $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key, 'school_id' => null],
            ['value' => $value, 'group' => $group]
        );
    }
}
