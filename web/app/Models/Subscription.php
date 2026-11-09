<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $plan
 * @property string $status
 */
class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'status',
        'ends_at',
    ];

    protected $casts = [
        'ends_at' => 'datetime',
    ];

    /** Valid plan slugs. */
    public const PLANS = ['free', 'pro', 'ultra', 'ultra_plus'];

    /**
     * @return BelongsTo<User, Subscription>
     *
     * @phpstan-return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The maximum number of integrations for this plan.
     * Returns null for unlimited.
     */
    public function limit(): ?int
    {
        return config("stripe.limits.{$this->plan}");
    }

    public function isPaid(): bool
    {
        return $this->plan !== 'free';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }
}
