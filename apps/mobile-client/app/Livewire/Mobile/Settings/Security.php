<?php

namespace App\Livewire\Mobile\Settings;

use App\Services\MobileConfig\MobileRemoteConfigStore;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;

#[Title('Security settings')]
final class Security extends SettingsSectionPage
{
    protected const TITLE = 'Security settings';

    protected const DESCRIPTION = 'Control local unlock methods, protected sessions, and device security.';

    protected const STATUS = 'PIN, biometric unlock, and session pages are connected.';

    private MobileRemoteConfigStore $remoteConfig;

    public function boot(MobileRemoteConfigStore $remoteConfig): void
    {
        $this->remoteConfig = $remoteConfig;
    }

    public function render(): View
    {
        $appLockConfig = $this->remoteConfig->appLockSettings();

        return view('livewire.mobile.settings.section', [
            'sectionTitle' => self::TITLE,
            'sectionDescription' => self::DESCRIPTION,
            'sectionStatus' => $appLockConfig['pin_required']
                ? 'Admin/API config currently requires a local PIN for protected mobile access.'
                : 'PIN, biometric unlock, and session pages are connected.',
            'sectionItems' => $this->sectionItems(),
        ]);
    }

    /**
     * @return list<array{key: string, label: string, description: string, url: string|null, badge: string|null}>
     */
    protected function sectionItems(): array
    {
        $appLockConfig = $this->remoteConfig->appLockSettings();

        return [
            ...parent::sectionItems(),
            [
                'key' => 'admin-api-app-lock-policy',
                'label' => 'Admin/API app lock policy',
                'description' => $appLockConfig['pin_required']
                    ? ($appLockConfig['biometric_allowed']
                        ? 'A local PIN is required; biometric unlock remains allowed where the device supports it.'
                        : 'A local PIN is required; biometric unlock is disabled by cached config.')
                    : ($appLockConfig['biometric_allowed']
                        ? 'A local PIN is optional; biometric unlock remains allowed where the device supports it.'
                        : 'A local PIN is optional; biometric unlock is disabled by cached config.'),
                'url' => null,
                'badge' => 'Config',
            ],
        ];
    }

    protected const ITEMS = [
        [
            'label' => 'App unlock',
            'description' => 'Open the protected app unlock flow.',
            'route' => 'mobile.unlock',
            'badge' => 'Protected',
        ],
        [
            'label' => 'Create PIN',
            'description' => 'Create a local PIN fallback for device unlock.',
            'route' => 'mobile.pin.create',
            'badge' => 'PIN',
        ],
        [
            'label' => 'Change PIN',
            'description' => 'Change the local PIN when one exists on this device.',
            'route' => 'mobile.pin.change',
            'badge' => 'PIN',
        ],
        [
            'label' => 'Remove PIN',
            'description' => 'Remove the local PIN after confirmation.',
            'route' => 'mobile.pin.remove',
            'badge' => 'PIN',
        ],
    ];
}
