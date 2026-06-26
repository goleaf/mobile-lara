<?php

namespace App\Services\MobileFeatures;

use App\Enums\MobileFeatureState;
use App\Models\MobileDeviceSession;
use App\Models\MobileFeatureFlag;
use App\Models\Tenant;
use App\Models\TenantFeatureOverride;
use App\Models\User;
use App\Models\UserFeatureOverride;
use App\Services\MobileVersion\MobileAppVersionPolicyResolver;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
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
        'diagnostics' => ['state' => MobileFeatureState::Visible, 'reason' => null, 'offline_behavior' => 'upload_online_export_local'],
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
        'diagnostics' => 'diagnostics.view',
    ];

    private const MAINTENANCE_ALLOWED_FEATURES = [
        'support',
    ];

    public function __construct(private MobileAppVersionPolicyResolver $versions) {}

    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     * @param  array<string, mixed>  $permissions
     * @return array<string, mixed>
     */
    public function resolve(User $user, array $tenantContext, array $permissions, ?Request $request = null, ?array $appVersion = null): array
    {
        $tenant = $this->tenantFromContext($tenantContext);
        $globalFlags = $this->globalFlags();
        $tenantOverrides = $tenant instanceof Tenant ? $this->tenantOverrides($tenant) : new Collection;
        $userOverrides = $tenant instanceof Tenant ? $this->userOverrides($tenant, $user) : new Collection;
        $keys = $this->featureKeys($globalFlags, $tenantOverrides, $userOverrides);
        $reportedVersion = $this->reportedVersion($request);
        $planKey = $this->planKey($tenantContext);
        $cohortKey = $this->cohortKey($request);
        $deviceContext = $this->deviceContext($request);
        $maintenanceContext = $this->maintenanceContext($request, $tenantContext, $appVersion);

        return [
            'version' => 'feature-flags-foundation-4',
            'reported_app_version' => $reportedVersion,
            'plan_key' => $planKey,
            'cohort_key' => $cohortKey,
            'device_context' => $deviceContext,
            'maintenance' => $maintenanceContext,
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
                        $reportedVersion,
                        $planKey,
                        $cohortKey,
                        $deviceContext,
                        $maintenanceContext,
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
            ->select(['id', 'key', 'name', 'default_state', 'reason', 'message', 'minimum_app_version', 'required_plans', 'allowed_cohorts', 'device_constraints', 'offline_behavior', 'metadata'])
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
        ?string $reportedVersion,
        string $planKey,
        ?string $cohortKey,
        array $deviceContext,
        array $maintenanceContext,
    ): array {
        $resolved = $this->baseFeature($key, $flag);

        if ($this->isEmergencyDisabled($resolved)) {
            return $this->applyEmergencyGate($resolved);
        }

        if ($tenantOverride instanceof TenantFeatureOverride) {
            $resolved = $this->applyOverride($resolved, $tenantOverride->state, $tenantOverride->reason, $tenantOverride->message, $tenantOverride->offline_behavior, 'tenant_override');

            if ($this->isEmergencyDisabled($resolved)) {
                return $this->applyEmergencyGate($resolved);
            }
        }

        if ($userOverride instanceof UserFeatureOverride) {
            $resolved = $this->applyOverride($resolved, $userOverride->state, $userOverride->reason, $userOverride->message, $userOverride->offline_behavior, 'user_override');

            if ($this->isEmergencyDisabled($resolved)) {
                return $this->applyEmergencyGate($resolved);
            }
        }

        $resolved = $this->applyMaintenanceGate($key, $resolved, $maintenanceContext);
        $resolved = $this->applyPlanGate($resolved, $planKey);
        $resolved = $this->applyCohortGate($resolved, $cohortKey);
        $resolved = $this->applyDeviceGate($resolved, $deviceContext);
        $resolved = $this->applyPermissionGate($key, $resolved, $permissions);

        return $this->applyVersionGate($resolved, $reportedVersion);
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
            requiredPlans: $this->stringList($flag?->required_plans),
            allowedCohorts: $this->stringList($flag?->allowed_cohorts),
            deviceConstraints: $this->deviceConstraints($flag?->device_constraints),
            offlineBehavior: $flag?->offline_behavior ?? $default['offline_behavior'],
            source: $flag instanceof MobileFeatureFlag ? 'global_default' : 'foundation_default',
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function isEmergencyDisabled(array $payload): bool
    {
        return ($payload['state'] ?? null) === MobileFeatureState::EmergencyDisabled->value;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function applyEmergencyGate(array $payload): array
    {
        return $this->payload(
            state: MobileFeatureState::EmergencyDisabled,
            reason: is_string($payload['reason'] ?? null) ? $payload['reason'] : 'emergency_disabled',
            message: is_string($payload['message'] ?? null) ? $payload['message'] : 'This feature is temporarily unavailable.',
            minimumAppVersion: is_string($payload['minimum_app_version'] ?? null) ? $payload['minimum_app_version'] : null,
            requiredPlans: $this->stringList($payload['required_plans'] ?? []),
            allowedCohorts: $this->stringList($payload['allowed_cohorts'] ?? []),
            deviceConstraints: $this->deviceConstraints($payload['device_constraints'] ?? []),
            offlineBehavior: (string) $payload['offline_behavior'],
            source: 'emergency_gate',
            nextAction: 'contact_support',
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array{enabled: bool, message: string|null, support_url: string|null, retry_after: int|null, allowed_actions: array<int, string>, policy_version: string|null}  $maintenanceContext
     * @return array<string, mixed>
     */
    private function applyMaintenanceGate(string $key, array $payload, array $maintenanceContext): array
    {
        if (($maintenanceContext['enabled'] ?? false) !== true || $payload['enabled'] !== true || in_array($key, self::MAINTENANCE_ALLOWED_FEATURES, true)) {
            return $payload;
        }

        return $this->payload(
            state: MobileFeatureState::Blocked,
            reason: 'maintenance_mode',
            message: is_string($maintenanceContext['message'] ?? null) ? $maintenanceContext['message'] : 'The mobile app is temporarily in maintenance.',
            minimumAppVersion: is_string($payload['minimum_app_version'] ?? null) ? $payload['minimum_app_version'] : null,
            requiredPlans: $this->stringList($payload['required_plans'] ?? []),
            allowedCohorts: $this->stringList($payload['allowed_cohorts'] ?? []),
            deviceConstraints: $this->deviceConstraints($payload['device_constraints'] ?? []),
            offlineBehavior: (string) $payload['offline_behavior'],
            source: 'maintenance_gate',
            nextAction: 'retry',
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
            requiredPlans: $this->stringList($payload['required_plans'] ?? []),
            allowedCohorts: $this->stringList($payload['allowed_cohorts'] ?? []),
            deviceConstraints: $this->deviceConstraints($payload['device_constraints'] ?? []),
            offlineBehavior: $offlineBehavior ?: (string) $payload['offline_behavior'],
            source: $source,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function applyPlanGate(array $payload, string $planKey): array
    {
        $requiredPlans = $this->stringList($payload['required_plans'] ?? []);

        if ($payload['enabled'] !== true || $requiredPlans === [] || in_array($planKey, $requiredPlans, true)) {
            return $payload;
        }

        return $this->payload(
            state: MobileFeatureState::Blocked,
            reason: 'plan_not_included',
            message: 'This feature is not included in the current plan.',
            minimumAppVersion: is_string($payload['minimum_app_version'] ?? null) ? $payload['minimum_app_version'] : null,
            requiredPlans: $requiredPlans,
            allowedCohorts: $this->stringList($payload['allowed_cohorts'] ?? []),
            deviceConstraints: $this->deviceConstraints($payload['device_constraints'] ?? []),
            offlineBehavior: (string) $payload['offline_behavior'],
            source: 'plan_gate',
            nextAction: 'upgrade_plan',
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function applyCohortGate(array $payload, ?string $cohortKey): array
    {
        $allowedCohorts = $this->stringList($payload['allowed_cohorts'] ?? []);

        if ($payload['enabled'] !== true || $allowedCohorts === [] || ($cohortKey !== null && in_array($cohortKey, $allowedCohorts, true))) {
            return $payload;
        }

        return $this->payload(
            state: MobileFeatureState::Blocked,
            reason: 'cohort_not_included',
            message: 'This feature is not available for your rollout group.',
            minimumAppVersion: is_string($payload['minimum_app_version'] ?? null) ? $payload['minimum_app_version'] : null,
            requiredPlans: $this->stringList($payload['required_plans'] ?? []),
            allowedCohorts: $allowedCohorts,
            deviceConstraints: $this->deviceConstraints($payload['device_constraints'] ?? []),
            offlineBehavior: (string) $payload['offline_behavior'],
            source: 'cohort_gate',
            nextAction: 'contact_admin',
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array{platform: string, device_id: string|null}  $deviceContext
     * @return array<string, mixed>
     */
    private function applyDeviceGate(array $payload, array $deviceContext): array
    {
        $constraints = $this->deviceConstraints($payload['device_constraints'] ?? []);
        $platforms = $this->stringList($constraints['platforms'] ?? []);
        $deviceIds = $this->stringList($constraints['device_ids'] ?? []);
        $deviceId = is_string($deviceContext['device_id'] ?? null) ? str($deviceContext['device_id'])->lower()->trim()->toString() : null;

        if ($payload['enabled'] !== true || ($platforms === [] && $deviceIds === [])) {
            return $payload;
        }

        if ($platforms !== [] && ! in_array($deviceContext['platform'], $platforms, true)) {
            return $this->deviceBlockedPayload($payload, $constraints);
        }

        if ($deviceIds !== [] && ($deviceId === null || ! in_array($deviceId, $deviceIds, true))) {
            return $this->deviceBlockedPayload($payload, $constraints);
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, array<int, string>>  $constraints
     * @return array<string, mixed>
     */
    private function deviceBlockedPayload(array $payload, array $constraints): array
    {
        return $this->payload(
            state: MobileFeatureState::Blocked,
            reason: 'device_not_supported',
            message: 'This feature is not available on this device.',
            minimumAppVersion: is_string($payload['minimum_app_version'] ?? null) ? $payload['minimum_app_version'] : null,
            requiredPlans: $this->stringList($payload['required_plans'] ?? []),
            allowedCohorts: $this->stringList($payload['allowed_cohorts'] ?? []),
            deviceConstraints: $constraints,
            offlineBehavior: (string) $payload['offline_behavior'],
            source: 'device_gate',
            nextAction: 'use_supported_device',
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
            requiredPlans: $this->stringList($payload['required_plans'] ?? []),
            allowedCohorts: $this->stringList($payload['allowed_cohorts'] ?? []),
            deviceConstraints: $this->deviceConstraints($payload['device_constraints'] ?? []),
            offlineBehavior: (string) $payload['offline_behavior'],
            source: 'permission_gate',
            nextAction: 'contact_admin',
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function applyVersionGate(array $payload, ?string $reportedVersion): array
    {
        $minimumAppVersion = is_string($payload['minimum_app_version'] ?? null) ? $payload['minimum_app_version'] : null;

        if ($payload['enabled'] !== true || $minimumAppVersion === null || trim($minimumAppVersion) === '') {
            return $payload;
        }

        if (! $this->isVersionBelowMinimum($reportedVersion, $minimumAppVersion)) {
            return $payload;
        }

        return $this->payload(
            state: MobileFeatureState::UpdateRequired,
            reason: 'minimum_app_version_required',
            message: is_string($payload['message'] ?? null) ? $payload['message'] : 'Update the app to use this feature.',
            minimumAppVersion: $minimumAppVersion,
            requiredPlans: $this->stringList($payload['required_plans'] ?? []),
            allowedCohorts: $this->stringList($payload['allowed_cohorts'] ?? []),
            deviceConstraints: $this->deviceConstraints($payload['device_constraints'] ?? []),
            offlineBehavior: (string) $payload['offline_behavior'],
            source: 'app_version_gate',
            nextAction: 'update_app',
        );
    }

    private function reportedVersion(?Request $request): ?string
    {
        $version = $request?->header('X-Mobile-App-Version');

        if (is_string($version) && trim($version) !== '') {
            return trim($version);
        }

        $session = $request?->attributes->get('mobile_device_session');

        if (! $session instanceof MobileDeviceSession) {
            return null;
        }

        return is_string($session->app_version) && trim($session->app_version) !== '' ? $session->app_version : null;
    }

    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     */
    private function planKey(array $tenantContext): string
    {
        $currentTenant = is_array($tenantContext['current_tenant'] ?? null) ? $tenantContext['current_tenant'] : [];
        $plan = Arr::get($tenantContext, 'subscription.plan.key')
            ?? Arr::get($tenantContext, 'subscription.plan')
            ?? Arr::get($currentTenant, 'plan')
            ?? Arr::get($currentTenant, 'subscription.plan')
            ?? Arr::get($currentTenant, 'billing.plan');

        return $this->normalizedKey($plan) ?? 'foundation';
    }

    private function cohortKey(?Request $request): ?string
    {
        $cohort = $request?->header('X-Mobile-Cohort')
            ?? $request?->header('X-Mobile-Rollout-Cohort');

        $cohort = $this->normalizedKey($cohort);

        return $cohort !== null && preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $cohort) === 1 ? $cohort : null;
    }

    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     * @param  array<string, mixed>|null  $appVersion
     * @return array{enabled: bool, message: string|null, support_url: string|null, retry_after: int|null, allowed_actions: array<int, string>, policy_version: string|null}
     */
    private function maintenanceContext(?Request $request, array $tenantContext, ?array $appVersion): array
    {
        $resolved = $appVersion ?? ($request instanceof Request ? $this->versions->resolve($request, $tenantContext) : []);
        $maintenance = is_array($resolved['maintenance'] ?? null) ? $resolved['maintenance'] : [];
        $retryAfter = $maintenance['retry_after'] ?? $resolved['retry_after'] ?? null;

        return [
            'enabled' => ($maintenance['enabled'] ?? false) === true,
            'message' => is_string($maintenance['message'] ?? null) ? $maintenance['message'] : (is_string($resolved['message'] ?? null) ? $resolved['message'] : null),
            'support_url' => is_string($maintenance['support_url'] ?? null) ? $maintenance['support_url'] : (is_string($resolved['support_url'] ?? null) ? $resolved['support_url'] : null),
            'retry_after' => is_numeric($retryAfter) ? (int) $retryAfter : null,
            'allowed_actions' => $this->stringList($resolved['allowed_actions'] ?? []),
            'policy_version' => is_string($resolved['policy_version'] ?? null) ? $resolved['policy_version'] : null,
        ];
    }

    /**
     * @return array{platform: string, device_id: string|null}
     */
    private function deviceContext(?Request $request): array
    {
        $session = $request?->attributes->get('mobile_device_session');
        $platform = $request?->header('X-Mobile-Platform');
        $deviceId = $request?->header('X-Mobile-Device-Id');

        if ((! is_string($platform) || trim($platform) === '') && $session instanceof MobileDeviceSession) {
            $platform = $session->platform;
        }

        if ((! is_string($deviceId) || trim($deviceId) === '') && $session instanceof MobileDeviceSession) {
            $deviceId = $session->device_id;
        }

        return [
            'platform' => $this->normalizedKey($platform) ?? 'unknown',
            'device_id' => $this->normalizedKey($deviceId),
        ];
    }

    private function normalizedKey(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return str($value)->lower()->trim()->toString();
    }

    private function isVersionBelowMinimum(?string $reportedVersion, string $minimumAppVersion): bool
    {
        if ($reportedVersion === null || trim($reportedVersion) === '') {
            return true;
        }

        return version_compare($reportedVersion, $minimumAppVersion, '<');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(
        MobileFeatureState $state,
        ?string $reason,
        ?string $message,
        ?string $minimumAppVersion,
        array $requiredPlans,
        array $allowedCohorts,
        array $deviceConstraints,
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
            'required_plans' => $requiredPlans,
            'allowed_cohorts' => $allowedCohorts,
            'device_constraints' => $deviceConstraints,
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

    /**
     * @return array<int, string>
     */
    private function stringList(mixed $value): array
    {
        $items = is_array($value) ? $value : preg_split('/[\r\n,]+/', (string) $value);

        return collect($items)
            ->filter(static fn (mixed $item): bool => is_string($item) && trim($item) !== '')
            ->map(static fn (string $item): string => str($item)->lower()->trim()->toString())
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array{platforms?: array<int, string>, device_ids?: array<int, string>}
     */
    private function deviceConstraints(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_filter([
            'platforms' => $this->stringList($value['platforms'] ?? []),
            'device_ids' => $this->stringList($value['device_ids'] ?? []),
        ]);
    }
}
