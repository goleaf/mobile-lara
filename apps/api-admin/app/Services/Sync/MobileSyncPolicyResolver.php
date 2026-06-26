<?php

namespace App\Services\Sync;

use App\Models\Tenant;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;

final class MobileSyncPolicyResolver
{
    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     * @param  array<string, mixed>  $permissions
     * @param  array<string, mixed>  $remoteConfig
     * @param  array<string, mixed>  $subscription
     * @param  array<string, mixed>  $appVersion
     * @return array<string, mixed>
     */
    public function resolve(array $tenantContext, array $permissions, array $remoteConfig = [], array $subscription = [], array $appVersion = []): array
    {
        $tenant = $this->tenantFromContext($tenantContext);
        $now = CarbonImmutable::now();

        if (! $tenant instanceof Tenant) {
            return $this->policy(
                enabled: false,
                reason: 'no_active_tenant',
                source: 'tenant_context',
                settings: [],
                remoteConfig: $remoteConfig,
                now: $now,
                tenant: null,
            );
        }

        $settings = is_array($tenant->settings) ? $tenant->settings : [];
        $syncSettings = $this->arrayValue(Arr::get($settings, 'sync'));
        $reason = $this->blockingReason($permissions, $subscription, $appVersion);
        $enabled = $reason === null && $this->boolValue($syncSettings['enabled'] ?? null, $this->remoteBool($remoteConfig, 'manual_sync_enabled', false));

        return $this->policy(
            enabled: $enabled,
            reason: $reason ?? ($enabled ? null : 'sync_disabled_by_policy'),
            source: 'tenant_sync_settings',
            settings: $syncSettings,
            remoteConfig: $remoteConfig,
            now: $now,
            tenant: $tenant,
        );
    }

    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     */
    private function tenantFromContext(array $tenantContext): ?Tenant
    {
        $currentTenant = is_array($tenantContext['current_tenant'] ?? null) ? $tenantContext['current_tenant'] : null;
        $publicId = is_string($currentTenant['id'] ?? null) ? $currentTenant['id'] : null;

        if ($publicId === null || trim($publicId) === '') {
            return null;
        }

        return Tenant::query()
            ->select(['id', 'public_id', 'settings', 'updated_at'])
            ->where('public_id', $publicId)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $permissions
     * @param  array<string, mixed>  $subscription
     * @param  array<string, mixed>  $appVersion
     */
    private function blockingReason(array $permissions, array $subscription, array $appVersion): ?string
    {
        if (Arr::get($permissions, 'abilities.sync.run') !== true) {
            return 'permission_denied';
        }

        if (($subscription['features_limited'] ?? false) === true) {
            return 'subscription_limited';
        }

        if (Arr::get($appVersion, 'maintenance.enabled') === true) {
            return 'maintenance_mode';
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $remoteConfig
     * @return array<string, mixed>
     */
    private function policy(bool $enabled, ?string $reason, string $source, array $settings, array $remoteConfig, CarbonImmutable $now, ?Tenant $tenant): array
    {
        $maxBatchSize = $this->intValue($settings['max_batch_size'] ?? null, $this->remoteInt($remoteConfig, 'max_batch_size', 50), 1, 500);

        return [
            'enabled' => $enabled,
            'manual_sync_enabled' => $this->boolValue($settings['manual_sync_enabled'] ?? null, $this->remoteBool($remoteConfig, 'manual_sync_enabled', false)),
            'offline_queue_enabled' => $this->boolValue($settings['offline_queue_enabled'] ?? null, true),
            'server_replay_enabled' => $enabled,
            'mode' => $enabled ? 'server_replay_ready' : 'local_queue_until_sync_api',
            'reason' => $reason,
            'max_batch_size' => $maxBatchSize,
            'retry_after_seconds' => $this->intValue($settings['retry_after_seconds'] ?? null, 300, 60, 86400),
            'stale_after_seconds' => $this->intValue($settings['stale_after_seconds'] ?? null, 900, 60, 86400),
            'conflict_policy' => $this->stringValue($settings['conflict_policy'] ?? null, 'server_review'),
            'server_endpoints' => [
                'bootstrap' => true,
                'push' => $enabled,
                'pull' => $enabled,
                'acknowledge' => $enabled,
            ],
            'source' => $source,
            'resolved_at' => $now->toIso8601String(),
            'policy_version' => $tenant instanceof Tenant ? $this->version($tenant, $settings, $maxBatchSize, $enabled) : 'sync-none',
        ];
    }

    /**
     * @param  array<string, mixed>  $remoteConfig
     */
    private function remoteBool(array $remoteConfig, string $key, bool $default): bool
    {
        $value = Arr::get($remoteConfig, 'values.sync.'.$key);

        return is_bool($value) ? $value : $default;
    }

    /**
     * @param  array<string, mixed>  $remoteConfig
     */
    private function remoteInt(array $remoteConfig, string $key, int $default): int
    {
        $value = Arr::get($remoteConfig, 'values.sync.'.$key);

        return is_numeric($value) ? (int) $value : $default;
    }

    private function boolValue(mixed $value, bool $default): bool
    {
        return is_bool($value) ? $value : $default;
    }

    private function intValue(mixed $value, int $default, int $min, int $max): int
    {
        $value = is_numeric($value) ? (int) $value : $default;

        return min($max, max($min, $value));
    }

    private function stringValue(mixed $value, string $default): string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : $default;
    }

    /**
     * @return array<string, mixed>
     */
    private function arrayValue(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    private function version(Tenant $tenant, array $settings, int $maxBatchSize, bool $enabled): string
    {
        $payload = json_encode([
            'tenant_id' => $tenant->id,
            'settings' => $settings,
            'max_batch_size' => $maxBatchSize,
            'enabled' => $enabled,
            'updated_at' => $tenant->updated_at?->toIso8601String(),
        ]);

        return 'sync-'.substr(sha1(is_string($payload) ? $payload : ''), 0, 16);
    }
}
