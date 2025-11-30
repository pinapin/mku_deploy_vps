<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'ip_address',
        'user_agent',
        'status',
    ];

    /**
     * Get the user that owns the login log.
     */
    public function user()
    {
        if ($this->role === 'admin') {
            return $this->belongsTo(User::class, 'user_id');
        } elseif ($this->role === 'mahasiswa') {
            return $this->belongsTo(Mahasiswa::class, 'user_id');
        } elseif ($this->role === 'dosen') {
            return $this->belongsTo(DosenP2K::class, 'user_id', 'id');
        }
        
        return null;
    }
}
