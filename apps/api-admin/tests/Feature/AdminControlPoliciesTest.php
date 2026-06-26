<?php

use App\Models\MobileAppVersionPolicy;
use App\Models\MobileFeatureFlag;
use App\Models\MobileRemoteConfig;
use App\Models\Tenant;
use App\Models\TenantFeatureOverride;
use App\Models\TenantRemoteConfigOverride;
use App\Models\User;
use App\Models\UserFeatureOverride;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

test('platform admins can manage current control-plane resources through policies', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    foreach (adminControlPolicyResources() as [$modelClass, $model]) {
        expect(Gate::forUser($admin)->allows('viewAny', $modelClass))->toBeTrue()
            ->and(Gate::forUser($admin)->allows('create', $modelClass))->toBeTrue()
            ->and(Gate::forUser($admin)->allows('view', $model))->toBeTrue()
            ->and(Gate::forUser($admin)->allows('update', $model))->toBeTrue()
            ->and(Gate::forUser($admin)->allows('restore', $model))->toBeTrue()
            ->and(Gate::forUser($admin)->allows('delete', $model))->toBeFalse()
            ->and(Gate::forUser($admin)->allows('forceDelete', $model))->toBeFalse();
    }
});

test('non platform admins cannot manage control-plane resources through policies', function (): void {
    $user = User::factory()->create();

    foreach (adminControlPolicyResources() as [$modelClass, $model]) {
        expect(Gate::forUser($user)->allows('viewAny', $modelClass))->toBeFalse()
            ->and(Gate::forUser($user)->allows('create', $modelClass))->toBeFalse()
            ->and(Gate::forUser($user)->allows('view', $model))->toBeFalse()
            ->and(Gate::forUser($user)->allows('update', $model))->toBeFalse()
            ->and(Gate::forUser($user)->allows('restore', $model))->toBeFalse()
            ->and(Gate::forUser($user)->allows('delete', $model))->toBeFalse()
            ->and(Gate::forUser($user)->allows('forceDelete', $model))->toBeFalse();
    }
});

/**
 * @return array<int, array{class-string, object}>
 */
function adminControlPolicyResources(): array
{
    return [
        [MobileFeatureFlag::class, MobileFeatureFlag::factory()->create()],
        [Tenant::class, Tenant::factory()->create()],
        [TenantFeatureOverride::class, TenantFeatureOverride::factory()->create()],
        [UserFeatureOverride::class, UserFeatureOverride::factory()->create()],
        [MobileRemoteConfig::class, MobileRemoteConfig::factory()->create()],
        [TenantRemoteConfigOverride::class, TenantRemoteConfigOverride::factory()->create()],
        [MobileAppVersionPolicy::class, MobileAppVersionPolicy::factory()->create()],
    ];
}
