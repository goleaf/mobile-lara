<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Developer settings')]
final class Developer extends SettingsSectionPage
{
    protected const TITLE = 'Developer settings';

    protected const DESCRIPTION = 'Open diagnostics, runtime state, NativePHP checks, and debug-only tools.';

    protected const STATUS = 'Debug and Tailwind check routes are available.';

    protected const ITEMS = [
        [
            'label' => 'Mobile debug',
            'description' => 'Open runtime details, NativePHP dialog checks, and Livewire toast examples.',
            'route' => 'mobile.debug',
            'badge' => 'Live',
        ],
        [
            'label' => 'Tailwind check',
            'description' => 'Open the Blade and Tailwind asset verification route.',
            'route' => 'dev.tailwind',
            'badge' => 'Dev',
        ],
        [
            'label' => 'Native permissions',
            'description' => 'Placeholder for future NativePHP permission diagnostics.',
            'route' => 'mobile.settings.permissions',
            'badge' => 'Next',
        ],
    ];
}
