<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Storage settings')]
final class Storage extends SettingsSectionPage
{
    protected const TITLE = 'Storage settings';

    protected const DESCRIPTION = 'Prepare cache usage, offline payloads, secure values, and local cleanup.';

    protected const STATUS = 'This is a placeholder route for storage controls.';

    protected const ITEMS = [
        [
            'label' => 'Offline cache',
            'description' => 'Placeholder for cached screens, queued writes, and local payload limits.',
            'badge' => 'Next',
        ],
        [
            'label' => 'Secure auth values',
            'description' => 'Placeholder for token, user id, and expiry storage visibility.',
            'badge' => 'Secure',
        ],
        [
            'label' => 'Clear local data',
            'description' => 'Placeholder for clearing cache without deleting the account.',
            'badge' => 'Next',
        ],
    ];
}
