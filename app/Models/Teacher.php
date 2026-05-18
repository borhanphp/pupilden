<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class Teacher extends Authenticatable implements CanResetPassword
{
    use HasApiTokens;

    public function getEmailForPasswordReset(): string
    {
        return (string) ($this->email ?? '');
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'bio',
        'website',
        'phone',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'password'  => 'hashed',
    ];

    protected $appends = ['profile_image_url'];

    public function getProfileImageUrlAttribute(): ?string
    {
        if (!$this->profile_image) return null;
        return Storage::disk('r2')->url($this->profile_image);
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_teacher')
                    ->withPivot('role', 'status', 'invited_by')
                    ->withTimestamps();
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function activeOrganizations()
    {
        return $this->organizations()->wherePivot('status', 'active');
    }
}
