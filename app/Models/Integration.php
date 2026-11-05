<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Integration extends Model
{
    protected $fillable = [
        'user_id', 'name', 'url', 'secret_key', 'gate_token', 'is_active', 'favicon_url',
    ];

    protected $hidden = [
        'secret_key',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loginEvents(): HasMany
    {
        return $this->hasMany(LoginEvent::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Integration $integration) {
            if (blank($integration->gate_token)) {
                $integration->gate_token = Str::random(40);
            }
        });
    }

    public function gateUrl(): string
    {
        return url('/i/'.$this->gate_token);
    }

    public function hasCompletedFirstLogin(): bool
    {
        return $this->loginEvents()
            ->whereIn('status', ['approved', 'auto'])
            ->exists();
    }

    /** Favicon URL derived from the integration URL (Google favicon service) */
    public function getFaviconAttribute(): string
    {
        if ($this->favicon_url) return $this->favicon_url;
        $host = parse_url($this->url, PHP_URL_HOST) ?? $this->url;
        return "https://www.google.com/s2/favicons?domain={$host}&sz=64";
    }
}
