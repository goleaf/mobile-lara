<?php

namespace App\Models;

use App\Enums\MobileFeatureState;
use Database\Factories\MobileFeatureFlagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'key',
    'name',
    'default_state',
    'reason',
    'message',
    'minimum_app_version',
    'required_plans',
    'device_constraints',
    'offline_behavior',
    'metadata',
])]
final class MobileFeatureFlag extends Model
{
    /** @use HasFactory<MobileFeatureFlagFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_state' => MobileFeatureState::class,
            'required_plans' => 'array',
            'device_constraints' => 'array',
            'metadata' => 'array',
        ];
    }

    /**
     * @param  Builder<MobileFeatureFlag>  $query
     * @return Builder<MobileFeatureFlag>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'key',
                'name',
                'default_state',
                'reason',
                'message',
                'minimum_app_version',
                'required_plans',
                'device_constraints',
                'offline_behavior',
                'metadata',
                'updated_at',
            ])
            ->orderBy('key');
    }

    /**
     * @param  Builder<MobileFeatureFlag>  $query
     * @return Builder<MobileFeatureFlag>
     */
    public function scopeMatchingAdminSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('key', 'like', '%'.$search.'%')
                ->orWhere('name', 'like', '%'.$search.'%');
        });
    }
}
