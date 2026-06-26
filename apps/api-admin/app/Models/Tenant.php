<?php

namespace App\Models;

use App\Enums\TenantStatus;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'public_id',
    'name',
    'slug',
    'status',
    'subscription_state',
    'settings',
])]
final class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TenantStatus::class,
            'settings' => 'array',
        ];
    }

    protected static function booted(): void
    {
        self::creating(function (Tenant $tenant): void {
            if (! is_string($tenant->public_id) || trim($tenant->public_id) === '') {
                $tenant->public_id = (string) Str::uuid();
            }

            if (! is_string($tenant->slug) || trim($tenant->slug) === '') {
                $tenant->slug = Str::slug($tenant->name).'-'.Str::lower(Str::random(6));
            }
        });
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    /**
     * @param  Builder<Tenant>  $query
     * @return Builder<Tenant>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'public_id',
                'name',
                'slug',
                'status',
                'subscription_state',
                'settings',
                'updated_at',
            ])
            ->withCount('memberships')
            ->orderBy('name');
    }

    /**
     * @param  Builder<Tenant>  $query
     * @return Builder<Tenant>
     */
    public function scopeMatchingAdminSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('slug', 'like', '%'.$search.'%')
                ->orWhere('public_id', 'like', '%'.$search.'%')
                ->orWhere('status', 'like', '%'.$search.'%')
                ->orWhere('subscription_state', 'like', '%'.$search.'%');
        });
    }

    public function isMobileSwitchable(): bool
    {
        return $this->status instanceof TenantStatus
            && $this->status->isMobileSwitchable();
    }
}
