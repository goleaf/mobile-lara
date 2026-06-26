<?php

namespace App\Services\MobileAccess;

use App\Services\MobileLocal\SettingsRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;

final class MobileAccessPolicy
{
    /**
     * @var list<string>
     */
    private const ENABLED_FEATURE_STATES = [
        'visible',
        'enabled',
        'beta',
        'deprecated',
        'offline_limited',
    ];

    /**
     * @var list<string>
     */
    private const SUBSCRIPTION_SAFE_FEATURES = [
        'billing',
        'profile',
        'settings',
        'support',
    ];

    /**
     * @var list<string>
     */
    private const MAINTENANCE_SAFE_FEATURES = [
        'profile',
        'settings',
        'support',
    ];

    public function __construct(private readonly SettingsRepository $settings) {}

    /**
     * @return array{allowed: bool, feature: string, permission: string|null, reason: string|null, message: string, next_action: string|null, source: string}
     */
    public function decision(string $feature, ?string $permission = null): array
    {
        $feature = $this->normalizeKey($feature);
        $permission = $permission !== null ? $this->normalizePermission($permission) : null;
        $data = $this->bootstrapData();

        if ($data === []) {
            return $this->allow($feature, $permission, 'bootstrap_not_cached');
        }

        if ($this->hasNoActiveTenant($data) && ! in_array($feature, self::SUBSCRIPTION_SAFE_FEATURES, true)) {
            return $this->deny(
                feature: $feature,
                permission: $permission,
                reason: 'no_active_tenant',
                message: 'Select an active workspace before opening this mobile feature.',
                nextAction: 'select_tenant',
                source: 'bootstrap_tenant_context',
            );
        }

        if ($this->maintenanceIsEnabled($data) && ! in_array($feature, self::MAINTENANCE_SAFE_FEATURES, true)) {
            return $this->deny(
                feature: $feature,
                permission: $permission,
                reason: 'maintenance_mode',
                message: $this->stringValue(Arr::get($data, 'maintenance.message'))
                    ?? $this->stringValue(Arr::get($data, 'app_version.maintenance.message'))
                    ?? 'The mobile app is temporarily in maintenance.',
                nextAction: 'retry',
                source: 'bootstrap_maintenance',
            );
        }

        if ($this->subscriptionIsLimited($data) && ! in_array($feature, self::SUBSCRIPTION_SAFE_FEATURES, true)) {
            return $this->deny(
                feature: $feature,
                permission: $permission,
                reason: $this->stringValue(Arr::get($data, 'subscription.feature_impacts.reason')) ?? 'subscription_limited',
                message: 'This feature is not available for the current subscription state.',
                nextAction: 'contact_admin',
                source: 'bootstrap_subscription',
            );
        }

        if ($feature === 'notifications' && ! $this->notificationsAreEnabled($data)) {
            return $this->deny(
                feature: $feature,
                permission: $permission,
                reason: 'notifications_disabled',
                message: 'Notifications are disabled for this workspace.',
                nextAction: 'contact_admin',
                source: 'bootstrap_notification_policy',
            );
        }

        if ($feature === 'offline_sync' && ! $this->syncIsEnabled($data)) {
            return $this->deny(
                feature: $feature,
                permission: $permission,
                reason: $this->stringValue(Arr::get($data, 'sync.reason')) ?? 'sync_disabled_by_policy',
                message: 'Sync is disabled by the current workspace policy.',
                nextAction: 'contact_admin',
                source: 'bootstrap_sync_policy',
            );
        }

        $featurePayload = $this->featurePayload($data, $feature);

        if ($featurePayload !== null && ! $this->featurePayloadIsEnabled($featurePayload)) {
            return $this->deny(
                feature: $feature,
                permission: $permission,
                reason: $this->stringValue($featurePayload['reason'] ?? null) ?? $this->stringValue($featurePayload['state'] ?? null) ?? 'feature_disabled',
                message: $this->stringValue($featurePayload['message'] ?? null) ?? 'This feature is disabled by the Admin/API control plane.',
                nextAction: $this->stringValue($featurePayload['next_action'] ?? null),
                source: $this->stringValue($featurePayload['source'] ?? null) ?? 'bootstrap_feature',
            );
        }

        if ($permission !== null && ! $this->permissionIsGranted($data, $permission)) {
            return $this->deny(
                feature: $feature,
                permission: $permission,
                reason: 'permission_denied',
                message: 'Your current workspace role cannot open this mobile feature.',
                nextAction: 'contact_admin',
                source: 'bootstrap_permissions',
            );
        }

        return $this->allow($feature, $permission, $featurePayload === null ? 'feature_not_declared' : 'bootstrap_policy');
    }

    public function allows(string $feature, ?string $permission = null): bool
    {
        return $this->decision($feature, $permission)['allowed'];
    }

    /**
     * @param  list<array<string, mixed>>  $actions
     * @return list<array<string, mixed>>
     */
    public function filterActions(array $actions): array
    {
        return array_values(array_filter(
            $actions,
            fn (array $action): bool => $this->allows(
                (string) ($action['feature'] ?? 'settings'),
                $this->nullableString($action['permission'] ?? null),
            ),
        ));
    }

