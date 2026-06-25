<?php

namespace App\Models;

use App\Enums\MobileFeatureState;
use Database\Factories\TenantFeatureOverrideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
}
