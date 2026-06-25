<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Appearance settings')]
final class Appearance extends SettingsSectionPage
{
    protected const TITLE = 'Appearance settings';

    protected const DESCRIPTION = 'Prepare theme, spacing, text scale, and mobile display preferences.';

    protected const STATUS = 'This is a placeholder route for appearance controls.';

    protected const ITEMS = [
        [
            'label' => 'Theme mode',
            'description' => 'Placeholder for system, light, and dark theme selection.',
            'badge' => 'Next',
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
