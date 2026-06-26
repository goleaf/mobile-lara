<?php

namespace App\Models;

use App\Enums\TenantStatus;
use App\Enums\TenantUserRole;
use App\Enums\TenantUserStatus;
use Database\Factories\TenantUserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tenant_id',
    'user_id',
    'role',
    'status',
    'is_current',
    'invited_at',
    'accepted_at',
    'suspended_at',
])]
final class TenantUser extends Model
{
    /** @use HasFactory<TenantUserFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => TenantUserRole::class,
            'status' => TenantUserStatus::class,
            'is_current' => 'boolean',
            'invited_at' => 'datetime',
            'accepted_at' => 'datetime',
            'suspended_at' => 'datetime',
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
     * @param  Builder<TenantUser>  $query
     * @return Builder<TenantUser>
     */
    public function scopeForAdminRecentList(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'tenant_id',
                'user_id',
                'role',
                'status',
                'is_current',
                'updated_at',
            ])
            ->with([
                'tenant:id,name,slug,public_id,status',
                'user:id,name,email',
            ])
            ->latest('updated_at')
            ->limit(10);
    }

    public function isSwitchable(): bool
    {
        return $this->status instanceof TenantUserStatus
            && $this->status->isActive()
            && $this->tenant?->isMobileSwitchable();
    }

    public function disabledReason(): ?string
    {
        if ($this->status instanceof TenantUserStatus && ! $this->status->isActive()) {
            return 'membership_'.$this->status->value;
        }

        if ($this->tenant?->status instanceof TenantStatus && ! $this->tenant->status->isMobileSwitchable()) {
            return 'tenant_'.$this->tenant->status->value;
        }

        return null;
    }
}
