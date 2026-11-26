<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginEvent extends Model
{
    public const PENDING_EXPIRY_SECONDS = 60;

    /** Valid status values: pending | approved | declined | auto | expired */
    protected $fillable = [
        'integration_id', 'user_id', 'ip_address', 'device',
        'status', 'is_first_login', 'is_archived', 'credential_username',
    ];

    /**
     * Events that have been pending longer than the given seconds.
     */
    /**
     * @param  Builder<LoginEvent>  $query
     * @return Builder<LoginEvent>
     */
    public function scopeOverduePending(Builder $query, int $seconds = self::PENDING_EXPIRY_SECONDS): Builder
    {
        return $query->where('status', 'pending')
            ->where('created_at', '<=', now()->subSeconds($seconds));
    }

    protected $casts = [
        'is_first_login' => 'boolean',
        'is_archived' => 'boolean',
    ];

    /** @return BelongsTo<Integration, $this> */
    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
