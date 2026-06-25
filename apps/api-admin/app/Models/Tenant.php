<?php

namespace App\Models;

use App\Enums\TenantStatus;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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

    public function isMobileSwitchable(): bool
    {
        return $this->status instanceof TenantStatus
            && $this->status->isMobileSwitchable();
    }
}