    /**
     * @return list<array{route: string, label: string, icon: string, primary: bool}>
     */
    public function primaryNavigationItems(): array
    {
        $items = $this->filterActions([
            ['route' => 'mobile.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'primary' => false, 'feature' => 'settings'],
            ['route' => 'mobile.search', 'label' => 'Search', 'icon' => 'search', 'primary' => false, 'feature' => 'search'],
        ]);

        if ($this->hasCreateActions()) {
            $items[] = ['route' => 'mobile.create', 'label' => 'Create', 'icon' => 'plus', 'primary' => true];
        }

        return array_values(array_merge($items, $this->filterActions([
            ['route' => 'mobile.notifications', 'label' => 'Notifications', 'icon' => 'bell', 'primary' => false, 'feature' => 'notifications', 'permission' => 'notifications.view'],
            ['route' => 'mobile.profile', 'label' => 'Profile', 'icon' => 'user', 'primary' => false, 'feature' => 'profile'],
        ])));
    }

    private function hasCreateActions(): bool
    {
        return $this->filterActions([
            ['feature' => 'records', 'permission' => 'records.create'],
            ['feature' => 'native_scanner'],
            ['feature' => 'native_files'],
        ]) !== [];
    }

    /**
     * @return array<string, mixed>
     */
    public function cachedBootstrapData(): array
    {
        return $this->bootstrapData();
    }

    /**
     * @return array<string, mixed>
     */
    private function bootstrapData(): array
    {
        try {
            $envelope = $this->settings->bootstrapContext();
        } catch (QueryException $exception) {
            if ($this->isMissingSettingsTable($exception)) {
                return [];
            }

            throw $exception;
        }

        return is_array($envelope['data'] ?? null) ? $envelope['data'] : [];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function hasNoActiveTenant(array $data): bool
    {
        return Arr::get($data, 'permissions.status') === 'no_active_tenant'
            || Arr::get($data, 'subscription.status') === 'no_active_tenant';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function maintenanceIsEnabled(array $data): bool
    {
        return Arr::get($data, 'maintenance.enabled') === true
            || Arr::get($data, 'app_version.maintenance.enabled') === true;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function subscriptionIsLimited(array $data): bool
    {
        return Arr::get($data, 'subscription.features_limited') === true
            || Arr::get($data, 'subscription.feature_impacts.paid_features_blocked') === true;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function notificationsAreEnabled(array $data): bool
    {
        return Arr::get($data, 'notification_preferences.in_app_enabled') !== false;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncIsEnabled(array $data): bool
    {
        return Arr::get($data, 'sync.enabled') === true;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|null
     */
    private function featurePayload(array $data, string $feature): ?array
    {
        $payload = Arr::get($data, 'features.items.'.$feature);

        return is_array($payload) ? $payload : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function featurePayloadIsEnabled(array $payload): bool
    {
        if (is_bool($payload['enabled'] ?? null)) {
            return $payload['enabled'];
        }

        $state = $this->stringValue($payload['state'] ?? null);

        return $state !== null && in_array($state, self::ENABLED_FEATURE_STATES, true);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function permissionIsGranted(array $data, string $permission): bool
    {
        $status = $this->stringValue(Arr::get($data, 'permissions.status'));

        if ($status === null || $status === 'not_configured') {
            return true;
        }

        if ($status === 'no_active_tenant') {
            return false;
        }

        if (Arr::get($data, 'permissions.abilities.'.$permission) === true) {
            return true;
        }

        $abilityList = Arr::get($data, 'permissions.ability_list');

        return is_array($abilityList) && in_array($permission, $abilityList, true);
    }

    /**
     * @return array{allowed: true, feature: string, permission: string|null, reason: null, message: string, next_action: null, source: string}
     */
    private function allow(string $feature, ?string $permission, string $source): array
    {
        return [
            'allowed' => true,
            'feature' => $feature,
            'permission' => $permission,
            'reason' => null,
            'message' => 'Allowed by cached Admin/API bootstrap policy.',
            'next_action' => null,
            'source' => $source,
        ];
    }

    /**
     * @return array{allowed: false, feature: string, permission: string|null, reason: string, message: string, next_action: string|null, source: string}
     */
    private function deny(string $feature, ?string $permission, string $reason, string $message, ?string $nextAction, string $source): array
    {
        return [
            'allowed' => false,
            'feature' => $feature,
            'permission' => $permission,
            'reason' => $reason,
            'message' => $message,
            'next_action' => $nextAction,
            'source' => $source,
        ];
    }

    private function normalizeKey(string $value): string
    {
        $value = str($value)->lower()->trim()->replace('-', '_')->toString();

        return $value !== '' ? $value : 'settings';
    }

    private function normalizePermission(string $value): string
    {
        return str($value)->lower()->trim()->toString();
    }

    private function nullableString(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function stringValue(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function isMissingSettingsTable(QueryException $exception): bool
    {
        $message = $exception->getMessage();

        return str_contains($message, 'mobile_local_settings')
            && (str_contains($message, 'no such table') || str_contains($message, 'Base table or view not found'));
    }
}
