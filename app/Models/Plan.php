<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'billing_period',
        'duration_days',
        'max_students',
        'max_staff',
        'max_programs',
        'max_applications',
        'allow_document_upload',
        'allow_payment_gateway',
        'allow_custom_branding',
        'allow_api_access',
        'allow_priority_support',
        'is_active',
        'sort_order',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'max_students' => 'integer',
        'max_staff' => 'integer',
        'max_programs' => 'integer',
        'max_applications' => 'integer',
        'allow_document_upload' => 'boolean',
        'allow_payment_gateway' => 'boolean',
        'allow_custom_branding' => 'boolean',
        'allow_api_access' => 'boolean',
        'allow_priority_support' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'features' => 'array',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->where('status', 'active');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    public function getBillingPeriodLabelAttribute(): string
    {
        return match($this->billing_period) {
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            default => ucfirst($this->billing_period),
        };
    }

    public function isUnlimited(string $feature): bool
    {
        return is_null($this->{$feature});
    }

    public function hasFeature(string $feature): bool
    {
        return (bool) $this->{$feature};
    }

    public function getFeatureValue(string $feature): int|null
    {
        return $this->{$feature};
    }

    public function getFeaturesAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }

    public function setFeaturesAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['features'] = json_encode($value);
        } else {
            $this->attributes['features'] = $value;
        }
    }
}
