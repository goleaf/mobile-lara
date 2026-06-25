<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Permission settings')]
final class Permissions extends SettingsSectionPage
{
    protected const TITLE = 'Permission settings';

    protected const DESCRIPTION = 'Prepare camera, location, microphone, file, biometrics, and scanner permission states.';

    protected const STATUS = 'This is a placeholder route for NativePHP permission controls.';

    protected const ITEMS = [
        [
            'label' => 'Native device permissions',
            'description' => 'Placeholder for camera, location, microphone, files, scanner, and network checks.',
            'badge' => 'Next',
        ],
        [
            'label' => 'Biometric capability',
            'description' => 'Placeholder for device biometric availability and permission status.',
            'badge' => 'Secure',
        ],
        [
            'label' => 'Debug diagnostics',
            'description' => 'Open the debug screen for runtime and NativePHP checks.',
            'route' => 'mobile.debug',
            'badge' => 'Live',
        ],
    ];
}
