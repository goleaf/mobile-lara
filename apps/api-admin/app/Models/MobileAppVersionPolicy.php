<?php

namespace App\Models;

use Database\Factories\MobileAppVersionPolicyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'platform',
    'minimum_supported_version',
    'minimum_recommended_version',
    'latest_version',
    'blocked_versions',
    'store_urls',
    'message',
    'support_url',
    'force_update',
    'maintenance_enabled',
    'maintenance_message',
    'retry_after_seconds',
    'allowed_actions',
    'logout_allowed',
    'is_active',
    'metadata',
])]
final class MobileAppVersionPolicy extends Model
{
    /** @use HasFactory<MobileAppVersionPolicyFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blocked_versions' => 'array',
            'store_urls' => 'array',
            'force_update' => 'boolean',
            'maintenance_enabled' => 'boolean',
            'retry_after_seconds' => 'integer',
            'allowed_actions' => 'array',
            'logout_allowed' => 'boolean',
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    /**
     * @param  Builder<MobileAppVersionPolicy>  $query
     * @return Builder<MobileAppVersionPolicy>
     */
    public function scopeActiveForPlatform(Builder $query, string $platform): Builder
    {
        return $query
            ->select([
                'id',
                'platform',
                'minimum_supported_version',
                'minimum_recommended_version',
                'latest_version',
                'blocked_versions',
                'store_urls',
                'message',
                'support_url',
                'force_update',
                'maintenance_enabled',
                'maintenance_message',
                'retry_after_seconds',
                'allowed_actions',
                'logout_allowed',
                'is_active',
                'updated_at',
            ])
            ->where('is_active', true)
            ->whereIn('platform', [$platform, 'all'])
            ->orderByDesc('updated_at');
    }

    /**
     * @param  Builder<MobileAppVersionPolicy>  $query
     * @return Builder<MobileAppVersionPolicy>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'platform',
                'minimum_supported_version',
                'minimum_recommended_version',
                'latest_version',
                'blocked_versions',
                'store_urls',
                'message',
                'support_url',
                'force_update',
                'maintenance_enabled',
                'maintenance_message',
                'retry_after_seconds',
                'allowed_actions',
                'logout_allowed',
                'is_active',
                'metadata',
                'updated_at',
            ])
            ->orderBy('platform')
            ->orderByDesc('updated_at');
    }

    /**
     * @param  Builder<MobileAppVersionPolicy>  $query
     * @return Builder<MobileAppVersionPolicy>
     */
    public function scopeForAdminPlatform(Builder $query, string $platform): Builder
    {
        if ($platform === 'any') {
            return $query;
        }

        return $query->where('platform', $platform);
    }
}
