<?php

namespace App\Services\MobileConfig;

use App\Models\MobileRemoteConfig;
use App\Models\Tenant;
use App\Models\TenantRemoteConfigOverride;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

final class MobileRemoteConfigResolver
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private const FOUNDATION_DEFAULTS = [
        'app_lock' => [
            'pin_required' => false,
            'biometric_allowed' => true,
        ],
        'dashboard' => [
            'widgets' => ['profile', 'sync_status', 'local_records'],
        ],
        'legal' => [
            'terms_url' => null,
            'privacy_url' => null,
        ],
        'support' => [
            'url' => null,
            'diagnostics_enabled' => false,
        ],
        'sync' => [
            'manual_sync_enabled' => false,
            'max_batch_size' => 50,
        ],
        'uploads' => [
            'max_attachment_mb' => 10,
            'allowed_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
        ],
    ];

    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     * @return array<string, mixed>
     */
    public function resolve(User $user, array $tenantContext): array
    {
        $tenant = $this->tenantFromContext($tenantContext);
        $globalConfigs = $this->globalConfigs();
        $tenantOverrides = $tenant instanceof Tenant ? $this->tenantOverrides($tenant) : new Collection;
        $keys = $this->configKeys($globalConfigs, $tenantOverrides);
        $defaultsUsed = [];
        $values = [];

        foreach ($keys as $key) {
            $globalConfig = $globalConfigs->get($key);
            $tenantOverride = $tenantOverrides->get($key);

            if (! $globalConfig instanceof MobileRemoteConfig && ! $tenantOverride instanceof TenantRemoteConfigOverride) {
                $defaultsUsed[] = $key;
            }

            $values[$key] = $this->resolvedValue($key, $globalConfig, $tenantOverride);
        }

        ksort($values);
        sort($defaultsUsed);

        $now = CarbonImmutable::now();
        $version = $this->configVersion($tenant, $values, $globalConfigs, $tenantOverrides);

        return [
            'version' => $version,
            'config_version' => $version,
            'tenant_id' => $tenant?->public_id,
            'values' => $values,
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
            'defaults_used' => $defaultsUsed,
            'support_context' => [
                'source' => 'remote_config_resolver',
                'global_config_count' => $globalConfigs->count(),
                'tenant_override_count' => $tenantOverrides->count(),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $resolved
     * @return array<string, mixed>
     */
    public function endpointPayload(array $resolved): array
    {
        return [
            'config' => $resolved['values'] ?? [],
            'config_version' => $resolved['config_version'] ?? $resolved['version'] ?? 'remote-config-foundation-1',
            'tenant_id' => $resolved['tenant_id'] ?? null,
            'freshness' => $resolved['freshness'] ?? [],
            'compatibility' => $resolved['compatibility'] ?? [],
            'defaults_used' => $resolved['defaults_used'] ?? [],
            'support_context' => $resolved['support_context'] ?? [],
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
     * @return Collection<string, MobileRemoteConfig>
     */
    private function globalConfigs(): Collection
    {
        return MobileRemoteConfig::query()
            ->forResolver()
            ->get()
            ->keyBy('key');
    }

    /**
     * @return Collection<string, TenantRemoteConfigOverride>
     */
    private function tenantOverrides(Tenant $tenant): Collection
    {
        return TenantRemoteConfigOverride::query()
            ->forTenantResolver($tenant)
            ->get()
            ->keyBy('config_key');
    }

    /**
     * @param  Collection<string, MobileRemoteConfig>  $globalConfigs
     * @param  Collection<string, TenantRemoteConfigOverride>  $tenantOverrides
     * @return array<int, string>
     */
    private function configKeys(Collection $globalConfigs, Collection $tenantOverrides): array
    {
        return collect(array_keys(self::FOUNDATION_DEFAULTS))
            ->merge($globalConfigs->keys())
            ->merge($tenantOverrides->keys())
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function resolvedValue(
        string $key,
        ?MobileRemoteConfig $globalConfig,
        ?TenantRemoteConfigOverride $tenantOverride,
    ): array {
        $value = self::FOUNDATION_DEFAULTS[$key] ?? [];

        if ($globalConfig instanceof MobileRemoteConfig) {
            $value = array_replace_recursive($value, $this->arrayValue($globalConfig->value));
        }

        if ($tenantOverride instanceof TenantRemoteConfigOverride) {
            $value = array_replace_recursive($value, $this->arrayValue($tenantOverride->value));
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function arrayValue(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /**
     * @param  array<string, array<string, mixed>>  $values
     * @param  Collection<string, MobileRemoteConfig>  $globalConfigs
     * @param  Collection<string, TenantRemoteConfigOverride>  $tenantOverrides
     */
    private function configVersion(?Tenant $tenant, array $values, Collection $globalConfigs, Collection $tenantOverrides): string
    {
        $payload = json_encode([
            'tenant' => $tenant?->public_id,
            'values' => $values,
            'global_versions' => $globalConfigs->pluck('version', 'key')->all(),
            'tenant_versions' => $tenantOverrides->pluck('version', 'config_key')->all(),
        ]);

        return 'remote-config-'.substr(sha1(is_string($payload) ? $payload : ''), 0, 16);
    }
}
