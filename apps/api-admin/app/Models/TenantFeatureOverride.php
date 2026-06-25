<?php

namespace App\Models;

use App\Enums\MobileFeatureState;
use Database\Factories\TenantFeatureOverrideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tenant_id',
    'feature_key',
    'state',
    'reason',
    'message',
    'offline_behavior',
    'metadata',
])]
final class TenantFeatureOverride extends Model
{
    /** @use HasFactory<TenantFeatureOverrideFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'state' => MobileFeatureState::class,
            'metadata' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @param  Builder<TenantFeatureOverride>  $query
     * @return Builder<TenantFeatureOverride>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'tenant_id',
                'feature_key',
                'state',
                'reason',
                'message',
                'offline_behavior',
                'metadata',
                'updated_at',
            ])
            ->with('tenant:id,name,public_id,status')
            ->orderByDesc('updated_at');
    }

    /**
     * @param  Builder<TenantFeatureOverride>  $query
     * @return Builder<TenantFeatureOverride>
     */
    public function scopeMatchingAdminSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('feature_key', 'like', '%'.$search.'%')
                ->orWhere('reason', 'like', '%'.$search.'%')
                ->orWhereHas('tenant', function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('public_id', 'like', '%'.$search.'%');
                });
        });
    }
}
