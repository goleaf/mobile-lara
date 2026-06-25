<?php

namespace App\Services\MobileFeatures;

use App\Enums\MobileFeatureState;
use App\Models\MobileFeatureFlag;
use App\Models\Tenant;
use App\Models\TenantFeatureOverride;
use App\Models\User;
use App\Models\UserFeatureOverride;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

final class MobileFeatureResolver
{
    /**
     * @var array<string, array{state: MobileFeatureState, reason: string|null, offline_behavior: string}>
     */
    private const FOUNDATION_DEFAULTS = [
        'records' => ['state' => MobileFeatureState::Disabled, 'reason' => 'records_api_pending', 'offline_behavior' => 'online_only'],
        'offline_sync' => ['state' => MobileFeatureState::OfflineLimited, 'reason' => 'sync_api_pending', 'offline_behavior' => 'queue_local_only'],
        'notifications' => ['state' => MobileFeatureState::Disabled, 'reason' => 'notifications_api_pending', 'offline_behavior' => 'online_only'],
        'support' => ['state' => MobileFeatureState::Disabled, 'reason' => 'support_api_pending', 'offline_behavior' => 'online_only'],
        'billing' => ['state' => MobileFeatureState::Disabled, 'reason' => 'billing_api_pending', 'offline_behavior' => 'online_only'],
        'reports' => ['state' => MobileFeatureState::Disabled, 'reason' => 'reports_api_pending', 'offline_behavior' => 'online_only'],
        'native_camera' => ['state' => MobileFeatureState::Visible, 'reason' => null, 'offline_behavior' => 'device_local'],
        'native_files' => ['state' => MobileFeatureState::Visible, 'reason' => null, 'offline_behavior' => 'device_local'],
        'native_share' => ['state' => MobileFeatureState::Visible, 'reason' => null, 'offline_behavior' => 'device_local'],
        'native_location' => ['state' => MobileFeatureState::Visible, 'reason' => null, 'offline_behavior' => 'device_local'],
        'native_scanner' => ['state' => MobileFeatureState::Visible, 'reason' => null, 'offline_behavior' => 'device_local'],
        'native_microphone' => ['state' => MobileFeatureState::Visible, 'reason' => null, 'offline_behavior' => 'device_local'],
        'native_biometrics' => ['state' => MobileFeatureState::Visible, 'reason' => null, 'offline_behavior' => 'device_local'],
    ];

    private const PERMISSION_GATES = [
        'records' => 'records.view',
        'offline_sync' => 'sync.view',
        'notifications' => 'notifications.view',
        'support' => 'support.view',
        'billing' => 'billing.view',
        'reports' => 'reports.view',
    ];

    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     * @param  array<string, mixed>  $permissions
     * @return array<string, mixed>
     */
    public function resolve(User $user, array $tenantContext, array $permissions): array
    {
        $tenant = $this->tenantFromContext($tenantContext);
        $globalFlags = $this->globalFlags();
        $tenantOverrides = $tenant instanceof Tenant ? $this->tenantOverrides($tenant) : new Collection;
        $userOverrides = $tenant instanceof Tenant ? $this->userOverrides($tenant, $user) : new Collection;
        $keys = $this->featureKeys($globalFlags, $tenantOverrides, $userOverrides);

        return [
            'version' => 'feature-flags-foundation-1',
            'resolved_at' => CarbonImmutable::now()->toIso8601String(),
            'tenant_id' => $tenant?->public_id,
            'items' => collect($keys)
                ->mapWithKeys(fn (string $key): array => [
                    $key => $this->featurePayload(
                        $key,
                        $globalFlags->get($key),
                        $tenantOverrides->get($key),
                        $userOverrides->get($key),
                        $permissions,
                    ),
                ])
                ->all(),
        ];
    }

    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     */
    private function tenantFromContext(array $tenantContext): ?Tenant
    {
        $currentTenant = is_array($tenantContext['current_tenant'] ?? null) ? $tenantContext['current_tenant'] : null;
        $publicId = is_string($currentTenant['id'] ?? null) ? $currentTenant['id'] : null;

        if ($publicId === null) {
            return null;
        }

        return Tenant::query()
            ->select(['id', 'public_id'])
            ->where('public_id', $publicId)
            ->first();
    }

    /**
     * @return Collection<string, MobileFeatureFlag>
     */
    private function globalFlags(): Collection
    {
        return MobileFeatureFlag::query()
            ->select(['id', 'key', 'name', 'default_state', 'reason', 'message', 'minimum_app_version', 'offline_behavior', 'metadata'])
            ->get()
            ->keyBy('key');
    }

    /**
     * @return Collection<string, TenantFeatureOverride>
     */
    private function tenantOverrides(Tenant $tenant): Collection
    {
        return TenantFeatureOverride::query()
            ->select(['id', 'tenant_id', 'feature_key', 'state', 'reason', 'message', 'offline_behavior', 'metadata'])
            ->where('tenant_id', $tenant->id)
            ->get()
            ->keyBy('feature_key');
    }

