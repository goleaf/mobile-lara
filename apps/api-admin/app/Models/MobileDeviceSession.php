<?php

namespace App\Models;

use Database\Factories\MobileDeviceSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'device_id',
    'device_name',
    'platform',
    'app_version',
    'status',
    'ip_address',
    'user_agent',
    'last_seen_at',
    'expires_at',
    'revoked_at',
])]
final class MobileDeviceSession extends Model
{
    /** @use HasFactory<MobileDeviceSessionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accessTokens(): HasMany
    {
        return $this->hasMany(MobileAccessToken::class);
    }

    public function refreshTokens(): HasMany
    {
        return $this->hasMany(MobileRefreshToken::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->revoked_at === null
            && $this->expires_at !== null
            && $this->expires_at->isFuture();
    }
}
