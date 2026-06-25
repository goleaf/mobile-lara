<?php

namespace App\Services\MobileLocal;

final class MobileNetworkStatus
{
    public function __construct(
        public readonly bool $isOnline,
        public readonly string $connectionType = 'unknown',
        public readonly ?bool $isMetered = null,
        public readonly ?bool $isConstrained = null,
        public readonly string $source = 'assumed',
        public readonly bool $nativeStatusAvailable = false,
        public readonly bool $fallbackCheckUsed = false,
        public readonly ?string $fallbackUrl = null,
    ) {}

    public function isOffline(): bool
    {
        return ! $this->isOnline;
    }

    public function stateLabel(): string
    {
        return $this->isOnline ? 'Online' : 'Offline';
    }

    public function variant(): string
    {
        return $this->isOnline ? 'success' : 'warning';
    }

    public function connectionTypeLabel(): string
    {
        return match ($this->connectionType) {
            'wifi' => 'Wi-Fi',
            'cellular' => 'Cellular',
            'ethernet' => 'Ethernet',
            'none' => 'None',
            'unknown' => 'Unknown',
            default => ucfirst(str_replace(['_', '-'], ' ', $this->connectionType)),
        };
    }

    public function meteredLabel(): string
    {
        return match ($this->isMetered) {
            true => 'Metered',
            false => 'Unmetered',
            null => 'Unknown',
        };
    }

    public function constrainedLabel(): string
    {
        return match ($this->isConstrained) {
            true => 'Low data mode',
            false => 'Unconstrained',
            null => 'Unknown',
        };
    }

    public function sourceLabel(): string
    {
        return match ($this->source) {
            'nativephp' => 'NativePHP',
            'fallback' => 'Fallback check',
            'nativephp+fallback' => 'NativePHP + fallback check',
            'assumed' => 'Assumed online',
            default => ucfirst(str_replace(['_', '-'], ' ', $this->source)),
        };
    }

    public function summary(): string
    {
        return $this->stateLabel().' / '.$this->connectionTypeLabel().' / '.$this->meteredLabel();
    }
}
