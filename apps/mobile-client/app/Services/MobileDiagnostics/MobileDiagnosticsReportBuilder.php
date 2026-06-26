<?php

namespace App\Services\MobileDiagnostics;

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Models\MobileLocalOfflineAction;
use App\Services\MobileConfig\MobileRemoteConfigStore;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\SettingsRepository;
use App\Services\Native\DeviceService;
use Carbon\CarbonImmutable;
use Composer\InstalledVersions;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class MobileDiagnosticsReportBuilder
{
    /**
     * @var list<string>
     */
    private const REDACTED_FIELDS = [
        'access_token',
        'api_key',
        'api_token',
        'authorization',
        'bearer',
        'client_secret',
        'cookie',
        'credential',
        'credentials',
        'email',
        'headers',
        'password',
        'payload',
        'private_key',
        'refresh_token',
        'secret',
        'token',
    ];

    public function __construct(
        private readonly SettingsRepository $settings,
        private readonly MobileRemoteConfigStore $remoteConfig,
        private readonly MobileNetworkState $networkState,
        private readonly DeviceService $devices,
        private readonly OfflineActionRepository $offlineActions,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function snapshot(): array
    {
        $bootstrapData = $this->bootstrapData();

        return [
            'generated_at' => CarbonImmutable::now()->toIso8601String(),
            'app' => $this->appSnapshot(),
            'user' => $this->userSnapshot($bootstrapData),
            'tenant' => $this->tenantSnapshot($bootstrapData),
            'features' => $this->featureSnapshot($bootstrapData),
            'remote_config' => $this->redact($this->remoteConfig->snapshot()),
            'network' => $this->networkSnapshot(),
            'sync' => $this->syncSnapshot($bootstrapData),
            'failed_sync_actions' => $this->failedSyncActionSummaries(),
            'device' => $this->devices->snapshot(),
            'redactions_applied' => [
                'tokens',
                'secrets',
                'authorization_headers',
                'queued_payloads',
                'raw_personal_data',
            ],
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->snapshot(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    /**
     * @return list<array{key: string, label: string, value: string}>
     */
    public function summaryRows(): array
    {
        $snapshot = $this->snapshot();

        return [
            [
                'key' => 'app-version',
                'label' => 'App version',
                'value' => (string) Arr::get($snapshot, 'app.app_version', 'Unknown'),
            ],
            [
                'key' => 'api-base-url',
                'label' => 'API base URL',
                'value' => (string) Arr::get($snapshot, 'app.api_base_url', 'Not configured'),
            ],
            [
                'key' => 'tenant',
                'label' => 'Current tenant',
                'value' => (string) Arr::get($snapshot, 'tenant.tenant_id', 'None'),
            ],
            [
                'key' => 'user',
                'label' => 'User id',
                'value' => (string) Arr::get($snapshot, 'user.id', 'Guest'),
            ],
            [
                'key' => 'features',
                'label' => 'Feature snapshot',
                'value' => $this->featureSummary(Arr::get($snapshot, 'features.items', [])),
            ],
            [
                'key' => 'remote-config',
                'label' => 'Remote config',
                'value' => (string) Arr::get($snapshot, 'remote_config.version', 'Unknown'),
            ],
            [
                'key' => 'network',
                'label' => 'Network',
                'value' => (string) Arr::get($snapshot, 'network.summary', 'Unknown'),
            ],
            [
                'key' => 'sync',
                'label' => 'Sync queue',
                'value' => $this->syncSummary(Arr::get($snapshot, 'sync', [])),
            ],
            [
                'key' => 'failed-actions',
                'label' => 'Failed sync actions',
                'value' => (string) count(Arr::get($snapshot, 'failed_sync_actions', [])),
            ],
            [
                'key' => 'redactions',
                'label' => 'Privacy protection',
                'value' => 'Tokens, secrets, headers, payloads, and raw personal data are redacted.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function appSnapshot(): array
    {
        return [
            'app_version' => (string) config('nativephp.version', config('app.version', '1.0.0')),
            'api_base_url' => $this->safeUrl((string) config('mobile_auth.api.base_url', '')),
            'laravel_version' => app()->version(),
            'livewire_version' => '4.x',
            'nativephp_mobile_version' => InstalledVersions::isInstalled('nativephp/mobile')
                ? InstalledVersions::getPrettyVersion('nativephp/mobile')
                : 'not-installed',
            'nativephp_running' => config('nativephp-internal.running') === true,
            'nativephp_app_id' => (string) config('nativephp.app_id', 'not-configured'),
            'nativephp_start_url' => $this->safeUrl((string) config('nativephp.start_url', '')),
        ];
    }

    /**
     * @param  array<string, mixed>  $bootstrapData
     * @return array{authenticated: bool, id: int|string|null, source: string}
     */
    private function userSnapshot(array $bootstrapData): array
    {
        $authId = Auth::id();
        $bootstrapId = Arr::get($bootstrapData, 'user.id');
        $id = $authId ?? (is_int($bootstrapId) || is_string($bootstrapId) ? $bootstrapId : null);

        return [
            'authenticated' => $id !== null,
            'id' => $id,
            'source' => $authId !== null ? 'auth_guard' : ($id !== null ? 'bootstrap_cache' : 'guest'),
        ];
    }

    /**
     * @param  array<string, mixed>  $bootstrapData
     * @return array{tenant_id: int|string|null, status: string|null, subscription_state: string|null}
     */
    private function tenantSnapshot(array $bootstrapData): array
    {
        $tenant = Arr::get($bootstrapData, 'current_tenant');

        if (! is_array($tenant)) {
            return [
                'tenant_id' => null,
                'status' => null,
                'subscription_state' => null,
            ];
        }

        $tenantId = $tenant['id'] ?? null;

        return [
            'tenant_id' => is_int($tenantId) || is_string($tenantId) ? $tenantId : null,
            'status' => $this->stringOrNull($tenant['status'] ?? null),
            'subscription_state' => $this->stringOrNull($tenant['subscription_state'] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $bootstrapData
     * @return array<string, mixed>
     */
    private function featureSnapshot(array $bootstrapData): array
    {
        $features = Arr::get($bootstrapData, 'features');
        $items = is_array($features) ? Arr::get($features, 'items', []) : [];
        $summary = [];

        if (is_array($items)) {
            foreach ($items as $key => $item) {
                if (! is_string($key) || ! is_array($item)) {
                    continue;
                }

                $summary[$key] = [
                    'state' => $this->stringOrNull($item['state'] ?? null),
                    'enabled' => (bool) ($item['enabled'] ?? false),
                    'visible' => (bool) ($item['visible'] ?? false),
                    'reason' => $this->stringOrNull($item['reason'] ?? null),
                    'source' => $this->stringOrNull($item['source'] ?? null),
                ];
            }
        }

        ksort($summary);

        return [
            'version' => is_array($features) ? $this->stringOrNull($features['version'] ?? null) : null,
            'items' => $summary,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function networkSnapshot(): array
    {
        $status = $this->networkState->status();

        return [
            'online' => $status->isOnline,
            'state' => $status->stateLabel(),
            'connection_type' => $status->connectionType,
            'connection_type_label' => $status->connectionTypeLabel(),
            'metered' => $status->isMetered,
            'metered_label' => $status->meteredLabel(),
            'constrained' => $status->isConstrained,
            'constrained_label' => $status->constrainedLabel(),
            'source' => $status->source,
            'source_label' => $status->sourceLabel(),
            'native_status_available' => $status->nativeStatusAvailable,
            'fallback_check_used' => $status->fallbackCheckUsed,
            'summary' => $status->summary(),
        ];
    }

    /**
     * @param  array<string, mixed>  $bootstrapData
     * @return array<string, mixed>
     */
    private function syncSnapshot(array $bootstrapData): array
    {
        try {
            $settings = $this->settings->find();
        } catch (QueryException) {
            $settings = null;
        }

        return [
            'enabled' => (bool) Arr::get($bootstrapData, 'sync.enabled', false),
            'reason' => $this->stringOrNull(Arr::get($bootstrapData, 'sync.reason')),
            'policy' => $this->remoteConfig->syncSettings(),
            'last_sync_at' => $settings?->last_sync_at?->toIso8601String(),
            'pending_actions' => $this->safeOfflineCount(fn (): int => $this->offlineActions->pendingCount()),
            'failed_actions' => $this->safeOfflineCount(fn (): int => $this->offlineActions->failedCount()),
            'conflict_actions' => $this->safeOfflineCount(fn (): int => $this->offlineActions->conflicts()->count()),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function failedSyncActionSummaries(): array
    {
        try {
            $failedActions = $this->offlineActions->byStatus(MobileLocalOfflineAction::STATUS_FAILED, 5);
        } catch (QueryException) {
            return [];
        }

        return $failedActions
            ->map(fn (MobileLocalOfflineAction $offlineAction): array => [
                'id' => $offlineAction->getKey(),
                'action_type' => $offlineAction->action_type,
                'method' => $offlineAction->method,
                'endpoint' => $this->safeEndpoint($offlineAction->endpoint),
                'attempts' => $offlineAction->attempts,
                'last_error' => $this->redactedString($offlineAction->last_error),
                'conflict_status' => $offlineAction->conflict_status,
                'created_at' => $offlineAction->created_at?->toIso8601String(),
                'available_at' => $offlineAction->available_at?->toIso8601String(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function bootstrapData(): array
    {
        try {
            $envelope = $this->settings->cachedBootstrapContext();
        } catch (QueryException) {
            return [];
        }

        $data = is_array($envelope) ? ($envelope['data'] ?? null) : null;

        return is_array($data) ? $data : [];
    }

    private function safeOfflineCount(callable $callback): ?int
    {
        try {
            return $callback();
        } catch (QueryException) {
            return null;
        }
    }

    private function safeEndpoint(?string $endpoint): ?string
    {
        if ($endpoint === null || trim($endpoint) === '') {
            return null;
        }

        return Str::of($endpoint)
            ->before('?')
            ->limit(160, '')
            ->toString();
    }

    private function redactedString(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        $value = preg_replace('/Bearer\s+[A-Za-z0-9._\-]+/i', 'Bearer [redacted]', $value) ?: $value;
        $value = preg_replace('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', '[redacted-email]', $value) ?: $value;

        return Str::of($value)->limit(160, '...')->toString();
    }

    private function safeUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return 'Not configured';
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            return 'Invalid configured URL';
        }

        $scheme = is_string($parts['scheme'] ?? null) ? $parts['scheme'].'://' : '';
        $host = is_string($parts['host'] ?? null) ? $parts['host'] : '';
        $port = is_int($parts['port'] ?? null) ? ':'.$parts['port'] : '';
        $path = is_string($parts['path'] ?? null) ? $parts['path'] : '';

        return $host === '' ? 'Configured URL without host' : "{$scheme}{$host}{$port}{$path}";
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function redact(mixed $value, ?string $key = null): mixed
    {
        if ($key !== null && $this->isSensitiveKey($key)) {
            return '[redacted]';
        }

        if (is_array($value)) {
            $redacted = [];

            foreach ($value as $childKey => $childValue) {
                $redacted[$childKey] = $this->redact($childValue, is_string($childKey) ? $childKey : null);
            }

            return $redacted;
        }

        return is_string($value) ? $this->redactedString($value) : $value;
    }

    private function isSensitiveKey(string $key): bool
    {
        $key = Str::of(Str::snake($key))->lower()->replace(['-', ' ', '.'], '_')->toString();

        foreach (self::REDACTED_FIELDS as $field) {
            if ($key === $field || str_contains($key, "_{$field}") || str_contains($key, "{$field}_")) {
                return true;
            }
        }

        return false;
    }

    private function featureSummary(mixed $items): string
    {
        if (! is_array($items) || $items === []) {
            return 'No cached feature payload';
        }

        $enabled = collect($items)->filter(fn (mixed $item): bool => is_array($item) && ($item['enabled'] ?? false) === true)->count();

        return "{$enabled} enabled / ".count($items).' cached';
    }

    private function syncSummary(mixed $sync): string
    {
        if (! is_array($sync)) {
            return 'Unavailable';
        }

        $pending = $sync['pending_actions'] ?? null;
        $failed = $sync['failed_actions'] ?? null;
        $conflicts = $sync['conflict_actions'] ?? null;

        return "Pending {$this->countLabel($pending)}, failed {$this->countLabel($failed)}, conflicts {$this->countLabel($conflicts)}";
    }

    private function countLabel(mixed $count): string
    {
        return is_int($count) ? (string) $count : 'unknown';
    }
}
