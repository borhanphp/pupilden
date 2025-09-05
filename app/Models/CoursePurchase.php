<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursePurchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_id',
        'coupon_id',
        'original_price',
        'discount_amount',
        'final_price',
        'payment_method',
        'payment_status',
        'transaction_id',
        'payment_details',
        'purchased_at'
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'payment_details' => 'array',
        'purchased_at' => 'datetime',
    ];

    /**
     * Get the student who made the purchase
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course that was purchased
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the coupon used for this purchase
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => ucfirst($this->payment_status)
        };
    }

    /**
     * Get payment status badge class
     */
    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'bg-warning',
            'completed' => 'bg-success',
            'failed' => 'bg-danger',
            'refunded' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    /**
     * Check if purchase is completed
     */
    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if purchase is pending
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if purchase failed
     */
    public function isFailed()
    {
        return $this->payment_status === 'failed';
    }

    /**
     * Check if purchase was refunded
     */
    public function isRefunded()
    {
        return $this->payment_status === 'refunded';
    }

    /**
     * Mark purchase as completed
     */
    public function markAsCompleted($transactionId = null, $paymentMethod = null)
    {
        $this->update([
            'payment_status' => 'completed',
            'transaction_id' => $transactionId,
            'payment_method' => $paymentMethod,
            'purchased_at' => now()
        ]);

        // Enroll student in the course
        $this->student->courses()->attach($this->course_id);
    }

    /**
     * Mark purchase as failed
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'payment_status' => 'failed',
            'payment_details' => array_merge($this->payment_details ?? [], ['failure_reason' => $reason])
        ]);
    }

    /**
     * Mark purchase as refunded
     */
    public function markAsRefunded($refundId = null)
    {
        $this->update([
            'payment_status' => 'refunded',
            'payment_details' => array_merge($this->payment_details ?? [], ['refund_id' => $refundId])
        ]);

        // Unenroll student from the course
        $this->student->courses()->detach($this->course_id);
    }
}
