<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Sync settings')]
final class Sync extends SettingsSectionPage
{
    protected const TITLE = 'Sync settings';

    protected const DESCRIPTION = 'Prepare background sync, retry behavior, conflicts, and offline state.';

    protected const STATUS = 'Dashboard sync indicators exist; detailed sync controls are placeholders.';

    protected const ITEMS = [
        [
            'label' => 'Dashboard sync status',
            'description' => 'Open the dashboard sync and offline status preview.',
            'route' => 'mobile.dashboard',
            'badge' => 'Live',
        ],
        [
            'label' => 'Pending queue',
            'description' => 'Placeholder for queued mobile writes and retry attempts.',
            'badge' => 'Next',
        ],
        [
            'label' => 'Conflict handling',
            'description' => 'Placeholder for merge review and server reconciliation.',
            'badge' => 'Next',
        ],
    ];
}
