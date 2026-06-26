<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Appearance settings')]
final class Appearance extends SettingsSectionPage
{
    protected const TITLE = 'Appearance settings';

    protected const DESCRIPTION = 'Prepare spacing, text scale, and mobile display preferences.';

    protected const STATUS = 'The mobile interface is light-only. Theme switching is disabled.';

    protected const ITEMS = [
        [
            'label' => 'Light interface',
            'description' => 'The app uses the light design system on every device.',
            'badge' => 'Active',
        ],
        [
            'label' => 'Compact layout',
            'description' => 'Placeholder for denser cards, smaller rows, and thumb-friendly spacing.',
            'badge' => 'Next',
        ],
        [
            'label' => 'Text size',
            'description' => 'Placeholder for accessible text scale and line-height preferences.',
            'badge' => 'Next',
        ],
    ];
}
