<?php

use App\Livewire\Mobile\Settings\Permissions;
use App\Services\Native\SystemService;
use Livewire\Livewire;
use Native\Mobile\System;

test('permissions settings screen renders platform helpers and recovery links', function (): void {
    Livewire::test(Permissions::class)
        ->assertSee('Permission settings')
        ->assertSee('Platform')
        ->assertSee('Browser fallback')
        ->assertSee('System::isIos()')
        ->assertSee('System::isAndroid()')
        ->assertSee('System::isMobile()')
        ->assertSee('Permission recovery')
        ->assertSee('Camera')
        ->assertSee('Photos and gallery')
        ->assertSee('Microphone')
        ->assertSee('Location')
        ->assertSee('Notifications')
        ->assertSee('Biometrics')
        ->assertSee('Files and storage')
        ->assertSee('Scanner')
        ->assertSee('Developer debug')
        ->assertSee(route('mobile.debug'), false)
        ->assertSee(route('mobile.settings'), false);
});

test('permissions settings screen reports browser fallback for app settings', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        Livewire::test(Permissions::class)
            ->call('openAppSettings', 'camera')
            ->assertSet('lastRecoveryTarget', 'Camera')
            ->assertSet('settingsError', 'Native app settings are unavailable in this browser runtime. Recovery target: Camera.')
            ->assertSee('Native app settings are unavailable in this browser runtime. Recovery target: Camera.')
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'warning'
                    && ($params['title'] ?? null) === 'Native settings unavailable';
            });
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('permissions settings screen opens native app settings through the system service', function (): void {
    config(['nativephp-internal.running' => true]);

    $system = new MobilePermissionsSettingsFakeSystem(isAndroid: true, isMobile: true);
    $this->app->instance(SystemService::class, new SystemService($system));

    Livewire::test(Permissions::class)
        ->assertSee('Android')
        ->assertSee('Open Android app settings')
        ->call('openAppSettings', 'microphone')
        ->assertSet('lastRecoveryTarget', 'Microphone')
        ->assertSet('settingsStatus', 'Native app settings opened. Recovery target: Microphone.')
        ->assertSee('Native app settings opened. Recovery target: Microphone.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Settings opened';
        });

    expect($system->openedSettings)->toBeTrue();
});

final class MobilePermissionsSettingsFakeSystem extends System
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
