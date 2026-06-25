<?php

use App\Services\Native\DeviceService;
use Native\Mobile\Device;

test('native device service formats device and battery payloads', function (): void {
    config([
        'nativephp-internal.running' => true,
        'nativephp.version' => '2.4.6',
    ]);

    $service = new DeviceService(new NativeDeviceServiceFakeDevice(
        info: json_encode([
            'info' => json_encode([
                'model' => 'iPhone15,3',
                'platform' => 'ios',
                'osVersion' => '18.5',
            ]),
        ]),
        batteryInfo: json_encode([
            'info' => json_encode([
                'batteryLevel' => 0.66,
                'isCharging' => true,
            ]),
        ]),
        vibrationSuccess: true,
        flashlightResult: [
            'success' => true,
            'state' => true,
        ],
    ));

    expect($service->deviceModel())->toBe('iPhone15,3')
        ->and($service->osVersion())->toBe('iOS 18.5')
        ->and($service->appVersion())->toBe('2.4.6')
        ->and($service->batteryStatus())->toBe('66%')
        ->and($service->chargingStatus())->toBe('Charging')
        ->and($service->toggleFlashlight())->toBe([
            'success' => true,
            'enabled' => true,
            'message' => 'Flashlight enabled.',
        ])
        ->and($service->vibrate())->toBe([
            'success' => true,
            'message' => 'Native vibration triggered.',
        ])
        ->and($service->hapticFeedback())->toBe([
            'success' => true,
            'message' => 'Native haptic feedback triggered.',
        ]);
});

test('native device service reports browser fallback when native runtime is inactive', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        $service = new DeviceService(new NativeDeviceServiceFakeDevice);

        expect($service->deviceModel())->toBe('Unavailable outside NativePHP runtime.')
            ->and($service->osVersion())->toBe('Unavailable outside NativePHP runtime.')
            ->and($service->batteryStatus())->toBe('Unavailable outside NativePHP runtime.')
            ->and($service->chargingStatus())->toBe('Unavailable outside NativePHP runtime.')
            ->and($service->toggleFlashlight())->toBe([
                'success' => false,
                'enabled' => false,
                'message' => 'Flashlight is unavailable outside NativePHP runtime.',
            ])
            ->and($service->vibrate())->toBe([
                'success' => false,
                'message' => 'Vibration is unavailable outside NativePHP runtime.',
            ])
            ->and($service->hapticFeedback())->toBe([
                'success' => false,
                'message' => 'Haptic feedback is unavailable outside NativePHP runtime.',
            ]);
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

final class NativeDeviceServiceFakeDevice extends Device
{
    /**
     * @param  array{success?: bool, state?: bool}  $flashlightResult
     */
    public function __construct(
        private readonly ?string $info = null,
        private readonly ?string $batteryInfo = null,
        private readonly bool $vibrationSuccess = false,
        private readonly array $flashlightResult = [
            'success' => false,
            'state' => false,
        ],
    ) {}

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function getBatteryInfo(): ?string
    {
        return $this->batteryInfo;
    }

    public function vibrate(): bool
    {
        return $this->vibrationSuccess;
    }

    public function flashlight(): array
    {
        return $this->flashlightResult;
    }
}
