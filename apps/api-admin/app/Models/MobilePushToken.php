<?php

namespace App\Models;

use Database\Factories\MobilePushTokenFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'user_id',
    'mobile_device_session_id',
    'public_id',
    'token_hash',
    'token_preview',
    'provider',
    'platform',
    'device_id',
    'app_version',
    'metadata',
    'last_registered_at',
    'revoked_at',
])]
final class MobilePushToken extends Model
{
    /** @use HasFactory<MobilePushTokenFactory> */
    use HasFactory;

    public const SELECT_COLUMNS = [
        'id',
        'tenant_id',
        'user_id',
        'mobile_device_session_id',
        'public_id',
        'token_hash',
        'token_preview',
        'provider',
        'platform',
        'device_id',
        'app_version',
        'metadata',
        'last_registered_at',
        'revoked_at',
        'created_at',
        'updated_at',
    ];

    protected static function booted(): void
    {
        self::creating(function (MobilePushToken $pushToken): void {
            if (! is_string($pushToken->public_id) || trim($pushToken->public_id) === '') {
                $pushToken->public_id = (string) Str::uuid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'last_registered_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deviceSession(): BelongsTo
    {
        return $this->belongsTo(MobileDeviceSession::class, 'mobile_device_session_id');
    }

    /**
     * @param  Builder<MobilePushToken>  $query
     * @return Builder<MobilePushToken>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('revoked_at');
    }
}
