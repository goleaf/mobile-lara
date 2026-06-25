<?php

namespace App\Models;

use Database\Factories\MobileRemoteConfigFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'key',
    'category',
    'value',
    'version',
    'description',
    'is_sensitive',
    'metadata',
])]
final class MobileRemoteConfig extends Model
{
    /** @use HasFactory<MobileRemoteConfigFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
            'is_sensitive' => 'boolean',
            'metadata' => 'array',
        ];
    }

    /**
     * @param  Builder<MobileRemoteConfig>  $query
     * @return Builder<MobileRemoteConfig>
     */
    public function scopeForResolver(Builder $query): Builder
    {
        return $query
            ->select(['id', 'key', 'value', 'version', 'updated_at'])
            ->where('category', 'mobile')
            ->where('is_sensitive', false)
            ->orderBy('key');
    }

    /**
     * @param  Builder<MobileRemoteConfig>  $query
     * @return Builder<MobileRemoteConfig>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'key',
                'category',
                'value',
                'version',
                'description',
                'is_sensitive',
                'metadata',
                'updated_at',
            ])
            ->orderBy('key');
    }

    /**
     * @param  Builder<MobileRemoteConfig>  $query
     * @return Builder<MobileRemoteConfig>
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
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhere('version', 'like', '%'.$search.'%');
        });
    }
}
