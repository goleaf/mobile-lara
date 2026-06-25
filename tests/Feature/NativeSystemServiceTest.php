<?php

use App\Services\Native\SystemService;
use Native\Mobile\System;

test('native system service reports browser fallback when native runtime is inactive', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        $service = new SystemService(new NativeSystemServiceFakeSystem);

        expect($service->platform())->toBe('browser')
            ->and($service->platformLabel())->toBe('Browser fallback')
            ->and($service->isIos())->toBeFalse()
            ->and($service->isAndroid())->toBeFalse()
            ->and($service->isMobile())->toBeFalse()
            ->and($service->nativeRuntimeAvailable())->toBeFalse()
            ->and($service->openAppSettings())->toBe([
                'success' => false,
                'message' => 'Native app settings are unavailable in this browser runtime.',
            ]);
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('native system service exposes platform helpers and opens app settings', function (): void {
    config(['nativephp-internal.running' => true]);

    $system = new NativeSystemServiceFakeSystem(isIos: true, isMobile: true);
    $service = new SystemService($system);

    expect($service->platform())->toBe('ios')
        ->and($service->platformLabel())->toBe('iOS')
        ->and($service->isIos())->toBeTrue()
        ->and($service->isAndroid())->toBeFalse()
        ->and($service->isMobile())->toBeTrue()
        ->and($service->platformSettingsLabel())->toBe('Open iOS app settings')
        ->and($service->permissionRecoveryLinks())->toHaveCount(8)
        ->and($service->permissionRecoveryLinks()[0])->toMatchArray([
            'key' => 'camera',
            'label' => 'Camera',
            'recovery_label' => 'Open iOS app settings',
        ])
        ->and($service->openAppSettings())->toBe([
            'success' => true,
            'message' => 'Native app settings opened.',
        ])
        ->and($system->openedSettings)->toBeTrue();
});

final class NativeSystemServiceFakeSystem extends System
{
    public bool $openedSettings = false;

    public function __construct(
        private readonly bool $isIos = false,
        private readonly bool $isAndroid = false,
        private readonly bool $isMobile = false,
    ) {}

    public function isIos(): bool
    {
        return $this->isIos;
    }

    public function isAndroid(): bool
    {
        return $this->isAndroid;
    }

    public function isMobile(): bool
    {
        return $this->isMobile;
    }

    public function appSettings(): void
    {
        $this->openedSettings = true;
    }
}
