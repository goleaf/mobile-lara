<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Support settings')]
final class Support extends SettingsSectionPage
{
    protected const TITLE = 'Support settings';

    protected const DESCRIPTION = 'Prepare help, diagnostics, contact, and troubleshooting entry points.';

    protected const STATUS = 'This is a placeholder route for support workflows.';

    protected const ITEMS = [
        [
            'label' => 'Help center',
            'description' => 'Placeholder for searchable help articles and mobile FAQs.',
            'badge' => 'Next',
        ],
        [
            'label' => 'Contact support',
            'description' => 'Placeholder for message, logs, and ticket submission.',
            'badge' => 'Next',
        ],
        [
            'label' => 'Diagnostics',
            'description' => 'Open developer diagnostics while support export is pending.',
            'route' => 'mobile.debug',
            'badge' => 'Live',
        ],
    ];
}
