<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use HasFactory;

    // Disable updated_at since it doesn't exist in the database schema
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_uid',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the social account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
