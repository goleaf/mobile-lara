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

    /**
     * @param  Builder<TenantRemoteConfigOverride>  $query
     * @return Builder<TenantRemoteConfigOverride>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'tenant_id',
                'config_key',
                'value',
                'version',
                'reason',
                'metadata',
                'updated_at',
            ])
            ->with('tenant:id,name,public_id,status')
            ->orderByDesc('updated_at');
    }

    /**
     * @param  Builder<TenantRemoteConfigOverride>  $query
     * @return Builder<TenantRemoteConfigOverride>
     */
    public function scopeMatchingAdminSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('config_key', 'like', '%'.$search.'%')
                ->orWhere('reason', 'like', '%'.$search.'%')
                ->orWhere('version', 'like', '%'.$search.'%')
                ->orWhereHas('tenant', function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('public_id', 'like', '%'.$search.'%');
                });
        });
    }
}
