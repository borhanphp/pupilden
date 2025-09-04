<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'organization_id',
        'username',
        'email',
        'password',
        'name',
        'profile_picture',
        'bio',
        'contact_number',
        'alt_contact_number'
    ];

    protected $hidden = ['password'];
}