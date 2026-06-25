<?php

namespace App\Services\Native;

use Native\Mobile\System;
use Throwable;

final class SystemService
{
    /**
     * @var list<array{key: string, label: string, description: string, permission: string, badge: string, platform_note: string}>
     */
    private const PERMISSION_RECOVERY_LINKS = [
        [
            'key' => 'camera',
            'label' => 'Camera',
            'description' => 'Profile photos, document capture, and scanner previews.',
            'permission' => 'camera',
            'badge' => 'Media',
            'platform_note' => 'Recover camera access from this app\'s OS settings screen.',
        ],
        [
            'key' => 'photos',
            'label' => 'Photos and gallery',
            'description' => 'Avatar selection, image previews, and saved media access.',
            'permission' => 'photos',
            'badge' => 'Media',
            'platform_note' => 'iOS may show limited library access; Android may split images and video.',
        ],
        [
            'key' => 'microphone',
            'label' => 'Microphone',
            'description' => 'Voice notes, recording flows, and future audio capture.',
            'permission' => 'microphone',
            'badge' => 'Audio',
            'platform_note' => 'Recover microphone access from app settings after a denial.',
        ],
        [
            'key' => 'location',
            'label' => 'Location',
            'description' => 'Nearby search, local context, and future geolocation features.',
            'permission' => 'locationWhenInUse',
            'badge' => 'Location',
            'platform_note' => 'Location permissions often require app settings after repeated denial.',
        ],
        [
            'key' => 'notifications',
            'label' => 'Notifications',
            'description' => 'Push enrollment, local reminders, and notification previews.',
            'permission' => 'notification',
            'badge' => 'Alerts',
            'platform_note' => 'Open app settings to restore notification delivery.',
        ],
        [
            'key' => 'biometrics',
            'label' => 'Biometrics',
            'description' => 'Face ID, Touch ID, fingerprint, and protected unlock confirmation.',
            'permission' => 'biometrics',
            'badge' => 'Secure',
            'platform_note' => 'Biometric availability is controlled by device security settings.',
        ],
        [
            'key' => 'files',
            'label' => 'Files and storage',
            'description' => 'Exports, imports, cache review, and local data recovery.',
            'permission' => 'storage',
            'badge' => 'Storage',
            'platform_note' => 'File access varies by platform and selected document provider.',
        ],
        [
            'key' => 'scanner',
            'label' => 'Scanner',
            'description' => 'Barcode and QR scanner flows that depend on camera access.',
            'permission' => 'camera',
            'badge' => 'Camera',
            'platform_note' => 'Scanner recovery follows the same camera permission path.',
        ],
    ];

    public function __construct(
        private readonly System $system,
    ) {}

    public function platform(): string
    {
        if (! $this->nativeBridgeIsAvailable()) {
            return 'browser';
        }

        if ($this->isIos()) {
            return 'ios';
        }

        if ($this->isAndroid()) {
            return 'android';
        }

        return $this->isMobile() ? 'mobile' : 'native';
    }

    public function platformLabel(): string
    {
        return match ($this->platform()) {
            'ios' => 'iOS',
            'android' => 'Android',
            'mobile' => 'Native mobile',
            'native' => 'Native runtime',
            default => 'Browser fallback',
        };
    }

    public function isIos(): bool
    {
        return $this->nativeBool('isIos');
    }

    public function isAndroid(): bool
    {
        return $this->nativeBool('isAndroid');
    }

    public function isMobile(): bool
    {
        return $this->nativeBool('isMobile');
    }

    public function nativeRuntimeAvailable(): bool
    {
        return $this->nativeBridgeIsAvailable();
    }

    public function platformSettingsLabel(): string
    {
        return match ($this->platform()) {
            'ios' => 'Open iOS app settings',
            'android' => 'Open Android app settings',
            'mobile', 'native' => 'Open native app settings',
            default => 'Open app settings',
        };
    }

    public function permissionRecoveryDescription(): string
    {
        return match ($this->platform()) {
            'ios' => 'Opens the iOS Settings page for this app so denied permissions can be restored.',
            'android' => 'Opens the Android app-details settings page so denied permissions can be restored.',
            'mobile', 'native' => 'Opens this app\'s native settings page for permission recovery.',
            default => 'Available when the app is running inside NativePHP or a connected Jump Bridge session.',
        };
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function openAppSettings(): array
    {
        if (! $this->nativeBridgeIsAvailable()) {
            return [
                'success' => false,
                'message' => 'Native app settings are unavailable in this browser runtime.',
            ];
        }

        try {
            if (method_exists($this->system, 'openAppSettings')) {
                $this->system->openAppSettings();
            } else {
                $this->system->appSettings();
            }
        } catch (Throwable) {
            return [
                'success' => false,
                'message' => 'Unable to open native app settings.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Native app settings opened.',
        ];
    }

    /**
     * @return list<array{key: string, label: string, value: string}>
     */
    public function platformHelperRows(): array
    {
        return [
            [
                'key' => 'platform',
                'label' => 'Platform',
                'value' => $this->platformLabel(),
            ],
            [
                'key' => 'native-runtime',
                'label' => 'Native runtime',
                'value' => $this->nativeRuntimeAvailable() ? 'Available' : 'Browser fallback',
            ],
            [
                'key' => 'ios-helper',
                'label' => 'System::isIos()',
                'value' => $this->boolLabel($this->isIos()),
            ],
            [
                'key' => 'android-helper',
                'label' => 'System::isAndroid()',
                'value' => $this->boolLabel($this->isAndroid()),
            ],
            [
                'key' => 'mobile-helper',
                'label' => 'System::isMobile()',
                'value' => $this->boolLabel($this->isMobile()),
            ],
            [
                'key' => 'settings-bridge',
                'label' => 'Settings bridge',
                'value' => 'System.OpenAppSettings',
            ],
        ];
    }

    /**
     * @return list<array{key: string, label: string, description: string, permission: string, badge: string, platform_note: string, recovery_label: string, recovery_description: string}>
     */
    public function permissionRecoveryLinks(): array
    {
        return array_map(
            fn (array $link): array => [
                ...$link,
                'recovery_label' => $this->platformSettingsLabel(),
                'recovery_description' => $this->permissionRecoveryDescription(),
            ],
            self::PERMISSION_RECOVERY_LINKS,
        );
    }

    /**
     * @return array{platform: string, platform_label: string, native_runtime_available: bool, recovery_label: string, recovery_description: string}
     */
    public function snapshot(): array
    {
        return [
            'platform' => $this->platform(),
            'platform_label' => $this->platformLabel(),
            'native_runtime_available' => $this->nativeRuntimeAvailable(),
            'recovery_label' => $this->platformSettingsLabel(),
            'recovery_description' => $this->permissionRecoveryDescription(),
        ];
    }

    private function nativeBool(string $method): bool
    {
        if (! $this->nativeBridgeIsAvailable() || ! method_exists($this->system, $method)) {
            return false;
        }

        try {
            return (bool) $this->system->{$method}();
        } catch (Throwable) {
            return false;
        }
    }

    private function nativeBridgeIsAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    private function boolLabel(bool $value): string
    {
        return $value ? 'Yes' : 'No';
    }
}
