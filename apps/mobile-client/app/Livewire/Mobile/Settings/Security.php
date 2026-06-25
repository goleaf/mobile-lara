<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Security settings')]
final class Security extends SettingsSectionPage
{
    protected const TITLE = 'Security settings';

    protected const DESCRIPTION = 'Control local unlock methods, protected sessions, and device security.';

    protected const STATUS = 'PIN, biometric unlock, and session pages are connected.';

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
