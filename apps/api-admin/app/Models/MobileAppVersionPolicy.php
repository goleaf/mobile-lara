<?php

namespace App\Models;

use Database\Factories\MobileAppVersionPolicyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tenant_id',
    'cohort_key',
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
            'tenant_id' => 'integer',
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

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
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
                'tenant_id',
                'cohort_key',
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
                'tenant_id',
                'cohort_key',
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
            ->with('tenant:id,name,public_id,status')
            ->orderBy('platform')
            ->orderBy('tenant_id')
            ->orderBy('cohort_key')
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

    public function scopeGlobalScope(Builder $query): Builder
    {
        return $query
            ->whereNull('tenant_id')
            ->whereNull('cohort_key');
    }

    public function scopeTenantScope(Builder $query, Tenant $tenant): Builder
    {
        return $query->where('tenant_id', $tenant->id);
    }

    public function scopeCohortScope(Builder $query, string $cohortKey): Builder
    {
        return $query->where('cohort_key', $cohortKey);
    }

    public function scopeType(): string
    {
        if ($this->tenant_id !== null) {
            return 'tenant';
        }

        if (is_string($this->cohort_key) && trim($this->cohort_key) !== '') {
            return 'cohort';
        }

        return 'global';
    }
}
