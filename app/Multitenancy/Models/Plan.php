<?php

namespace App\Multitenancy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'max_students',
        'max_admins',
        'storage_limit_gb',
        'features',
        'is_active',
        'is_featured',
        'grace_period_days',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_students' => 'integer',
        'max_admins' => 'integer',
        'storage_limit_gb' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'grace_period_days' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $castsFeatures = false;

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function getFeaturesArrayAttribute(): array
    {
        if ($this->castsFeatures) {
            return $this->attributes['features'] ?? [];
        }

        if (is_array($this->attributes['features'])) {
            return $this->attributes['features'];
        }

        return json_decode($this->attributes['features'] ?? '[]', true) ?? [];
    }

    public function hasFeature(string $feature): bool
    {
        $features = $this->featuresArray;
        return isset($features[$feature]) && $features[$feature] === true;
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function isMonthly(): bool
    {
        return $this->billing_cycle === 'monthly';
    }

    public function isYearly(): bool
    {
        return $this->billing_cycle === 'yearly';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    public static function getDefaultPlan(): ?self
    {
        return self::where('slug', 'starter')->first();
    }
}