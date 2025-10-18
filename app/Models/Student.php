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
        'is_active',
        'password',
        'name',
        'profile_picture',
        'bio',
        'contact_number',
        'alt_contact_number'
    ];

    protected $hidden = ['password'];

    /**
     * Get the organization that owns the student
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the courses that the student is enrolled in
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_students');
    }

    /**
     * Get the course purchases made by the student
     */
    public function coursePurchases()
    {
        return $this->hasMany(CoursePurchase::class);
    }
}