    /**
     * @return Collection<string, UserFeatureOverride>
     */
    private function userOverrides(Tenant $tenant, User $user): Collection
    {
        return UserFeatureOverride::query()
            ->select(['id', 'tenant_id', 'user_id', 'feature_key', 'state', 'reason', 'message', 'offline_behavior', 'metadata'])
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('feature_key');
    }

    /**
     * @param  Collection<string, MobileFeatureFlag>  $globalFlags
     * @param  Collection<string, TenantFeatureOverride>  $tenantOverrides
     * @param  Collection<string, UserFeatureOverride>  $userOverrides
     * @return array<int, string>
     */
    private function featureKeys(Collection $globalFlags, Collection $tenantOverrides, Collection $userOverrides): array
    {
        return collect(array_keys(self::FOUNDATION_DEFAULTS))
            ->merge($globalFlags->keys())
            ->merge($tenantOverrides->keys())
            ->merge($userOverrides->keys())
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function featurePayload(
        string $key,
        ?MobileFeatureFlag $flag,
        ?TenantFeatureOverride $tenantOverride,
        ?UserFeatureOverride $userOverride,
        array $permissions,
    ): array {
        $resolved = $this->baseFeature($key, $flag);

        if ($tenantOverride instanceof TenantFeatureOverride) {
            $resolved = $this->applyOverride($resolved, $tenantOverride->state, $tenantOverride->reason, $tenantOverride->message, $tenantOverride->offline_behavior, 'tenant_override');
        }

        if ($userOverride instanceof UserFeatureOverride) {
            $resolved = $this->applyOverride($resolved, $userOverride->state, $userOverride->reason, $userOverride->message, $userOverride->offline_behavior, 'user_override');
        }

        return $this->applyPermissionGate($key, $resolved, $permissions);
    }

    /**
     * @return array<string, mixed>
     */
    private function baseFeature(string $key, ?MobileFeatureFlag $flag): array
    {
        $default = self::FOUNDATION_DEFAULTS[$key] ?? [
            'state' => MobileFeatureState::Hidden,
            'reason' => 'feature_not_registered',
            'offline_behavior' => 'online_only',
        ];
        $state = $flag?->default_state instanceof MobileFeatureState ? $flag->default_state : $default['state'];

        return $this->payload(
            state: $state,
            reason: $flag?->reason ?? $default['reason'],
            message: $flag?->message,
            minimumAppVersion: $flag?->minimum_app_version,
            offlineBehavior: $flag?->offline_behavior ?? $default['offline_behavior'],
            source: $flag instanceof MobileFeatureFlag ? 'global_default' : 'foundation_default',
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function applyOverride(
        array $payload,
        MobileFeatureState $state,
        ?string $reason,
        ?string $message,
        ?string $offlineBehavior,
        string $source,
    ): array {
        return $this->payload(
            state: $state,
            reason: $reason,
            message: $message,
            minimumAppVersion: is_string($payload['minimum_app_version'] ?? null) ? $payload['minimum_app_version'] : null,
            offlineBehavior: $offlineBehavior ?: (string) $payload['offline_behavior'],
            source: $source,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $permissions
     * @return array<string, mixed>
     */
    private function applyPermissionGate(string $key, array $payload, array $permissions): array
    {
        $requiredPermission = self::PERMISSION_GATES[$key] ?? null;

        if ($requiredPermission === null || $payload['enabled'] !== true) {
            return $payload;
        }

        if (Arr::get($permissions, 'abilities.'.$requiredPermission) === true) {
            return $payload;
        }

        return $this->payload(
            state: MobileFeatureState::Blocked,
            reason: 'permission_denied',
            message: 'This feature is not available for your current role.',
            minimumAppVersion: is_string($payload['minimum_app_version'] ?? null) ? $payload['minimum_app_version'] : null,
            offlineBehavior: (string) $payload['offline_behavior'],
            source: 'permission_gate',
            nextAction: 'contact_admin',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(
        MobileFeatureState $state,
        ?string $reason,
        ?string $message,
        ?string $minimumAppVersion,
        string $offlineBehavior,
        string $source,
        ?string $nextAction = null,
    ): array {
        return [
            'state' => $state->value,
            'visible' => $state->isVisible(),
            'enabled' => $state->isEnabled(),
            'reason' => $reason,
            'next_action' => $nextAction ?? $this->nextAction($state),
            'minimum_app_version' => $minimumAppVersion,
            'offline_behavior' => $offlineBehavior,
            'message' => $message,
            'source' => $source,
        ];
    }

    private function nextAction(MobileFeatureState $state): ?string
    {
        return match ($state) {
            MobileFeatureState::Hidden,
            MobileFeatureState::Visible,
            MobileFeatureState::Beta,
            MobileFeatureState::Deprecated => null,
            MobileFeatureState::Disabled,
            MobileFeatureState::Blocked => 'contact_admin',
            MobileFeatureState::UpdateRequired => 'update_app',
            MobileFeatureState::OfflineLimited => 'continue_limited',
            MobileFeatureState::EmergencyDisabled => 'contact_support',
        };
    }
}
