<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'organization_id',
        'file_name',
        'file_path',
        'file_type',
        'usage_type',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
