<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGateway extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'gateway_name',
        'display_name',
        'is_active',
        'is_manual',
        'is_default',
        'credentials',
        'settings',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_manual' => 'boolean',
        'is_default' => 'boolean',
        'credentials' => 'array',
        'settings' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function isDefault()
    {
        return $this->is_default;
    }

    /**
     * Get available gateway types
     */
    public static function getGatewayTypes()
    {
        return [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'razorpay' => 'Razorpay',
            'sslcommerz' => 'SSLCommerz',
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            'rocket' => 'Rocket',
            'bank_transfer' => 'Bank Transfer',
        ];
    }
}
