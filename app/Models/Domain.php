<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    /*`organization_id`, `domain_name`, `is_primary`, `is_active`, `is_verified`, `is_expired`, `activation_date`, `expiry_date`*/
    protected $fillable = [
        'organization_id',
        'domain_name',
        'is_primary',
        'is_active',
        'is_verified',
        'is_expired',
        'activation_date',
        'expiry_date',
        'created_by',
        'updated_by'
    ];
}
