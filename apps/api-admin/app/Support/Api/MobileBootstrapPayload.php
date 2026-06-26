<?php

namespace App\Support\Api;

use App\Models\MobileDeviceSession;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

final class MobileBootstrapPayload
{
    /**
     * @param  array{current_tenant?: array<string, mixed>|null, available_tenants?: array<int, array<string, mixed>>}  $tenantContext
     * @param  array<string, mixed>  $permissions
     * @param  array<string, mixed>  $features
     * @param  array<string, mixed>  $remoteConfig
     * @param  array<string, mixed>  $appVersion
     * @param  array<string, mixed>  $subscription
     * @param  array<string, mixed>  $notificationPolicy
     * @return array<string, mixed>
     */
    public static function make(
        User $user,
        MobileDeviceSession $session,
        Request $request,
        array $tenantContext = [],
        array $permissions = [],
        array $features = [],
        array $remoteConfig = [],
        array $appVersion = [],
        array $subscription = [],
        array $notificationPolicy = [],
    ): array {
        $now = CarbonImmutable::now();

        return [
            'user' => MobileAuthPayload::user($user),
            'device_session' => MobileAuthPayload::session($session),
            'current_tenant' => $tenantContext['current_tenant'] ?? null,
            'available_tenants' => $tenantContext['available_tenants'] ?? [],
            'permissions' => $permissions,
            'features' => $features ?: self::foundationFeatures($now),
            'remote_config' => $remoteConfig ?: self::foundationRemoteConfig($now),
            'app_version' => $appVersion ?: self::appVersion($request),
            'maintenance' => $appVersion['maintenance'] ?? self::maintenance(),
            'subscription' => $subscription ?: self::subscription($now),
            'notification_preferences' => $notificationPolicy['preferences'] ?? self::notificationPreferences(),
            'sync' => [
                'enabled' => false,
                'offline_queue_enabled' => true,
                'mode' => 'local_queue_until_sync_api',
                'reason' => 'sync_api_pending',
                'retry_after_seconds' => 300,
            ],
            'unread_notification_count' => is_int($notificationPolicy['unread_count'] ?? null) ? $notificationPolicy['unread_count'] : 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(array $features = [], array $remoteConfig = [], array $subscription = [], array $notificationPolicy = []): array
    {
        $now = CarbonImmutable::now();

        return [
            'bootstrap_version' => 'foundation-1',
            'config_version' => is_string($remoteConfig['config_version'] ?? null) ? $remoteConfig['config_version'] : 'remote-config-foundation-1',
            'features_version' => is_string($features['version'] ?? null) ? $features['version'] : 'foundation-1',
            'subscription_version' => is_string($subscription['subscription_version'] ?? null) ? $subscription['subscription_version'] : 'subscription-foundation-1',
            'notification_policy_version' => is_string($notificationPolicy['policy_version'] ?? null) ? $notificationPolicy['policy_version'] : 'notifications-foundation-1',
            'sync_cursor' => null,
            'issued_at' => $now->toIso8601String(),
            'fresh_until' => $now->addMinutes(15)->toIso8601String(),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function foundationFeatures(CarbonImmutable $now): array
    {
        return [
            'version' => 'foundation-1',
            'resolved_at' => $now->toIso8601String(),
            'items' => self::featureItems(),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function featureItems(): array
    {
        return [
            'records' => self::feature('disabled', 'records_api_pending'),
            'offline_sync' => self::feature('offline_limited', 'sync_api_pending'),
            'notifications' => self::feature('disabled', 'notifications_api_pending'),
            'support' => self::feature('disabled', 'support_api_pending'),
            'billing' => self::feature('disabled', 'billing_api_pending'),
            'reports' => self::feature('disabled', 'reports_api_pending'),
            'native_camera' => self::feature('visible', null),
            'native_files' => self::feature('visible', null),
            'native_share' => self::feature('visible', null),
            'native_location' => self::feature('visible', null),
            'native_scanner' => self::feature('visible', null),
            'native_microphone' => self::feature('visible', null),
            'native_biometrics' => self::feature('visible', null),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function feature(string $state, ?string $reason): array
    {
        return [
            'state' => $state,
            'enabled' => in_array($state, ['visible', 'enabled'], true),
            'reason' => $reason,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function foundationRemoteConfig(CarbonImmutable $now): array
    {
        return [
            'version' => 'remote-config-foundation-1',
            'config_version' => 'remote-config-foundation-1',
            'freshness' => [
                'state' => 'server_fresh',
                'issued_at' => $now->toIso8601String(),
                'fresh_until' => $now->addMinutes(15)->toIso8601String(),
            ],
            'compatibility' => [
                'status' => 'compatible',
                'minimum_app_version' => null,
                'incompatible_keys' => [],
            ],
            'defaults_used' => array_keys(self::remoteConfig()),
            'values' => self::remoteConfig(),
            'support_context' => [
                'source' => 'foundation_default',
                'global_config_count' => 0,
                'tenant_override_count' => 0,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function remoteConfig(): array
    {
        return [
            'dashboard' => [
                'widgets' => ['profile', 'sync_status', 'local_records'],
            ],
            'sync' => [
                'manual_sync_enabled' => false,
                'max_batch_size' => 50,
            ],
            'uploads' => [
                'max_attachment_mb' => 10,
                'allowed_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
            ],
            'support' => [
                'url' => null,
                'diagnostics_enabled' => false,
            ],
            'legal' => [
                'terms_url' => null,
                'privacy_url' => null,
            ],
            'app_lock' => [
                'pin_required' => false,
                'biometric_allowed' => true,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function maintenance(): array
    {
        return [
            'enabled' => false,
            'message' => null,
            'support_url' => null,
            'retry_after' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function subscription(CarbonImmutable $now): array
    {
        return [
            'status' => 'active',
            'plan' => [
                'key' => 'foundation',
                'name' => 'Foundation',
                'tier' => 'foundation',
            ],
            'trial' => [
                'active' => false,
                'ends_at' => null,
                'days_remaining' => null,
            ],
            'features_limited' => false,
            'limits' => [],
            'usage' => [],
            'available_actions' => ['view_plan', 'support'],
            'billing_portal' => [
                'available' => false,
                'url' => null,
                'reason' => 'billing_api_pending',
            ],
            'feature_impacts' => [
                'paid_features_blocked' => false,
                'reason' => null,
            ],
            'source' => 'foundation_default',
            'resolved_at' => $now->toIso8601String(),
            'subscription_version' => 'subscription-foundation-1',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function notificationPreferences(): array
    {
        return [
            'push_enabled' => false,
            'in_app_enabled' => true,
            'email_enabled' => false,
            'quiet_hours' => [
                'enabled' => false,
                'starts_at' => null,
                'ends_at' => null,
                'timezone' => null,
            ],
            'push_registration_required' => false,
            'status' => 'foundation_default',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function appVersion(Request $request): array
    {
        $reportedVersion = $request->header('X-Mobile-App-Version');
        $reportedVersionCode = $request->header('X-Mobile-App-Version-Code');

        return [
            'state' => 'supported',
            'reported_version' => is_string($reportedVersion) && trim($reportedVersion) !== '' ? $reportedVersion : null,
            'reported_version_code' => is_string($reportedVersionCode) && trim($reportedVersionCode) !== '' ? $reportedVersionCode : null,
            'status' => 'supported',
            'minimum_supported_version' => '1.0.0',
            'minimum_recommended_version' => null,
            'latest_version' => null,
            'optional_update' => false,
            'force_update' => false,
            'store_url' => null,
            'store_urls' => [
                'ios' => null,
                'android' => null,
            ],
            'message' => null,
            'support_url' => null,
            'retry_after' => null,
            'allowed_actions' => ['continue', 'logout', 'support'],
            'logout_allowed' => true,
            'policy_scope' => [
                'type' => 'foundation',
                'platform' => null,
                'tenant_id' => null,
                'cohort_key' => null,
            ],
            'maintenance' => self::maintenance(),
        ];
    }
}
