<?php

namespace App\Models;

use App\Enums\MobileFeatureState;
use Database\Factories\UserFeatureOverrideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tenant_id',
    'user_id',
    'feature_key',
    'state',
    'reason',
    'message',
    'offline_behavior',
    'metadata',
])]
final class UserFeatureOverride extends Model
{
    /** @use HasFactory<UserFeatureOverrideFactory> */
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  Builder<UserFeatureOverride>  $query
     * @return Builder<UserFeatureOverride>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'tenant_id',
                'user_id',
                'feature_key',
                'state',
                'reason',
                'message',
                'offline_behavior',
                'metadata',
                'updated_at',
            ])
            ->with([
                'tenant:id,name,public_id,status',
                'user:id,name,email',
            ])
            ->orderByDesc('updated_at');
    }

    /**
     * @param  Builder<UserFeatureOverride>  $query
     * @return Builder<UserFeatureOverride>
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
                })
                ->orWhereHas('user', function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
        });
    }
}
