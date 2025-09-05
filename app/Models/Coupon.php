<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'usage_limit_per_user',
        'applicable_courses',
        'starts_at',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'usage_limit_per_user' => 'integer',
        'applicable_courses' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the organization that owns the coupon
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the course purchases that used this coupon
     */
    public function coursePurchases()
    {
        return $this->hasMany(CoursePurchase::class);
    }

    /**
     * Check if coupon is valid
     */
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon is applicable to a specific course
     */
    public function isApplicableToCourse($courseId)
    {
        if (!$this->applicable_courses) {
            return true; // Applicable to all courses
        }

        return in_array($courseId, $this->applicable_courses);
    }

    /**
     * Check if coupon meets minimum amount requirement
     */
    public function meetsMinimumAmount($amount)
    {
        if (!$this->minimum_amount) {
            return true;
        }

        return $amount >= $this->minimum_amount;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($amount)
    {
        if (!$this->isValid() || !$this->meetsMinimumAmount($amount)) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
        } elseif ($this->type === 'fixed') {
            $discount = $this->value;
        }

        // Apply maximum discount limit
        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        // Don't exceed the original amount
        return min($discount, $amount);
    }

    /**
     * Get coupon type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'percentage' => 'Percentage Discount',
            'fixed' => 'Fixed Amount Discount',
            default => ucfirst($this->type)
        };
    }

    /**
     * Get coupon status
     */
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) {
            return 'Not Started';
        }

        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return 'Expired';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'Usage Limit Reached';
        }

        return 'Active';
    }

    /**
     * Get coupon status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'Active' => 'bg-success',
            'Inactive' => 'bg-secondary',
            'Not Started' => 'bg-info',
            'Expired' => 'bg-danger',
            'Usage Limit Reached' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('used_count');
    }
}
