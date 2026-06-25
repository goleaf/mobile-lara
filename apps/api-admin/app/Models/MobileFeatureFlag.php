<?php

namespace App\Models;

use App\Enums\MobileFeatureState;
use Database\Factories\MobileFeatureFlagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'key',
    'name',
    'default_state',
    'reason',
    'message',
    'minimum_app_version',
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
            'metadata' => 'array',
        ];
    }
}
