<?php

namespace App\Services\MobileAppState;

use App\Services\MobileLocal\SettingsRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;

final class MobileAppStateStore
{
    /**
     * @var list<string>
     */
    private const FORCE_UPDATE_STATES = [
        'force_update',
        'blocked',
        'internal_only',
        'stale_client',
    ];

    /**
     * @var list<string>
     */
    private const OPTIONAL_UPDATE_STATES = [
        'optional_update',
        'recommended_update',
        'deprecated',
    ];

    public function __construct(private readonly SettingsRepository $settings) {}

    /**
     * @return array{
     *     state: string,
     *     label: string,
     *     banner_title: string|null,
     *     message: string|null,
     *     force_update: bool,
     *     optional_update: bool,
     *     maintenance_enabled: bool,
     *     current_version: string,
     *     version_code: string,
     *     minimum_supported_version: string|null,
     *     minimum_recommended_version: string|null,
     *     latest_version: string|null,
     *     store_url: string|null,
     *     support_url: string|null,
     *     retry_after: int|null,
     *     retry_after_label: string|null,
     *     allowed_actions: list<string>,
     *     can_update: bool,
     *     can_retry: bool,
     *     can_support: bool,
     *     can_logout: bool,
     *     policy_source: string,
     *     policy_version: string|null,
     *     cached: bool
     * }
     */
    public function current(): array
    {
        $data = $this->bootstrapData();
        $appVersion = $this->arrayValue(Arr::get($data, 'app_version'));
        $maintenance = $this->arrayValue(Arr::get($data, 'maintenance'));
        $nestedMaintenance = $this->arrayValue(Arr::get($appVersion, 'maintenance'));
        $state = $this->stringValue(Arr::get($appVersion, 'state'))
            ?? $this->stringValue(Arr::get($appVersion, 'status'))
            ?? 'unknown';
        $maintenanceEnabled = $state === 'maintenance'
            || Arr::get($maintenance, 'enabled') === true
            || Arr::get($nestedMaintenance, 'enabled') === true;
        $forceUpdate = ! $maintenanceEnabled && (
            Arr::get($appVersion, 'force_update') === true
            || in_array($state, self::FORCE_UPDATE_STATES, true)
        );
        $optionalUpdate = ! $maintenanceEnabled && ! $forceUpdate && (
            Arr::get($appVersion, 'optional_update') === true
            || in_array($state, self::OPTIONAL_UPDATE_STATES, true)
        );
        $allowedActions = $this->allowedActions($appVersion, $forceUpdate, $optionalUpdate, $maintenanceEnabled);
        $retryAfter = $this->integerValue(Arr::get($maintenance, 'retry_after'))
            ?? $this->integerValue(Arr::get($nestedMaintenance, 'retry_after'))
            ?? $this->integerValue(Arr::get($appVersion, 'retry_after'));
        $storeUrl = $this->storeUrl($appVersion);
        $supportUrl = $this->stringValue(Arr::get($maintenance, 'support_url'))
            ?? $this->stringValue(Arr::get($nestedMaintenance, 'support_url'))
            ?? $this->stringValue(Arr::get($appVersion, 'support_url'));

        return [
            'state' => $state,
            'label' => $this->label($state),
            'banner_title' => $this->bannerTitle($forceUpdate, $optionalUpdate, $maintenanceEnabled),
            'message' => $this->message($appVersion, $maintenance, $nestedMaintenance, $forceUpdate, $optionalUpdate, $maintenanceEnabled),
            'force_update' => $forceUpdate,
            'optional_update' => $optionalUpdate,
            'maintenance_enabled' => $maintenanceEnabled,
            'current_version' => $this->stringValue(Arr::get($appVersion, 'reported_version')) ?? (string) config('nativephp.version', '1.0.0'),
            'version_code' => $this->stringValue(Arr::get($appVersion, 'reported_version_code')) ?? (string) config('nativephp.version_code', '1'),
            'minimum_supported_version' => $this->stringValue(Arr::get($appVersion, 'minimum_supported_version')),
            'minimum_recommended_version' => $this->stringValue(Arr::get($appVersion, 'minimum_recommended_version')),
            'latest_version' => $this->stringValue(Arr::get($appVersion, 'latest_version')),
            'store_url' => $storeUrl,
            'support_url' => $supportUrl,
            'retry_after' => $retryAfter,
            'retry_after_label' => $this->retryAfterLabel($retryAfter),
            'allowed_actions' => $allowedActions,
            'can_update' => $storeUrl !== null && in_array('update', $allowedActions, true),
            'can_retry' => in_array('retry', $allowedActions, true),
            'can_support' => $supportUrl !== null && in_array('support', $allowedActions, true),
            'can_logout' => Arr::get($appVersion, 'logout_allowed', true) !== false && in_array('logout', $allowedActions, true),
            'policy_source' => $this->stringValue(Arr::get($appVersion, 'policy_source')) ?? 'not_cached',
            'policy_version' => $this->stringValue(Arr::get($appVersion, 'policy_version')),
            'cached' => $data !== [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function bootstrapData(): array
    {
        try {
            $context = $this->settings->bootstrapContext();
        } catch (QueryException $exception) {
            if ($this->isMissingSettingsTable($exception)) {
                return [];
            }

            throw $exception;
        }

        return is_array($context['data'] ?? null) ? $context['data'] : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function arrayValue(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    private function stringValue(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function integerValue(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $value = (int) $value;

        return $value > 0 ? $value : null;
    }

    /**
     * @return list<string>
     */
    private function allowedActions(array $appVersion, bool $forceUpdate, bool $optionalUpdate, bool $maintenanceEnabled): array
    {
        $configured = collect($this->arrayValue(Arr::get($appVersion, 'allowed_actions')))
            ->filter(fn (mixed $action): bool => $this->stringValue($action) !== null)
            ->map(fn (mixed $action): string => (string) $this->stringValue($action))
            ->values()
            ->all();

        if ($configured !== []) {
            return $configured;
        }

        if ($maintenanceEnabled) {
            return ['retry', 'support', 'logout'];
        }

        if ($forceUpdate) {
            return ['update', 'support', 'logout'];
        }

        if ($optionalUpdate) {
            return ['continue', 'update', 'support', 'logout'];
        }

        return ['continue', 'support', 'logout'];
    }

    private function storeUrl(array $appVersion): ?string
    {
        $directUrl = $this->stringValue(Arr::get($appVersion, 'store_url'));

        if ($directUrl !== null) {
            return $directUrl;
        }

        $platform = $this->stringValue(Arr::get($appVersion, 'reported_platform'));
        $storeUrls = $this->arrayValue(Arr::get($appVersion, 'store_urls'));

        if ($platform !== null) {
            $platformUrl = $this->stringValue($storeUrls[$platform] ?? null);

            if ($platformUrl !== null) {
                return $platformUrl;
            }
        }

        foreach ($storeUrls as $url) {
            $url = $this->stringValue($url);

            if ($url !== null) {
                return $url;
            }
        }

        return null;
    }

    private function message(array $appVersion, array $maintenance, array $nestedMaintenance, bool $forceUpdate, bool $optionalUpdate, bool $maintenanceEnabled): ?string
    {
        if ($maintenanceEnabled) {
            return $this->stringValue(Arr::get($maintenance, 'message'))
                ?? $this->stringValue(Arr::get($nestedMaintenance, 'message'))
                ?? $this->stringValue(Arr::get($appVersion, 'message'))
                ?? 'The Admin/API control plane has placed the mobile app into maintenance mode.';
        }

        if ($forceUpdate) {
            return $this->stringValue(Arr::get($appVersion, 'message'))
                ?? 'Update this mobile app before continuing.';
        }

        if ($optionalUpdate) {
            return $this->stringValue(Arr::get($appVersion, 'message'))
                ?? 'A newer mobile app version is available.';
        }

        return $this->stringValue(Arr::get($appVersion, 'message'));
    }

    private function bannerTitle(bool $forceUpdate, bool $optionalUpdate, bool $maintenanceEnabled): ?string
    {
        if ($maintenanceEnabled) {
            return 'Maintenance mode';
        }

        if ($forceUpdate) {
            return 'Update required';
        }

        if ($optionalUpdate) {
            return 'App update available';
        }

        return null;
    }

    private function label(string $state): string
    {
        return str($state)
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    private function retryAfterLabel(?int $seconds): ?string
    {
        if ($seconds === null) {
            return null;
        }

        if ($seconds >= 3600) {
            $hours = (int) ceil($seconds / 3600);

            return $hours === 1 ? 'about 1 hour' : "about {$hours} hours";
        }

        if ($seconds >= 60) {
            $minutes = (int) ceil($seconds / 60);

            return $minutes === 1 ? 'about 1 minute' : "about {$minutes} minutes";
        }

        return "{$seconds} seconds";
    }

    private function isMissingSettingsTable(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($exception->getMessage(), 'mobile_local_settings')
            && (str_contains($message, 'no such table') || str_contains($message, 'base table or view not found'));
    }
}
