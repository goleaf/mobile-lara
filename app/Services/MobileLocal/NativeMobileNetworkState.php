<?php

namespace App\Services\MobileLocal;

use App\Contracts\MobileLocal\MobileNetworkState;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Native\Mobile\Network;
use Throwable;

final class NativeMobileNetworkState implements MobileNetworkState
{
    public function __construct(
        private readonly Network $network,
    ) {}

    public function isAvailable(): bool
    {
        return $this->status()->isOnline;
    }

    public function status(): MobileNetworkStatus
    {
        $nativeStatus = $this->nativeStatus();
        $nativeConnectionAvailable = $this->nativeConnectionAvailable($nativeStatus);

        if ($nativeConnectionAvailable === false) {
            return $this->makeStatus(
                isOnline: false,
                nativeStatus: $nativeStatus,
                fallbackStatus: null,
                source: 'nativephp',
            );
        }

        $fallbackStatus = null;

        if ($this->fallbackCheckEnabled()) {
            $fallbackStatus = $this->fallbackConnectionAvailable();
        }

        $isOnline = $fallbackStatus !== null
            ? ($nativeConnectionAvailable ?? true) && $fallbackStatus['is_online']
            : ($nativeConnectionAvailable ?? true);

        return $this->makeStatus(
            isOnline: $isOnline,
            nativeStatus: $nativeStatus,
            fallbackStatus: $fallbackStatus,
            source: $this->source($nativeStatus, $fallbackStatus),
        );
    }

    private function nativeStatus(): ?object
    {
        try {
            return $this->network->status();
        } catch (Throwable) {
            return null;
        }
    }

    private function nativeConnectionAvailable(?object $status): ?bool
    {
        if (! is_object($status) || ! property_exists($status, 'connected')) {
            return null;
        }

        return (bool) $status->connected;
    }

    /**
     * @return array{is_online: bool, url: string|null}
     */
    private function fallbackConnectionAvailable(): array
    {
        $url = trim((string) config('mobile_local.network.fallback_check.url', config('app.url')));

        if ($url === '') {
            return [
                'is_online' => true,
                'url' => null,
            ];
        }

        try {
            $response = Http::timeout($this->configInt('timeout_seconds', 2, 1, 30))
                ->connectTimeout($this->configInt('connect_timeout_seconds', 1, 1, 30))
                ->acceptJson()
                ->get($url);

            return [
                'is_online' => $response->successful() || $response->redirect(),
                'url' => $url,
            ];
        } catch (ConnectionException) {
            return [
                'is_online' => false,
                'url' => $url,
            ];
        } catch (Throwable) {
            return [
                'is_online' => false,
                'url' => $url,
            ];
        }
    }

    private function fallbackCheckEnabled(): bool
    {
        return (bool) config('mobile_local.network.fallback_check.enabled', false);
    }

    private function configInt(string $key, int $default, int $min, int $max): int
    {
        $value = (int) config("mobile_local.network.fallback_check.{$key}", $default);

        return max($min, min($value, $max));
    }

    /**
     * @param  array{is_online: bool, url: string|null}|null  $fallbackStatus
     */
    private function makeStatus(
        bool $isOnline,
        ?object $nativeStatus,
        ?array $fallbackStatus,
        string $source,
    ): MobileNetworkStatus {
        return new MobileNetworkStatus(
            isOnline: $isOnline,
            connectionType: $this->connectionType($nativeStatus),
            isMetered: $this->boolProperty($nativeStatus, 'isExpensive'),
            isConstrained: $this->boolProperty($nativeStatus, 'isConstrained'),
            source: $source,
            nativeStatusAvailable: is_object($nativeStatus),
            fallbackCheckUsed: $fallbackStatus !== null,
            fallbackUrl: $fallbackStatus['url'] ?? null,
        );
    }

    private function connectionType(?object $status): string
    {
        if (! is_object($status) || ! property_exists($status, 'type') || ! is_string($status->type)) {
            return 'unknown';
        }

        $type = trim(mb_strtolower($status->type));

        return $type === '' ? 'unknown' : $type;
    }

    private function boolProperty(?object $status, string $property): ?bool
    {
        if (! is_object($status) || ! property_exists($status, $property)) {
            return null;
        }

        return (bool) $status->{$property};
    }

    /**
     * @param  array{is_online: bool, url: string|null}|null  $fallbackStatus
     */
    private function source(?object $nativeStatus, ?array $fallbackStatus): string
    {
        if (is_object($nativeStatus) && $fallbackStatus !== null) {
            return 'nativephp+fallback';
        }

        if (is_object($nativeStatus)) {
            return 'nativephp';
        }

        if ($fallbackStatus !== null) {
            return 'fallback';
        }

        return 'assumed';
    }
}
