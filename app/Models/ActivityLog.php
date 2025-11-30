<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'action',
        'method',
        'params',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'params' => 'array',
    ];
}
