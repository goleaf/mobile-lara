<?php

namespace App\Services\MobileConfig;

use App\Services\MobileLocal\SettingsRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;

final class MobileRemoteConfigStore
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private const FOUNDATION_VALUES = [
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

    public function __construct(private readonly SettingsRepository $settings) {}

    /**
     * @return array<string, mixed>
     */
    public function snapshot(): array
    {
        $remoteConfig = $this->remoteConfigPayload();

        return [
            'version' => $this->stringFrom($remoteConfig['config_version'] ?? null)
                ?? $this->stringFrom($remoteConfig['version'] ?? null)
                ?? 'remote-config-foundation-1',
            'values' => $this->values(),
            'freshness' => $this->arrayFrom($remoteConfig['freshness'] ?? null),
            'compatibility' => $this->arrayFrom($remoteConfig['compatibility'] ?? null),
            'defaults_used' => $this->stringListFrom($remoteConfig['defaults_used'] ?? null),
            'support_context' => $this->arrayFrom($remoteConfig['support_context'] ?? null),
            'cached_at' => $this->cachedAt(),
            'source' => $this->hasCachedBootstrap() ? 'cached_bootstrap' : 'foundation_default',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function values(): array
    {
        $values = $this->arrayFrom($this->remoteConfigPayload()['values'] ?? null);

        $resolved = self::FOUNDATION_VALUES;

        foreach ($values as $key => $section) {
            if (! is_string($key) || ! is_array($section)) {
                continue;
            }

            $resolved[$key] = array_replace($resolved[$key] ?? [], $section);
        }

        ksort($resolved);

        return $resolved;
    }

    /**
     * @return array<string, mixed>
     */
    public function section(string $key): array
    {
        return $this->arrayFrom(Arr::get($this->values(), $this->normalizePath($key)));
    }

    public function supportUrl(): ?string
    {
        return $this->url('support.url');
    }

    /**
     * @return array{url: string|null, diagnostics_enabled: bool}
     */
    public function supportSettings(): array
    {
        return [
            'url' => $this->supportUrl(),
            'diagnostics_enabled' => $this->bool('support.diagnostics_enabled'),
        ];
    }

    /**
     * @return array{terms_url: string|null, privacy_url: string|null}
     */
    public function legalSettings(): array
    {
        return [
            'terms_url' => $this->url('legal.terms_url'),
            'privacy_url' => $this->url('legal.privacy_url'),
        ];
    }

    /**
     * @return array{manual_sync_enabled: bool, max_batch_size: int}
     */
    public function syncSettings(): array
    {
        return [
            'manual_sync_enabled' => $this->bool('sync.manual_sync_enabled'),
            'max_batch_size' => $this->positiveInt('sync.max_batch_size', 50),
        ];
    }

    /**
     * @return array{max_attachment_mb: int, allowed_mime_types: list<string>}
     */
    public function uploadSettings(): array
    {
        return [
            'max_attachment_mb' => $this->positiveInt('uploads.max_attachment_mb', 10),
            'allowed_mime_types' => $this->stringList('uploads.allowed_mime_types', self::FOUNDATION_VALUES['uploads']['allowed_mime_types']),
        ];
    }

    /**
     * @return array{pin_required: bool, biometric_allowed: bool}
     */
    public function appLockSettings(): array
    {
        return [
            'pin_required' => $this->bool('app_lock.pin_required'),
            'biometric_allowed' => $this->bool('app_lock.biometric_allowed', true),
        ];
    }

    /**
     * @return list<string>
     */
    public function dashboardWidgets(): array
    {
        return $this->stringList('dashboard.widgets', self::FOUNDATION_VALUES['dashboard']['widgets']);
    }

    public function bool(string $path, bool $default = false): bool
    {
        $value = Arr::get($this->values(), $this->normalizePath($path));

        return is_bool($value) ? $value : $default;
    }

    public function positiveInt(string $path, int $default): int
    {
        $value = Arr::get($this->values(), $this->normalizePath($path));

        if (! is_int($value) || $value < 1) {
            return $default;
        }

        return $value;
    }

    public function string(string $path, ?string $default = null): ?string
    {
        return $this->stringFrom(Arr::get($this->values(), $this->normalizePath($path))) ?? $default;
    }

    public function url(string $path, ?string $default = null): ?string
    {
        $url = $this->string($path, $default);

        if ($url === null || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return $default;
        }

        $scheme = str($url)->before('://')->lower()->toString();

        return in_array($scheme, ['http', 'https'], true) ? $url : $default;
    }

    /**
     * @param  list<string>  $default
     * @return list<string>
     */
    public function stringList(string $path, array $default = []): array
    {
        $values = $this->stringListFrom(Arr::get($this->values(), $this->normalizePath($path)));

        return $values === [] ? $default : $values;
    }

    private function hasCachedBootstrap(): bool
    {
        return $this->bootstrapData() !== [];
    }

    /**
     * @return array<string, mixed>
     */
    private function remoteConfigPayload(): array
    {
        $data = $this->bootstrapData();
        $payload = Arr::get($data, 'remote_config');

        return is_array($payload) ? $payload : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function bootstrapData(): array
    {
        try {
            $envelope = $this->settings->cachedBootstrapContext();
        } catch (QueryException $exception) {
            if ($this->isMissingSettingsTable($exception)) {
                return [];
            }

            throw $exception;
        }

        $data = is_array($envelope) ? ($envelope['data'] ?? null) : null;

        return is_array($data) ? $data : [];
    }

    private function cachedAt(): ?string
    {
        try {
            return $this->settings->bootstrapCachedAt()?->toIso8601String();
        } catch (QueryException $exception) {
            if ($this->isMissingSettingsTable($exception)) {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function arrayFrom(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    private function stringFrom(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    /**
     * @return list<string>
     */
    private function stringListFrom(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(
            array_map(fn (mixed $item): ?string => $this->stringFrom($item), $value),
            fn (?string $item): bool => $item !== null,
        ));
    }

    private function normalizePath(string $path): string
    {
        return str($path)->lower()->trim()->replace('-', '_')->toString();
    }

    private function isMissingSettingsTable(QueryException $exception): bool
    {
        $message = $exception->getMessage();

        return str_contains($message, 'mobile_local_settings')
            && (str_contains($message, 'no such table') || str_contains($message, 'Base table or view not found'));
    }
}
