<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginEvent extends Model
{
    /** Valid status values: pending | approved | declined | auto | expired */
    protected $fillable = [
        'integration_id', 'user_id', 'ip_address', 'device',
        'status', 'is_first_login', 'is_archived', 'credential_username',
    ];

    /**
     * Events that have been pending longer than the given minutes.
     */
    public function scopeOverduePending($query, int $minutes = 45)
    {
        return $query->where('status', 'pending')
                     ->where('created_at', '<=', now()->subMinutes($minutes));
    }

    protected $casts = [
        'is_first_login' => 'boolean',
        'is_archived' => 'boolean',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
