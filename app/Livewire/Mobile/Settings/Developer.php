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
            'description' => 'Open NativePHP platform helpers and permission recovery actions.',
            'route' => 'mobile.settings.permissions',
            'badge' => 'Live',
        ],
        [
            'label' => 'Media capture',
            'description' => 'Open NativePHP camera, video recorder, and gallery picker checks.',
            'route' => 'mobile.media.capture',
            'badge' => 'Media',
        ],
        [
            'label' => 'Media gallery',
            'description' => 'Review local media_items records and sync status on this device.',
            'route' => 'mobile.media.gallery',
            'badge' => 'Local',
        ],
        [
            'label' => 'Voice notes',
            'description' => 'Record, save, delete, and queue microphone upload placeholders.',
            'route' => 'mobile.voice-notes',
            'badge' => 'Audio',
        ],
        [
            'label' => 'File manager',
            'description' => 'Read, write, import, export, share, copy, move, and delete local app files.',
            'route' => 'mobile.files',
            'badge' => 'Files',
        ],
    ];
}
