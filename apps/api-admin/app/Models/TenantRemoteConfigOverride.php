<?php

namespace App\Models;

use Database\Factories\TenantRemoteConfigOverrideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tenant_id',
    'config_key',
    'value',
    'version',
    'reason',
    'metadata',
])]
final class TenantRemoteConfigOverride extends Model
{
    /** @use HasFactory<TenantRemoteConfigOverrideFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
            'metadata' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @param  Builder<TenantRemoteConfigOverride>  $query
     * @return Builder<TenantRemoteConfigOverride>
     */
    public function scopeForTenantResolver(Builder $query, Tenant $tenant): Builder
    {
        return $query
            ->select(['id', 'tenant_id', 'config_key', 'value', 'version', 'updated_at'])
            ->where('tenant_id', $tenant->id)
            ->orderBy('config_key');
    }
}
